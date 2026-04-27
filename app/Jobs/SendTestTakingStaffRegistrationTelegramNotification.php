<?php

namespace App\Jobs;

use App\Http\Controllers\PublicTestTakingStaffRegistrationController;
use App\Models\TestTakingStaffRegistration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendTestTakingStaffRegistrationTelegramNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 90;

    public function __construct(public TestTakingStaffRegistration $registration)
    {
    }

    public function handle(PublicTestTakingStaffRegistrationController $notifier): void
    {
        $notifier->sendTelegramRegistrationNotification($this->registration);
    }
}
