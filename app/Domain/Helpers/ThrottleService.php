<?php

namespace App\Domain\Helpers;

class ThrottleService
{    
    const   BASE_THROTTLE_TEXT      = 'throttle: ',
            LOCAL_PROFILE_THROTTLE  = '100,10',
            PROD_PROFILE_THROTTLE   = '3,10';
    
    /**
     * Gets the throttle of the profile routes
     *
     * @return string
    */
    static public function getProfileThrottle(): string 
    {
        return self::BASE_THROTTLE_TEXT . (EnvService::isLocal() ? self::LOCAL_PROFILE_THROTTLE : self::PROD_PROFILE_THROTTLE);
    }
}