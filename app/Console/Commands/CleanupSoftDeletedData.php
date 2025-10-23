<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Booking;
use App\Models\Event;
use Carbon\Carbon;

class CleanupSoftDeletedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:soft-deleted {--days=30 : Number of days after which soft deleted data will be permanently deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft deleted records older than specified days (default: 30 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("🗑️  Starting cleanup of soft deleted data older than {$days} days...");
        $this->info("Cutoff date: {$cutoffDate->format('Y-m-d H:i:s')}");
        $this->newLine();
        
        $totalDeleted = 0;
        
        // Cleanup Users
        $this->info('📋 Cleaning up Users...');
        $deletedUsers = User::onlyTrashed()
            ->where('deleted_at', '<=', $cutoffDate)
            ->get();
        
        if ($deletedUsers->count() > 0) {
            foreach ($deletedUsers as $user) {
                $this->line("  - Deleting user: {$user->name} (ID: {$user->id}, Deleted: {$user->deleted_at->format('Y-m-d')})");
                $user->forceDelete();
                $totalDeleted++;
            }
            $this->info("  ✅ Deleted {$deletedUsers->count()} users permanently");
        } else {
            $this->line("  ℹ️  No users to delete");
        }
        $this->newLine();
        
        // Cleanup Bookings
        $this->info('📋 Cleaning up Bookings...');
        $deletedBookings = Booking::onlyTrashed()
            ->where('deleted_at', '<=', $cutoffDate)
            ->get();
        
        if ($deletedBookings->count() > 0) {
            foreach ($deletedBookings as $booking) {
                $this->line("  - Deleting booking: {$booking->booking_code} (ID: {$booking->id}, Deleted: {$booking->deleted_at->format('Y-m-d')})");
                $booking->forceDelete();
                $totalDeleted++;
            }
            $this->info("  ✅ Deleted {$deletedBookings->count()} bookings permanently");
        } else {
            $this->line("  ℹ️  No bookings to delete");
        }
        $this->newLine();
        
        // Cleanup Events
        $this->info('📋 Cleaning up Events...');
        $deletedEvents = Event::onlyTrashed()
            ->where('deleted_at', '<=', $cutoffDate)
            ->get();
        
        if ($deletedEvents->count() > 0) {
            foreach ($deletedEvents as $event) {
                $this->line("  - Deleting event: {$event->title} (ID: {$event->id}, Deleted: {$event->deleted_at->format('Y-m-d')})");
                $event->forceDelete();
                $totalDeleted++;
            }
            $this->info("  ✅ Deleted {$deletedEvents->count()} events permanently");
        } else {
            $this->line("  ℹ️  No events to delete");
        }
        $this->newLine();
        
        // Summary
        $this->info("=" . str_repeat("=", 60));
        $this->info("🎉 Cleanup completed!");
        $this->info("Total records permanently deleted: {$totalDeleted}");
        $this->info("=" . str_repeat("=", 60));
        
        return Command::SUCCESS;
    }
}
