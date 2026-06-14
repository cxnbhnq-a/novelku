<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ActivityLog;

class CleanupActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:cleanup {--days=30 : Hapus logs lebih tua dari X hari}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus activity logs yang sudah lama';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        
        $this->info("Menghapus activity logs lebih tua dari {$days} hari...");

        try {
            $deletedCount = ActivityLog::deleteOldLogs($days);
            
            $this->info("✅ Berhasil menghapus {$deletedCount} log.");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
