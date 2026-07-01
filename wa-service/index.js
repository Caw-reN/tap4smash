'use strict'

require('dotenv').config()

const {
    default: makeWASocket,
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestBaileysVersion,
    isJidBroadcast,
    makeCacheableSignalKeyStore,
} = require('@whiskeysockets/baileys')
const { Boom }  = require('@hapi/boom')
const pino      = require('pino')
const http      = require('http')
const fs        = require('fs')
const qrcode    = require('qrcode-terminal')
const QRCode    = require('qrcode')

// ─── Config ───────────────────────────────────────────────────────────────────
const PORT    = parseInt(process.env.WA_SERVICE_PORT  || '3001', 10)
const API_KEY = process.env.WA_SERVICE_API_KEY || 'tap4smash_wa_secret_2025'

// ─── State ────────────────────────────────────────────────────────────────────
let waSocket         = null
let isConnected      = false
let currentQRString  = null
let connectedNumber  = null

// ─── Logger (silent — hanya console.log manual yang tampil) ──────────────────
const logger = pino({ level: 'silent' })

// ─── Message Store (FIX utama "menunggu pesan") ───────────────────────────────
// Baileys membutuhkan implementasi getMessage agar bisa menjawab permintaan
// re-enkripsi dari WhatsApp ketika penerima restart/ganti HP.
// makeInMemoryStore sudah dihapus dari Baileys, jadi kita buat sendiri:
// Map sederhana dengan batas 200 pesan/chat agar memory tidak bocor.
const msgStore      = new Map()  // Map<jid, Map<msgId, messageObject>>
const MAX_PER_CHAT  = 200

const store = {
    /** Simpan pesan ke store */
    upsert(messages) {
        for (const msg of messages) {
            const jid = msg.key?.remoteJid
            const id  = msg.key?.id
            if (!jid || !id) continue
            if (!msgStore.has(jid)) msgStore.set(jid, new Map())
            const chat = msgStore.get(jid)
            chat.set(id, msg)
            // Buang entri paling lama jika melebihi batas
            if (chat.size > MAX_PER_CHAT) {
                chat.delete(chat.keys().next().value)
            }
        }
    },
    /** Ambil pesan dari store (dipanggil oleh getMessage) */
    loadMessage(jid, id) {
        return msgStore.get(jid)?.get(id)
    },
}

// ─── Safety Net: tangkap semua error async yang tidak ter-catch ───────────────
// libsignal kadang lempar "Failed to decrypt" sebagai unhandled rejection.
// Ini BUKAN crash fatal — cukup log lalu lanjut.
process.on('unhandledRejection', (reason) => {
    const msg = reason?.message || String(reason)
    // Filter error decrypt dari libsignal — tidak perlu ditampilkan ke user
    if (msg.includes('decrypt') || msg.includes('No matching session')) {
        console.warn('[WA] ⚠️  Decrypt warning (diabaikan):', msg.slice(0, 80))
        return
    }
    console.error('[WA] Unhandled rejection:', msg)
})

process.on('uncaughtException', (err) => {
    console.error('[WA] Uncaught exception (service tetap jalan):', err.message)
})

// ─── Baileys: Connect ke WhatsApp ─────────────────────────────────────────────
async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('./auth_info')
    const { version }          = await fetchLatestBaileysVersion()

    console.log(`\n[WA] Menggunakan Baileys versi protokol WA: ${version.join('.')}`)

    const sock = makeWASocket({
        version,
        auth: {
            creds: state.creds,
            // makeCacheableSignalKeyStore: cache key Signal di memory
            // → lebih stabil saat banyak pesan masuk bersamaan
            keys: makeCacheableSignalKeyStore(state.keys, logger),
        },
        printQRInTerminal: false,
        logger,
        shouldIgnoreJid:   jid => isJidBroadcast(jid),
        // ✔ FIX "menunggu pesan": suplai getMessage ke Baileys.
        // Ketika WA server minta ulang key enkripsi suatu pesan,
        // Baileys memanggil fungsi ini. Jika kita bisa kembalikan
        // message-nya → pesan berhasil terkirim ulang & tidak "menunggu".
        getMessage: async (key) => {
            const msg = await store.loadMessage(key.remoteJid, key.id)
            return msg?.message || undefined
        },
    })

    // Ikat store ke socket — simpan semua pesan masuk/keluar
    sock.ev.on('messages.upsert', ({ messages }) => store.upsert(messages))

    // ── Event: update koneksi ──────────────────────────────────────────────────
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update

        if (qr) {
            currentQRString = qr
            console.log('\n📱 Scan QR berikut dengan WhatsApp di HP kamu (Settings > Linked Devices):')
            console.log('   Atau buka dashboard admin → Menu WhatsApp untuk scan via browser.\n')
            qrcode.generate(qr, { small: true })
            console.log('\n⏳ Menunggu scan...')
        }

        if (connection === 'close') {
            isConnected     = false
            waSocket        = null
            connectedNumber = null

            const statusCode = new Boom(lastDisconnect?.error)?.output?.statusCode
            const reason     = Object.keys(DisconnectReason).find(
                k => DisconnectReason[k] === statusCode
            )

            console.log(`[WA] Koneksi terputus. Alasan: ${reason || statusCode}`)

            if (statusCode === DisconnectReason.loggedOut) {
                currentQRString = null
                console.log('[WA] ❌ Logged out! Menghapus auth_info/ dan merestart...')
                try { fs.rmSync('./auth_info', { recursive: true, force: true }) } catch (e) {}
                setTimeout(() => {
                    const now = new Date()
                    fs.utimesSync(__filename, now, now)
                }, 1000)
            }

            const delay = statusCode === DisconnectReason.restartRequired ? 1000 : 5000
            console.log(`[WA] 🔄 Mencoba reconnect dalam ${delay / 1000} detik...`)
            setTimeout(connectToWhatsApp, delay)
        }

        if (connection === 'open') {
            isConnected     = true
            waSocket        = sock
            currentQRString = null
            connectedNumber = sock.user?.id?.split(':')[0] ?? null
            console.log(`\n✅ [WA] Berhasil terhubung! Nomor aktif: ${connectedNumber}\n`)
        }
    })

    // ── Simpan kredensial setiap ada perubahan ────────────────────────────────
    sock.ev.on('creds.update', saveCreds)
}

// ─── Kirim pesan WhatsApp ─────────────────────────────────────────────────────
async function kirimPesan(phone, message) {
    if (!isConnected || !waSocket) {
        throw new Error('WhatsApp belum terhubung ke server.')
    }

    const cleanPhone = phone.replace(/\D/g, '')
    const jid        = `${cleanPhone}@s.whatsapp.net`

    await waSocket.sendMessage(jid, { text: message })
    return true
}

// ─── HTTP Server ──────────────────────────────────────────────────────────────
const server = http.createServer((req, res) => {
    res.setHeader('Content-Type', 'application/json; charset=utf-8')

    // Auth API Key
    if (req.headers['x-api-key'] !== API_KEY) {
        res.writeHead(401)
        res.end(JSON.stringify({ success: false, message: 'Unauthorized: API Key tidak valid.' }))
        return
    }

    // GET /status
    if (req.method === 'GET' && req.url === '/status') {
        res.writeHead(200)
        res.end(JSON.stringify({
            success:   true,
            connected: isConnected,
            hasQR:     currentQRString !== null,
            phone:     connectedNumber,
            message:   isConnected
                ? `✅ WhatsApp terhubung (${connectedNumber})`
                : (currentQRString ? '📱 QR siap — scan via dashboard admin.' : '⏳ Menunggu koneksi...'),
        }))
        return
    }

    // GET /qr
    if (req.method === 'GET' && req.url === '/qr') {
        if (isConnected || !currentQRString) {
            res.writeHead(200)
            res.end(JSON.stringify({ success: true, hasQR: false, qrDataUrl: null, connected: isConnected }))
            return
        }

        QRCode.toDataURL(currentQRString, {
            width: 280, margin: 2,
            color: { dark: '#111827', light: '#ffffff' },
        }).then(qrDataUrl => {
            res.writeHead(200)
            res.end(JSON.stringify({ success: true, hasQR: true, qrDataUrl, connected: false }))
        }).catch(err => {
            res.writeHead(500)
            res.end(JSON.stringify({ success: false, message: 'Gagal generate QR: ' + err.message }))
        })
        return
    }

    // POST /send-message
    if (req.method === 'POST' && req.url === '/send-message') {
        let rawBody = ''
        req.on('data', chunk => { rawBody += chunk.toString() })
        req.on('end', async () => {
            let payload
            try {
                payload = JSON.parse(rawBody)
            } catch {
                res.writeHead(400)
                res.end(JSON.stringify({ success: false, message: 'Request body harus berformat JSON.' }))
                return
            }

            const { phone, message } = payload

            if (!phone || !message) {
                res.writeHead(400)
                res.end(JSON.stringify({ success: false, message: 'Field "phone" dan "message" wajib diisi.' }))
                return
            }

            if (!isConnected) {
                res.writeHead(503)
                res.end(JSON.stringify({ success: false, message: 'WhatsApp belum terhubung. Scan QR terlebih dahulu.' }))
                return
            }

            try {
                await kirimPesan(phone, message)
                console.log(`[WA] 📤 Pesan terkirim ke: ${phone}`)
                res.writeHead(200)
                res.end(JSON.stringify({ success: true, message: 'Pesan berhasil dikirim.' }))
            } catch (err) {
                console.error(`[WA] ❌ Gagal kirim ke ${phone}:`, err.message)
                res.writeHead(500)
                res.end(JSON.stringify({ success: false, message: `Gagal mengirim pesan: ${err.message}` }))
            }
        })
        return
    }

    // POST /logout
    if (req.method === 'POST' && req.url === '/logout') {
        if (isConnected && waSocket) {
            waSocket.logout()
        } else {
            try { fs.rmSync('./auth_info', { recursive: true, force: true }) } catch (e) {}
            const now = new Date()
            fs.utimesSync(__filename, now, now)
        }
        res.writeHead(200)
        res.end(JSON.stringify({ success: true, message: 'Berhasil logout.' }))
        return
    }

    // 404
    res.writeHead(404)
    res.end(JSON.stringify({ success: false, message: 'Endpoint tidak ditemukan.' }))
})

// ─── Start ────────────────────────────────────────────────────────────────────
server.listen(PORT, '127.0.0.1', () => {
    console.log('╔══════════════════════════════════════════╗')
    console.log('║   🏸  Tap4Smash WA Service (Baileys)     ║')
    console.log('╚══════════════════════════════════════════╝')
    console.log(`🚀 HTTP server berjalan di: http://127.0.0.1:${PORT}`)
    console.log(`🔑 API Key aktif: ${API_KEY}`)
    console.log('─────────────────────────────────────────────')
    console.log(`  GET  http://127.0.0.1:${PORT}/status`)
    console.log(`  GET  http://127.0.0.1:${PORT}/qr`)
    console.log(`  POST http://127.0.0.1:${PORT}/send-message`)
    console.log('─────────────────────────────────────────────\n')

    connectToWhatsApp()
})

// ─── Graceful shutdown ────────────────────────────────────────────────────────
process.on('SIGINT', () => {
    console.log('\n[WA] Service dihentikan.')
    server.close(() => process.exit(0))
})
