<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kavenegar\KavenegarApi;

class SendPostIndexingSMS extends Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phoneNumber;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($phoneNumber, $message)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
    }


    public function tags()
    {
        return ['user', 'token'];
    }

    public function toSms($notifiable)
    {
        $api = new KavenegarApi(config('kavenegar.apikey'));
        return $api->VerifyLookup($this->phoneNumber, $this->message, null, null, 'token');
    }
}
