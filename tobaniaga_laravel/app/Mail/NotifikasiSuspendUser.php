<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifikasiSuspendUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $alasan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun TobaNiaga Kamu Telah Disuspend',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.suspend-user',
        );
    }
}
