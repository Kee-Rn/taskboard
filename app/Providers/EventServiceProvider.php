<?php

namespace App\Providers;

use App\Events\TaskAssigned;
use App\Events\TaskStatusUpdated;
use App\Listeners\SendTaskAssignedNotification;
use App\Listeners\SendTaskStatusNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TaskAssigned::class => [
            SendTaskAssignedNotification::class,
        ],
        TaskStatusUpdated::class => [
            SendTaskStatusNotification::class,
        ],
    ];
}