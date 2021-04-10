<?php

namespace App\Jobs\Helpers;

use Illuminate\Bus\Queueable;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MailPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mail;
    
    private $data;
    
    private $receivers;

    /**
     * Create a new job instance.
     * @param \Illuminate\Contracts\Mail\Mailable $mail
     * @param object $data
     * @param array|string $receivers
     * 
     * @return void
    */
    public function __construct(string $mail, object $data, $receivers)
    {
        $this->mail = $mail;
        $this->data = $data;
        $this->receivers = $receivers;
    }

    /**
     * Execute the job.
     *
     * @return void
    */
    public function handle()
    {
        Mail::to($this->receivers)->send(new $this->mail($this->data));
    }

    public function failed($exception)
    {
        LogService::critical($exception->getMessage(), 'mail');
    }
}
