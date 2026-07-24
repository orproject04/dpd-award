<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Pendaftar;

class BuktiDaftarEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $pendaftar;
    public $waktuSubmit;

    /**
     * Create a new message instance.
     */
    public function __construct(Pendaftar $pendaftar, $waktuSubmit)
    {
        $this->pendaftar = $pendaftar;
        $this->waktuSubmit = $waktuSubmit;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bukti Pendaftaran - DPDRI Awards 2026',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bukti_daftar',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $this->pendaftar->load(['kontribusi', 'penghargaan']);

        // Generate PDF in memory from the PDF view
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bukti_daftar', [
            'pendaftar' => $this->pendaftar,
            'waktuSubmit' => $this->waktuSubmit,
        ]);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(fn () => $pdf->output(), 'Bukti_Pendaftaran_' . $this->pendaftar->nomor_registrasi . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
