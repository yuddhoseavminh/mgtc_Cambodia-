<?php

namespace App\Jobs;

use App\Http\Controllers\PublicApplicationController;
use App\Models\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendApplicationTelegramNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 90;

    public function __construct(public Application $application)
    {
    }

    public function handle(PublicApplicationController $notifier): void
    {
        $notifier->sendTelegramRegistrationNotification($this->application);
    }
}
