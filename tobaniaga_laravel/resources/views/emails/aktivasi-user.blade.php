<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akun Diaktifkan — TobaNiaga</title>
<style>
  body { margin: 0; padding: 0; background: #f0f4f8; font-family: Inter, -apple-system, sans-serif; color: #1a1a1a; }
  .wrap { max-width: 520px; margin: 40px auto; background: #faf8f5; border-radius: 16px; overflow: hidden; border: 1px solid #0f2d4514; }
  .stripe { height: 6px; background: repeating-linear-gradient(90deg, #8f2333 0 8px, #c49044 8px 14px, #1a4a6b 14px 22px, #faf8f5 22px 26px, #1a4a6b 26px 34px, #c49044 34px 40px, #8f2333 40px 48px); }
  .header { padding: 32px 40px 24px; border-bottom: 1px solid #0f2d4510; }
  .logo { font-size: 20px; font-weight: 600; color: #0f2d45; }
  .body { padding: 36px 40px; }
  .badge { display: inline-block; background: #1a4a6b15; color: #1a4a6b; border: 1px solid #1a4a6b30; border-radius: 6px; padding: 4px 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 20px; }
  h2 { font-size: 22px; font-weight: 600; color: #0f2d45; margin: 0 0 12px; line-height: 1.3; }
  p { font-size: 14px; color: #1a1a1a90; line-height: 1.7; margin: 0 0 16px; }
  .info-box { background: #0f2d4508; border: 1px solid #0f2d4518; border-radius: 8px; padding: 16px; font-size: 13px; color: #0f2d45; line-height: 1.6; margin: 20px 0; }
  .cta { display: inline-block; background: #0f2d45; color: #faf8f5; font-weight: 600; font-size: 14px; padding: 12px 28px; border-radius: 8px; text-decoration: none; margin-top: 8px; }
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
    <div class="badge">✓ Akun Diaktifkan</div>
    <h2>Akun kamu telah diaktifkan kembali!</h2>
    <p>Halo <strong>{{ $user->nama }}</strong>,</p>
    <p>Kabar baik! Akun TobaNiaga kamu telah diaktifkan kembali oleh tim kami.</p>
    <div class="info-box">
      Kamu sekarang bisa login dan menggunakan TobaNiaga seperti biasa.
    </div>
    <a href="{{ route('login') }}" class="cta">Login Sekarang</a>
  </div>
  <div class="footer">
    <p>Email ini dikirim otomatis oleh sistem TobaNiaga. Jangan balas email ini.<br>
    &copy; {{ date('Y') }} TobaNiaga — Pasar UMKM Danau Toba.</p>
  </div>
</div>
</body>
</html>
