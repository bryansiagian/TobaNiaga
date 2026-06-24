<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kode Verifikasi TobaNiaga</title>
<style>
  body { margin: 0; padding: 0; background: #f0f4f8; font-family: Inter, -apple-system, sans-serif; color: #1a1a1a; }
  .wrap { max-width: 520px; margin: 40px auto; background: #faf8f5; border-radius: 16px; overflow: hidden; border: 1px solid #0f2d4514; }
  .stripe { height: 6px; background: repeating-linear-gradient(90deg, #8f2333 0 8px, #c49044 8px 14px, #1a4a6b 14px 22px, #faf8f5 22px 26px, #1a4a6b 26px 34px, #c49044 34px 40px, #8f2333 40px 48px); }
  .header { padding: 32px 40px 24px; border-bottom: 1px solid #0f2d4510; }
  .logo { font-size: 20px; font-weight: 600; color: #0f2d45; letter-spacing: -0.3px; }
  .body { padding: 36px 40px; }
  .greeting { font-size: 15px; color: #1a1a1a; margin-bottom: 12px; }
  .desc { font-size: 14px; color: #1a1a1a99; line-height: 1.6; margin-bottom: 32px; }
  .otp-box { background: #0f2d45; border-radius: 12px; padding: 28px; text-align: center; margin-bottom: 28px; }
  .otp-label { font-size: 11px; font-family: 'JetBrains Mono', monospace; text-transform: uppercase; letter-spacing: 0.15em; color: #faf8f599; margin-bottom: 12px; }
  .otp-code { font-size: 42px; font-weight: 700; letter-spacing: 0.25em; color: #c49044; font-family: 'JetBrains Mono', monospace; }
  .expire { font-size: 13px; color: #1a1a1a60; text-align: center; margin-bottom: 28px; }
  .expire strong { color: #8f2333; }
  .warning { background: #8f233308; border: 1px solid #8f233320; border-radius: 8px; padding: 14px 16px; font-size: 13px; color: #8f2333; line-height: 1.5; }
  .footer { padding: 20px 40px 28px; border-top: 1px solid #0f2d4510; }
  .footer p { font-size: 12px; color: #1a1a1a40; margin: 0; line-height: 1.6; }
</style>
</head>
<body>
<div class="wrap">
  <div class="stripe"></div>

  <div class="header">
    <div class="logo">TobaNiaga</div>
  </div>

  <div class="body">
    <p class="greeting">Halo, <strong>{{ $nama }}</strong>!</p>
    <p class="desc">
      Gunakan kode di bawah ini untuk memverifikasi alamat email kamu di TobaNiaga.
      Kode hanya berlaku sekali dan akan kadaluarsa dalam waktu singkat.
    </p>

    <div class="otp-box">
      <div class="otp-label">Kode Verifikasi</div>
      <div class="otp-code">{{ $kode }}</div>
    </div>

    <p class="expire">Kode berlaku selama <strong>10 menit</strong> sejak email ini dikirim.</p>

    <div class="warning">
      Jika kamu tidak mendaftar di TobaNiaga, abaikan email ini.
      Jangan bagikan kode ini kepada siapa pun.
    </div>
  </div>

  <div class="footer">
    <p>Email ini dikirim otomatis oleh sistem TobaNiaga. Jangan balas email ini.<br>
    &copy; {{ date('Y') }} TobaNiaga — Pasar UMKM Danau Toba.</p>
  </div>
</div>
</body>
</html>
