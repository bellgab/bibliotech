<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class SendBorrowReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bibliotech:send-reminders 
                            {--type=all : Type of reminders to send (due, overdue, all)}
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for due and overdue book borrowings';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No emails will be sent');
            $this->line('');
        }

        $this->info('📧 BiblioTech Email Notification Service');
        $this->line('=====================================');

        // Get current statistics
        $stats = $this->notificationService->getNotificationStats();
        
        $this->table(['Type', 'Count'], [
            ['Upcoming Due (3 days)', $stats['upcoming_due']],
            ['Overdue', $stats['overdue']],
            ['Total Pending', $stats['total_pending']]
        ]);

        if ($stats['total_pending'] === 0) {
            $this->info('✅ No reminders to send!');
            return 0;
        }

        $totalSent = 0;

        // Send due reminders
        if (in_array($type, ['due', 'all'])) {
            $this->line('');
            $this->info('📅 Sending due date reminders...');
            
            if (!$dryRun) {
                $dueSent = $this->notificationService->sendDueReminders();
                $totalSent += $dueSent;
                $this->info("✅ Sent {$dueSent} due date reminders");
            } else {
                $this->warn("Would send {$stats['upcoming_due']} due date reminders");
            }
        }

        // Send overdue notifications
        if (in_array($type, ['overdue', 'all'])) {
            $this->line('');
            $this->info('⚠️  Sending overdue notifications...');
            
            if (!$dryRun) {
                $overdueSent = $this->notificationService->sendOverdueNotifications();
                $totalSent += $overdueSent;
                $this->info("✅ Sent {$overdueSent} overdue notifications");
            } else {
                $this->warn("Would send {$stats['overdue']} overdue notifications");
            }
        }

        $this->line('');
        if (!$dryRun) {
            $this->info("📨 Total emails sent: {$totalSent}");
        } else {
            $this->warn("📋 Total emails that would be sent: {$stats['total_pending']}");
        }

        return 0;
    }
}
