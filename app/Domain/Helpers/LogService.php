<?php

namespace App\Domain\Helpers;

use Exception;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\Log;

class LogService
{    
    const SEPARATOR       = ' | ',
          DEFAULT_CHANNEL = 'custom';

    /**
     * Log object
     *
     * @var Log
    */
    private $log;

    /**
     * User object
     *
     * @var User
    */
    private $user;
    
    /**
     * Log content
     *
     * @var string
    */
    private $log_meta_data = self::SEPARATOR;
    
    /**
     * Start a logger channel
     *
     * @param string $channel
     * @return void
    */ 
    public function __construct(string $channel = self::DEFAULT_CHANNEL, User $user = null)
    {
        $this->log = Log::channel($channel);
        $this->user = $user;
        $this->setMetaData();
    }
    
    /**
     * Create a info log
     *
     * @param string $content
     * @return string|null
    */
    public function info(string $content) :?string
    {
       return $this->writeLog($content, 'info');
    }
    
    /**
     * Create a error log
     *
     * @param string $content
     * @return string|null
    */
    public function error(string $content) :?string
    {
       return $this->writeLog($content, 'error');
    }
    
    /**
     * Create a critical log
     *
     * @param string $content
     * @return string|null
    */
    public function critical(string $content) :?string
    {
        // TODO: Maybe send an email
       return $this->writeLog($content, 'critical');
    }
    
    /**
     * Create a warning log
     *
     * @param string $content
     * @return string|null
    */
    public function warning(string $content) :?string
    {
       return $this->writeLog($content, 'warning');
    }
    
    /**
     * Create a debug log
     *
     * @param string $content
     * @return string|null
    */
    public function debug(string $content) :?string
    {
       return $this->writeLog($content, 'debug');
    }
    
    /**
     * Write the log
     * 
     * @param string $content
     * @param string $action
     * @return string|null
    */
    private function writeLog(string $content, $action) :?string
    {
        try {
            $full_log_content = $content . $this->log_meta_data;
            $this->log->$action($full_log_content);
            return $full_log_content;
        } catch(Exception $ex) {
            Log::channel(self::DEFAULT_CHANNEL)->critical($ex->getMessage());
            // TODO: Send a system email
            return null;
        } 
    }

    private function setMetaData()
    {
        try {
            $this->writeUser();
            $this->writeIpAddress();
            $this->writeBrowser();
            $this->writeURL();
        } catch (Exception $ex) {
            Log::channel(self::DEFAULT_CHANNEL)->critical($ex->getMessage());
            // TODO: Send a system email
        }
    }

    private function writeUser()
    {
        $this->log_meta_data .= 'USER: ' . $this->getUser() . self::SEPARATOR;
    }

    private function writeIpAddress()
    {
        $this->log_meta_data .= 'IP: ' . request()->ip() . self::SEPARATOR;
    }

    private function writeBrowser()
    {
        $this->log_meta_data .= 'BROWSER: ' . request()->header('user-agent') . self::SEPARATOR;
    }

    private function writeURL()
    {
        $this->log_meta_data .= 'URL: ' . request()->server('HTTP_REFERER') . self::SEPARATOR;
    }

    private function getUser()
    {
        return $this->user ? $this->user->id : 'GUEST';
    }

}