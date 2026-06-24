<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiAktivasiUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun TobaNiaga Kamu Telah Diaktifkan Kembali',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.aktivasi-user',
        );
    }
}
