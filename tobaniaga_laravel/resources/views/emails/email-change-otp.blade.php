<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; background: #FAF7F0; padding: 32px;">
    <div style="max-width: 480px; margin: 0 auto; background: white; border-radius: 12px; padding: 32px; border: 1px solid #CBDEDC;">
        <p style="font-size: 22px; font-weight: 600; color: #0A2C2D; margin-bottom: 8px;">TobaNiaga</p>
        <p style="color: #2A2622; margin-bottom: 24px;">Halo <strong>{{ $nama }}</strong>,</p>
        <p style="color: #2A2622; margin-bottom: 16px;">Kamu meminta untuk mengganti email akun TobaNiaga. Gunakan kode OTP berikut:</p>
        <div style="background: #EAF1F0; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 24px;">
            <p style="font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #0A2C2D; margin: 0;">{{ $otp }}</p>
        </div>
        <p style="color: #2A262299; font-size: 13px;">Kode berlaku selama <strong>10 menit</strong>. Jangan bagikan kode ini ke siapapun.</p>
        <p style="color: #2A262299; font-size: 13px;">Jika kamu tidak meminta perubahan email, abaikan pesan ini.</p>
    </div>
</body>
</html>
