<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PermissionRegistry;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync';
    protected $description = 'Sync all defined system permissions into the database';

    public function handle()
    {
        $this->info('Syncing system permissions...');

        PermissionRegistry::sync();

        $this->info('Permissions synchronized successfully!');
    }
}
