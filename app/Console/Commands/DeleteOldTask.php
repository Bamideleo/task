<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;

class DeleteOldTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete 30 days old tasks';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Task::whereDate('created_at', '<', Carbon::now()->subDays(30))
        ->delete();
    }
}
