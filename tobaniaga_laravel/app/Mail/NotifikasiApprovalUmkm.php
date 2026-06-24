<?php

namespace App\Mail;

use App\Models\Umkm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiApprovalUmkm extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Umkm   $umkm,
        public readonly string $status,   // 'approved' | 'rejected'
        public readonly string $alasan = '',
    ) {}

    public function envelope(): Envelope
    {
        $subjek = $this->status === 'approved'
            ? "Selamat! UMKM \"{$this->umkm->nama_umkm}\" telah diverifikasi"
            : "Update pendaftaran UMKM \"{$this->umkm->nama_umkm}\"";

        return new Envelope(subject: $subjek);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.approval-umkm');
    }
}
