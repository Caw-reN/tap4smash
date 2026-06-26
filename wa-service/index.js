'use strict'

require('dotenv').config()

const {
    default: makeWASocket,
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestBaileysVersion,
    isJidBroadcast,
} = require('@whiskeysockets/baileys')
const { Boom }  = require('@hapi/boom')
const pino      = require('pino')
const http      = require('http')
const fs        = require('fs')
const qrcode    = require('qrcode-terminal')
const QRCode    = require('qrcode')          // generate QR sebagai gambar PNG

// ─── Config ───────────────────────────────────────────────────────────────────
const PORT    = parseInt(process.env.WA_SERVICE_PORT  || '3001', 10)
const API_KEY = process.env.WA_SERVICE_API_KEY || 'tap4smash_wa_secret_2025'

// ─── State ────────────────────────────────────────────────────────────────────
let waSocket         = null
let isConnected      = false
let currentQRString  = null   // string QR terbaru dari Baileys
let connectedNumber  = null   // nomor WA yang sedang terhubung

// ─── Logger (silent — hanya console.log manual yang tampil) ──────────────────
const logger = pino({ level: 'silent' })

// ─── Baileys: Connect ke WhatsApp ─────────────────────────────────────────────
async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('./auth_info')
    const { version }          = await fetchLatestBaileysVersion()

    console.log(`\n[WA] Menggunakan Baileys versi protokol WA: ${version.join('.')}`)

    const sock = makeWASocket({
        version,
        auth:               state,
        printQRInTerminal:  false, // kita handle manual lewat qrcode-terminal
        logger,
        // Abaikan pesan broadcast/status WA
        shouldIgnoreJid: jid => isJidBroadcast(jid),
    })

    // ── Event: update koneksi ──────────────────────────────────────────────────
    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update

        // Simpan QR string & tampilkan di terminal
        if (qr) {
            currentQRString = qr
            console.log('\n📱 Scan QR berikut dengan WhatsApp di HP kamu (Settings > Linked Devices):')
            console.log('   Atau buka dashboard admin CI4 → Menu WhatsApp untuk scan via browser.\n')
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
                console.log('[WA] ❌ Logged out! Menghapus folder auth_info/ dan merestart...')
                try { fs.rmSync('./auth_info', { recursive: true, force: true }) } catch(e){}
                
                // Pancing node --watch untuk merestart process dengan mengupdate timestamp file ini
                console.log('[WA] Merestart service...')
                setTimeout(() => {
                    const now = new Date()
                    fs.utimesSync(__filename, now, now)
                }, 1000)
            }

            // Reconnect otomatis untuk alasan selain logged out
            const delay = statusCode === DisconnectReason.restartRequired ? 1000 : 5000
            console.log(`[WA] 🔄 Mencoba reconnect dalam ${delay / 1000} detik...`)
            setTimeout(connectToWhatsApp, delay)
        }

        if (connection === 'open') {
            isConnected     = true
            waSocket        = sock
            currentQRString = null   // QR sudah tidak dibutuhkan
            connectedNumber = sock.user?.id?.split(':')[0] ?? null
            console.log(`\n✅ [WA] Berhasil terhubung! Nomor aktif: ${connectedNumber}\n`)
        }
    })

    // ── Simpan kredensial setiap kali ada perubahan ────────────────────────────
    sock.ev.on('creds.update', saveCreds)
}

// ─── Kirim pesan WhatsApp ─────────────────────────────────────────────────────
async function kirimPesan(phone, message) {
    if (!isConnected || !waSocket) {
        throw new Error('WhatsApp belum terhubung ke server.')
    }

    // Pastikan format nomor benar: hanya angka, diakhiri @s.whatsapp.net
    const cleanPhone = phone.replace(/\D/g, '')
    const jid        = `${cleanPhone}@s.whatsapp.net`

    await waSocket.sendMessage(jid, { text: message })
    return true
}

// ─── HTTP Server (dipanggil oleh CI4 via cURL) ────────────────────────────────
const server = http.createServer((req, res) => {
    res.setHeader('Content-Type', 'application/json; charset=utf-8')

    // ── Autentikasi API Key ────────────────────────────────────────────────────
    const incomingKey = req.headers['x-api-key']
    if (incomingKey !== API_KEY) {
        res.writeHead(401)
        res.end(JSON.stringify({ success: false, message: 'Unauthorized: API Key tidak valid.' }))
        return
    }

    // ── GET /status ────────────────────────────────────────────────────────────
    if (req.method === 'GET' && req.url === '/status') {
        res.writeHead(200)
        res.end(JSON.stringify({
            success:  true,
            connected: isConnected,
            hasQR:    currentQRString !== null,
            phone:    connectedNumber,
            message:  isConnected
                ? `✅ WhatsApp terhubung (${connectedNumber})`
                : (currentQRString ? '📱 QR siap — scan via dashboard admin.' : '⏳ Menunggu koneksi...'),
        }))
        return
    }

    // ── GET /qr — kembalikan QR sebagai Base64 PNG untuk dashboard admin ────────
    if (req.method === 'GET' && req.url === '/qr') {
        if (isConnected || !currentQRString) {
            res.writeHead(200)
            res.end(JSON.stringify({
                success: true,
                hasQR:   false,
                qrDataUrl: null,
                connected: isConnected,
            }))
            return
        }

        // Generate QR sebagai PNG data URL
        QRCode.toDataURL(currentQRString, {
            width:  280,
            margin: 2,
            color:  { dark: '#111827', light: '#ffffff' },
        }).then(qrDataUrl => {
            res.writeHead(200)
            res.end(JSON.stringify({
                success:   true,
                hasQR:     true,
                qrDataUrl: qrDataUrl,
                connected: false,
            }))
        }).catch(err => {
            res.writeHead(500)
            res.end(JSON.stringify({ success: false, message: 'Gagal generate QR: ' + err.message }))
        })
        return
    }

    // ── POST /send-message ─────────────────────────────────────────────────────
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
                res.end(JSON.stringify({
                    success: false,
                    message: 'WhatsApp belum terhubung. Cek terminal untuk scan QR terlebih dahulu.',
                }))
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

    // ── POST /logout ───────────────────────────────────────────────────────────
    if (req.method === 'POST' && req.url === '/logout') {
        if (isConnected && waSocket) {
            waSocket.logout()
        } else {
            // Jika tidak connect, paksa hapus folder dan restart
            try { fs.rmSync('./auth_info', { recursive: true, force: true }) } catch(e){}
            const now = new Date()
            fs.utimesSync(__filename, now, now)
        }
        res.writeHead(200)
        res.end(JSON.stringify({ success: true, message: 'Berhasil logout.' }))
        return
    }

    // ── 404 untuk endpoint lain ────────────────────────────────────────────────
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
    console.log('Endpoint tersedia:')
    console.log(`  GET  http://127.0.0.1:${PORT}/status`)
    console.log(`  GET  http://127.0.0.1:${PORT}/qr`)
    console.log(`  POST http://127.0.0.1:${PORT}/send-message`)
    console.log('─────────────────────────────────────────────\n')

    // Mulai koneksi ke WhatsApp
    connectToWhatsApp()
})

// ─── Graceful shutdown ────────────────────────────────────────────────────────
process.on('SIGINT', () => {
    console.log('\n[WA] Service dihentikan.')
    server.close(() => process.exit(0))
})
