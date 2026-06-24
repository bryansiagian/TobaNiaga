<?php

namespace App\Mail;

use App\Models\Umkm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiRektivasiUmkm extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Umkm $umkm) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'UMKM Kamu Telah Diaktifkan Kembali — TobaNiaga',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reaktivasi-umkm',
        );
    }
}
