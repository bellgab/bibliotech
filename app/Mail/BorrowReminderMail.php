<?php

namespace App\Mail;

use App\Models\BookBorrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BorrowReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $borrow;

    public function __construct(BookBorrowing $borrow)
    {
        $this->borrow = $borrow;
    }

    public function build()
    {
        return $this->view('emails.borrow-reminder')
                    ->subject('Könyv visszahozási emlékeztető - ' . $this->borrow->book->title)
                    ->with([
                        'user' => $this->borrow->user,
                        'book' => $this->borrow->book,
                        'dueDate' => $this->borrow->due_date,
                        'isOverdue' => $this->borrow->is_overdue
                    ]);
    }
}
