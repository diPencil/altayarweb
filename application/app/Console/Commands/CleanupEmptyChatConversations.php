<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupEmptyChatConversations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cleanup-empty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup empty guest chat conversations with no messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up empty guest chat conversations...');

        $count = \App\Models\ChatConversation::whereNull('user_id')
            ->whereDoesntHave('messages')
            ->whereNull('human_requested_at')
            ->where('created_at', '<', now()->subHours(24))
            ->delete();

        $this->info("Deleted {$count} empty guest conversations.");
    }
}
