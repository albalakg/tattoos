<?php

namespace App\Domain\Emails\Models;

use App\Domain\Emails\Models\EmailsSent;
use Illuminate\Database\Eloquent\Model;

class LuEmailType extends Model
{
    const SUPPORT_TICKET_MESSAGE_EMAIL          = 1;
    const SUPPORT_TICKET_MESSAGE_EMAIL_TEXT     = 'Support Ticket Message Created';
    const SUPPORT_TICKET_EMAIL                  = 2;
    const SUPPORT_TICKET_EMAIL_TEXT             = 'Support Ticket Created';
    const FORGOT_PASSWORD_EMAIL                 = 3;
    const FORGOT_PASSWORD_EMAIL_TEXT            = 'Forgot Password Created';
    const EMAIL_VERIFICATION_EMAIL              = 4;
    const EMAIL_VERIFICATION_EMAIL_TEXT         = 'Email Verification Created';
    const APPLICATION_ERROR_EMAIL               = 5;
    const APPLICATION_ERROR_EMAIL_TEXT          = 'Application Error Created';
    const ORDER_STATUS_UPDATE_EMAIL             = 6;
    const ORDER_STATUS_UPDATE_EMAIL_TEXT        = 'Order Status Update Created';
    const ADD_COURSE_TO_USER_EMAIL              = 7;
    const ADD_COURSE_TO_USER_EMAIL_TEXT         = 'Add Course To User Created';
    const DELETE_USER_REQUEST_EMAIL             = 8;
    const DELETE_USER_REQUEST_EMAIL_TEXT        = 'Delete User Request Created';
    const UPDATE_EMAIL_REQUEST_EMAIL            = 9;
    const UPDATE_EMAIL_REQUEST_EMAIL_TEXT       = 'Update Email Request Created';
    const COURSE_HAS_BEEN_EXPIRED_EMAIL         = 10;
    const COURSE_HAS_BEEN_EXPIRED_EMAIL_TEXT    = 'Course Has been Expired';

    public function emails()
    {
        return $this->hasMany(EmailsSent::class, 'email_type_id', 'id');
    }
}