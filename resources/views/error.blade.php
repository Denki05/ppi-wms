<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Gagal — WMS Gudang</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at 10% 0%, rgba(15,118,110,.07), transparent 42%),
                radial-gradient(circle at 95% 8%, rgba(37,99,235,.05), transparent 38%),
                #f6f9f8;
            color: #1e293b;
        }
        .error-card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border: 1px solid #e6eeeb;
            border-top: 3px solid #dc2626;
            border-radius: 14px;
            padding: 32px 28px 28px;
            text-align: center;
            box-shadow: 0 18px 40px -18px rgba(15,23,42,.22), 0 2px 8px rgba(15,23,42,.05);
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            font-weight: 700;
            font-size: 13.5px;
            color: #0f172a;
        }
        .brand-mark {
            width: 26px; height: 26px; border-radius: 7px;
            display: grid; place-items: center;
            background: linear-gradient(135deg, #14b8a6, #0f766e);
            color: #fff;
        }
        .brand small {
            display: block;
            font-size: 10px;
            font-weight: 400;
            color: #64748b;
            line-height: 1.1;
            text-align: left;
        }
        .error-icon {
            width: 52px; height: 52px;
            margin: 0 auto 14px;
            display: grid; place-items: center;
            border-radius: 50%;
            background: #fef2f2;
            color: #dc2626;
        }
        h1 { margin: 0 0 8px; font-size: 18px; color: #b91c1c; }
        p {
            margin: 0 0 22px;
            font-size: 13px;
            line-height: 1.55;
            color: #475569;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 18px;
            border-radius: 8px;
            background: linear-gradient(135deg, #14b8a6, #0f766e);
            color: #fff;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s ease;
        }
        .btn:hover { background: linear-gradient(135deg, #0f766e, #115e59); }
        .hint {
            margin: 16px 0 0;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="brand">
            <span class="brand-mark">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>
                </svg>
            </span>
            <span>
                WMS Gudang
                <small>SPV Logistik</small>
            </span>
        </div>

        <div class="error-icon">
            <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/>
                <path d="M5.6 5.6l12.8 12.8"/>
            </svg>
        </div>

        <h1>Akses WMS Ditolak</h1>
        <p>{{ $message }}</p>

        <a href="{{ config('wms.trans_portal_url') }}" class="btn">Kembali ke Portal Trans</a>
        <p class="hint">Anda tidak memiliki akses ke modul WMS. Hubungi admin bila akses ini seharusnya aktif.</p>
    </div>
</body>
</html>
