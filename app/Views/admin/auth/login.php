<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Tap4Smash</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,900;1,900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --volt: #CCFF00; --charcoal: #111827; --slate-dark: #1F2937;
            --slate: #374151; --text-muted: #9CA3AF; --red: #EF4444;
        }
        body {
            background: var(--charcoal);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            background-image:
                linear-gradient(rgba(204,255,0,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(204,255,0,.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .login-wrap { width: 100%; max-width: 380px; padding: 1rem; }

        /* Branding */
        .brand { text-align: center; margin-bottom: 2rem; }
        .brand-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 64px; height: 64px;
            background: var(--volt);
            color: var(--charcoal);
            font-size: 1.75rem;
            margin: 0 auto 0.75rem;
        }
        .brand h1 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 1.6rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--volt);
            font-style: italic;
        }
        .brand p {
            font-size: 0.72rem;
            color: var(--text-muted);
            letter-spacing: 0.15em;
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 0.2rem;
        }

        /* Card */
        .login-card {
            background: var(--slate-dark);
            border: 1px solid var(--slate);
            border-top: 2px solid var(--volt);
            border-radius: 6px;
            overflow: hidden;
        }
        .login-card-header {
            padding: 1.25rem 1.75rem 1rem;
            border-bottom: 1px solid var(--slate);
        }
        .login-card-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #fff;
        }
        .login-card-header p { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; }

        .login-body { padding: 1.5rem 1.75rem; }

        /* Alert */
        .alert-error {
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.3);
            border-left: 3px solid var(--red);
            color: #fca5a5;
            padding: 0.65rem 1rem;
            font-size: 0.8rem;
            margin-bottom: 1.25rem;
            display: flex;
            gap: 0.5rem;
            align-items: center;
            border-radius: 6px;
        }

        /* Form */
        .form-group { margin-bottom: 1.1rem; }
        .form-group label {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 0.45rem;
        }
        .input-wrap { position: relative; }
        .input-wrap .input-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #4B5563;
            font-size: 0.8rem;
        }
        .form-group input {
            display: block;
            width: 100%;
            padding: 0.65rem 0.9rem 0.65rem 2.25rem;
            background: var(--charcoal);
            border: 1px solid var(--slate);
            color: #F9FAFB;
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.15s;
            border-radius: 6px;
        }
        .form-group input:focus { border-color: var(--volt); }
        .form-group input::placeholder { color: #4B5563; }

        /* Submit */
        .btn-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.75rem;
            background: var(--volt);
            color: var(--charcoal);
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border: none;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            margin-top: 0.25rem;
            border-radius: 6px;
        }
        .btn-login:hover  { background: #b8e800; }
        .btn-login:active { transform: scale(.98); }

        /* Footer */
        .login-footer {
            border-top: 1px solid var(--slate);
            padding: 0.75rem 1.75rem;
            font-size: 0.68rem;
            color: var(--text-muted);
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-wrap">

    <div class="brand">
        <div class="brand-icon">
            <i class="fa-solid fa-table-tennis-paddle-ball"></i>
        </div>
        <h1>Tap4Smash</h1>
        <p>Admin Dashboard</p>
    </div>

    <div class="login-card">
        <div class="login-card-header">
            <h2>Masuk ke Panel Admin</h2>
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
                               placeholder="admin" autocomplete="username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password" id="password" name="password"
                               placeholder="••••••••" autocomplete="current-password" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Masuk <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <div class="login-footer">
            Tap4Smash &copy; <?= date('Y') ?> &mdash; GOR Badminton Management System
        </div>
    </div>

</div>
</body>
</html>
