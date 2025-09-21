<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;

class UpdateEventStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:update-statuses {--dry-run : Show what would be updated without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update event statuses based on current date and registration deadlines';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏆 Updating Event Statuses...');
        $this->info('Current Date/Time: ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->newLine();
        
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }
        
        // Get events that might need status updates
        $events = Event::whereIn('status', [
            'draft',
            'open_registration', 
            'registration_closed',
            'ongoing'
        ])->orderBy('event_date')->get();
        
        if ($events->isEmpty()) {
            $this->info('ℹ️  No events found that need status updates.');
            return;
        }
        
        $this->info("📅 Found {$events->count()} events to check...");
        $this->newLine();
        
        $updatedCount = 0;
        $table = [];
        
        foreach ($events as $event) {
            $oldStatus = $event->status;
            
            // Use dry-run parameter to avoid saving during test
            $result = $event->updateStatus($dryRun);
            $newStatus = $event->status;
            
            $statusIcon = $this->getStatusIcon($newStatus);
            $changeIndicator = $oldStatus !== $newStatus ? '🔄' : '✅';
            
            $table[] = [
                $changeIndicator,
                $event->event_code,
                $event->title,
                $event->event_date->format('Y-m-d'),
                $event->registration_deadline->format('Y-m-d'),
                $oldStatus,
                $newStatus . ' ' . $statusIcon,
                $oldStatus !== $newStatus ? 'Updated' : 'No change'
            ];
            
            if ($oldStatus !== $newStatus) {
                $updatedCount++;
                
                if (!$dryRun) {
                    $this->info("✅ Updated: {$event->event_code} - {$event->title}");
                    $this->line("   {$oldStatus} → {$newStatus}");
                } else {
                    $this->info("🔍 Would update: {$event->event_code} - {$event->title}");
                    $this->line("   {$oldStatus} → {$newStatus}");
                }
            }
        }
        
        $this->newLine();
        $this->table([
            'Action',
            'Code',
            'Event Title',
            'Event Date',
            'Reg Deadline',
            'Old Status',
            'New Status',
            'Result'
        ], $table);
        
        $this->newLine();
        
        if ($dryRun) {
            $this->info("🔍 DRY RUN SUMMARY:");
            $this->info("   - Events checked: {$events->count()}");
            $this->info("   - Would be updated: {$updatedCount}");
            $this->newLine();
            $this->comment('Run without --dry-run to actually update the statuses.');
        } else {
            $this->info("✨ UPDATE SUMMARY:");
            $this->info("   - Events checked: {$events->count()}");
            $this->info("   - Events updated: {$updatedCount}");
            
            if ($updatedCount > 0) {
                $this->info("   🎉 Successfully updated {$updatedCount} event(s)!");
            } else {
                $this->info("   ℹ️  All events are already up to date.");
            }
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Get emoji icon for status
     */
    private function getStatusIcon($status)
    {
        return match($status) {
            'draft' => '📝',
            'open_registration' => '🔓',
            'registration_closed' => '🔒',
            'ongoing' => '🏃',
            'completed' => '🏁',
            'cancelled' => '❌',
            default => '❓'
        };
    }
}
