<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-unverified {--dry-run : Show what would be deleted without actually deleting} {--days=7 : Days to wait before deleting unverified users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unverified users older than specified days (default: 7 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days') ?? 7;
        $cutoffDate = Carbon::now()->subDays($days);

        // البحث عن المستخدمين غير المؤكدين الأقدم من المدة المحددة
        $query = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate);

        $count = $query->count();

        if ($count === 0) {
            $this->info("No unverified users found older than {$days} days.");
            return 0;
        }

        if ($this->option('dry-run')) {
            $this->info("Found {$count} unverified users older than {$days} days that would be deleted:");
            $this->newLine();

            $users = $query->get(['id', 'full_name', 'email', 'user_type', 'created_at']);

            $this->table(
                ['ID', 'Full Name', 'Email', 'Type', 'Created At'],
                $users->map(function ($user) {
                    return [
                        $user->id,
                        $user->full_name,
                        $user->email,
                        $user->user_type,
                        $user->created_at->format('Y-m-d H:i:s')
                    ];
                })->toArray()
            );

            $this->newLine();
            $this->warn("This was a DRY RUN. No users were actually deleted.");
            $this->info("Run without --dry-run to actually delete these users.");

            return 0;
        }

        // طلب التأكيد من المستخدم
        if (!$this->confirm("Are you sure you want to delete {$count} unverified users older than {$days} days?")) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        // حذف المستخدمين
        $deleted = $query->delete();

        $this->info("Successfully deleted {$deleted} unverified users.");

        // تسجيل العملية في الـ log
        Log::info("Cleanup: Deleted {$deleted} unverified users older than {$days} days", [
            'command' => 'users:cleanup-unverified',
            'days' => $days,
            'deleted_count' => $deleted,
            'executed_at' => now()
        ]);

        // إحصائيات إضافية
        $remainingUnverified = User::whereNull('email_verified_at')->count();
        $totalUsers = User::count();

        $this->newLine();
        $this->info("Statistics after cleanup:");
        $this->line("- Total users: {$totalUsers}");
        $this->line("- Remaining unverified users: {$remainingUnverified}");
        $this->line("- Verification rate: " . round((($totalUsers - $remainingUnverified) / max($totalUsers, 1)) * 100, 2) . "%");

        return 0;
    }
}
