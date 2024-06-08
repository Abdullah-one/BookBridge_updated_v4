<?php

namespace App\Jobs;

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class sendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $notification;
    public function __construct($notification)
    {
        $this->notification=$notification;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notificationController=new NotificationController();
        $notificationController->create($this->notification);
    }
}
