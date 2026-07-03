<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Tap4Smash</title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:          #F4F6F9;
            --surface:     #FFFFFF;
            --surface2:    #EAEEF3;
            --border:      #D8DDE6;
            --navy:        #0F2044;
            --navy-mid:    #1A3260;
            --navy-light:  #2A4A7F;
            --navy-dim:    rgba(15,32,68,.08);
            --accent:      #AAEE00;
            --accent-dark: #88CC00;
            --accent-text: #3a5200;
            --text:        #0F2044;
            --text-muted:  #6B7FA3;
            --red:         #dc2626;
            --red-dim:     rgba(220,38,38,.12);
        }

        html, body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            /* Subtle grid background seperti hero frontend */
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 40px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 1.5rem 1rem;
        }

        /* ── Branding ─────────────────────────────────────────── */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 68px; height: 68px;
            background: var(--navy);
            color: var(--accent);
            font-size: 1.9rem;
            margin: 0 auto 1rem;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(15,32,68,.2);
            position: relative;
        }

        /* Accent dot */
        .brand-icon::after {
            content: '';
            position: absolute;
            bottom: -4px; right: -4px;
            width: 16px; height: 16px;
            background: var(--accent);
            border-radius: 50%;
            border: 2px solid var(--bg);
        }

        .brand h1 {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--navy);
        }

        .brand h1 span { color: var(--accent-dark); }

        .brand p {
            font-size: 0.7rem;
            color: var(--text-muted);
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 0.3rem;
        }

        /* ── Card ──────────────────────────────────────────────── */
        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(15,32,68,.1), 0 1px 4px rgba(15,32,68,.06);
        }

        .login-card-header {
            padding: 1.25rem 1.75rem 1.1rem;
            border-bottom: 1px solid var(--border);
            background: var(--navy);
            border-bottom: 3px solid var(--accent);
        }

        .login-card-header h2 {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            font-size: 0.88rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #fff;
        }

        .login-card-header p {
            font-size: 0.72rem;
            color: rgba(255,255,255,.5);
            margin-top: 0.2rem;
        }

        .login-body { padding: 1.75rem; }

        /* ── Alert ─────────────────────────────────────────────── */
        .alert-error {
            background: var(--red-dim);
            border: 1px solid rgba(220,38,38,.3);
            border-left: 4px solid var(--red);
            color: #991b1b;
            padding: 0.7rem 1rem;
            font-size: 0.82rem;
            margin-bottom: 1.25rem;
            display: flex;
            gap: 0.5rem;
            align-items: center;
            border-radius: 6px;
        }

        /* ── Form ──────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.1rem; }

        .form-group label {
            display: block;
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: var(--text-muted);
            margin-bottom: 0.45rem;
            font-family: 'Oswald', sans-serif;
        }

        .input-wrap { position: relative; }

        .input-wrap .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .form-group input {
            display: block;
            width: 100%;
            padding: 0.7rem 0.9rem 0.7rem 2.4rem;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            border-radius: 6px;
        }

        .form-group input:focus {
            border-color: var(--navy-light);
            box-shadow: 0 0 0 3px var(--navy-dim);
        }

        .form-group input::placeholder { color: var(--border-dark, #B0BAC9); }

        /* ── Submit ────────────────────────────────────────────── */
        .btn-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.8rem;
            background: var(--accent);
            color: var(--accent-text);
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            margin-top: 0.5rem;
            border-radius: 6px;
        }

        .btn-login:hover {
            background: var(--accent-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(170,238,0,.3);
        }

        .btn-login:active { transform: scale(.98); }

        /* ── Footer ────────────────────────────────────────────── */
        .login-footer {
            border-top: 1px solid var(--border);
            padding: 0.85rem 1.75rem;
            font-size: 0.67rem;
            color: var(--text-muted);
            text-align: center;
            background: var(--surface2);
        }

        .login-footer a {
            color: var(--navy-light);
            font-weight: 600;
        }

        .login-footer a:hover { color: var(--navy); }
    </style>
</head>
<body>
<div class="login-wrap">

    <div class="brand">
        <div class="brand-icon">
            <i class="fa-solid fa-table-tennis-paddle-ball"></i>
        </div>
        <h1>Tap4<span>Smash</span></h1>
        <p>Admin Dashboard</p>
    </div>

    <div class="login-card">
        <div class="login-card-header">
            <h2><i class="fa-solid fa-shield-halved" style="color:var(--accent);margin-right:.4rem;"></i>Masuk ke Panel Admin</h2>
            <p>Khusus pengelola GOR Tap4Smash</p>
        </div>

        <div class="login-body">
            <?php if (! empty($error)): ?>
            <div class="alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <?= esc($error) ?>
            </div>
            <?php endif; ?>

            <form action="<?= site_url('admin/login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user input-icon"></i>
                        <input type="text" id="username" name="username"
                               placeholder="Masukkan username"
                               autocomplete="username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password"
                               placeholder="••••••••"
                               autocomplete="current-password" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Dashboard
                </button>
            </form>
        </div>

        <div class="login-footer">
            <a href="<?= site_url('/') ?>"><i class="fa-solid fa-arrow-left"></i> Kembali ke halaman publik</a>
            &nbsp;&mdash;&nbsp;
            Tap4Smash &copy; <?= date('Y') ?>
        </div>
    </div>

</div>
</body>
</html>
