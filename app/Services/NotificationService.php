<?php

namespace App\Services;

use App\Models\BookBorrowing;
use App\Mail\BorrowReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Emlékeztető emailek küldése közelgő határidőkre
     */
    public function sendDueReminders()
    {
        $sent = 0;
        
        // Könyv visszahozási határidő előtt 3 nappal
        $upcomingDue = BookBorrowing::whereNull('returned_at')
            ->where('due_date', '=', Carbon::now()->addDays(3)->toDateString())
            ->with(['user', 'book.author'])
            ->get();

        foreach ($upcomingDue as $borrow) {
            try {
                Mail::to($borrow->user->email)->send(new BorrowReminderMail($borrow));
                $sent++;
            } catch (\Exception $e) {
                \Log::error('Failed to send reminder email to ' . $borrow->user->email . ': ' . $e->getMessage());
            }
        }

        return $sent;
    }

    /**
     * Lejárt kölcsönzések értesítése
     */
    public function sendOverdueNotifications()
    {
        $sent = 0;
        
        $overdue = BookBorrowing::whereNull('returned_at')
            ->where('due_date', '<', Carbon::now()->toDateString())
            ->with(['user', 'book.author'])
            ->get();

        foreach ($overdue as $borrow) {
            try {
                Mail::to($borrow->user->email)->send(new BorrowReminderMail($borrow));
                $sent++;
            } catch (\Exception $e) {
                \Log::error('Failed to send overdue email to ' . $borrow->user->email . ': ' . $e->getMessage());
            }
        }

        return $sent;
    }

    /**
     * Könyv visszahozás megerősítése
     */
    public function sendReturnConfirmation(BookBorrowing $borrow)
    {
        try {
            // Create a simple return confirmation email
            Mail::raw(
                "Kedves {$borrow->user->name}!\n\n" .
                "Köszönjük, hogy visszahozta a(z) '{$borrow->book->title}' című könyvet.\n" .
                "Visszahozás időpontja: " . $borrow->returned_at->format('Y. m. d. H:i') . "\n\n" .
                "Várjuk újra könyvtárunkban!\n\n" .
                "BiblioTech csapata",
                function ($message) use ($borrow) {
                    $message->to($borrow->user->email)
                            ->subject('Könyv visszahozás megerősítése - ' . $borrow->book->title);
                }
            );
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send return confirmation to ' . $borrow->user->email . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Új könyv kölcsönzés megerősítése
     */
    public function sendBorrowConfirmation(BookBorrowing $borrow)
    {
        try {
            Mail::raw(
                "Kedves {$borrow->user->name}!\n\n" .
                "Sikeresen kikölcsönözte a(z) '{$borrow->book->title}' című könyvet.\n\n" .
                "Kölcsönzés részletei:\n" .
                "- Szerző: {$borrow->book->author->name}\n" .
                "- Kölcsönzés dátuma: " . $borrow->borrowed_at->format('Y. m. d.') . "\n" .
                "- Visszahozási határidő: " . $borrow->due_date->format('Y. m. d.') . "\n\n" .
                "Kérjük, hogy a megadott határidőig hozza vissza a könyvet!\n\n" .
                "Jó olvasást!\n\n" .
                "BiblioTech csapata",
                function ($message) use ($borrow) {
                    $message->to($borrow->user->email)
                            ->subject('Könyv kölcsönzés megerősítése - ' . $borrow->book->title);
                }
            );
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send borrow confirmation to ' . $borrow->user->email . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Statisztikák az elküldött értesítésekről
     */
    public function getNotificationStats()
    {
        $upcomingDue = BookBorrowing::whereNull('returned_at')
            ->where('due_date', '=', Carbon::now()->addDays(3)->toDateString())
            ->count();

        $overdue = BookBorrowing::whereNull('returned_at')
            ->where('due_date', '<', Carbon::now()->toDateString())
            ->count();

        return [
            'upcoming_due' => $upcomingDue,
            'overdue' => $overdue,
            'total_pending' => $upcomingDue + $overdue
        ];
    }
}
