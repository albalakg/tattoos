<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Domain\Interfaces\IMails;
use Illuminate\Queue\SerializesModels;
use App\Domain\Emails\Models\LuEmailType;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteUserRequestMail extends Mailable implements IMails
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('אימות בקשת מחיקת משתמש')->view('mails.profile.deleteAccountRequest');
    }
            
    /**
     * @return int
    */
    static public function getTypeId(): int
    {
        return LuEmailType::DELETE_USER_REQUEST_EMAIL;
    }
}
