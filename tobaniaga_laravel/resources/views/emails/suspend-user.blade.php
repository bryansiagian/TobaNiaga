<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Akun Disuspend — TobaNiaga</title>
<style>
  body { margin: 0; padding: 0; background: #f0f4f8; font-family: Inter, -apple-system, sans-serif; color: #1a1a1a; }
  .wrap { max-width: 520px; margin: 40px auto; background: #faf8f5; border-radius: 16px; overflow: hidden; border: 1px solid #0f2d4514; }
  .stripe { height: 6px; background: repeating-linear-gradient(90deg, #8f2333 0 8px, #c49044 8px 14px, #1a4a6b 14px 22px, #faf8f5 22px 26px, #1a4a6b 26px 34px, #c49044 34px 40px, #8f2333 40px 48px); }
  .header { padding: 32px 40px 24px; border-bottom: 1px solid #0f2d4510; }
  .logo { font-size: 20px; font-weight: 600; color: #0f2d45; }
  .body { padding: 36px 40px; }
  .badge { display: inline-block; background: #8f233310; color: #8f2333; border: 1px solid #8f233330; border-radius: 6px; padding: 4px 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 20px; }
  h2 { font-size: 22px; font-weight: 600; color: #0f2d45; margin: 0 0 12px; line-height: 1.3; }
  p { font-size: 14px; color: #1a1a1a90; line-height: 1.7; margin: 0 0 16px; }
  .alasan-box { background: #8f233308; border: 1px solid #8f233320; border-radius: 8px; padding: 16px; font-size: 13px; color: #8f2333; line-height: 1.6; margin: 20px 0; }
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
    <div class="badge">⚠ Akun Disuspend</div>
    <h2>Akun kamu telah disuspend</h2>
    <p>Halo <strong>{{ $user->nama }}</strong>,</p>
    <p>Kami ingin memberitahu bahwa akun TobaNiaga kamu telah disuspend oleh tim kami.</p>
    <div class="alasan-box">
      <strong>Alasan:</strong><br>{{ $alasan }}
    </div>
    <p>Jika kamu merasa ini adalah kesalahan atau ingin mengajukan banding, silakan hubungi tim TobaNiaga.</p>
  </div>
  <div class="footer">
    <p>Email ini dikirim otomatis oleh sistem TobaNiaga. Jangan balas email ini.<br>
    &copy; {{ date('Y') }} TobaNiaga — Pasar UMKM Danau Toba.</p>
  </div>
</div>
</body>
</html>
