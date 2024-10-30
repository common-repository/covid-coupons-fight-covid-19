<?php
namespace CovidCoupons\Adapters\WP;

use CovidCoupons\Adapters\WP\Config;
use CovidCoupons\App\Contracts\Log as LogInterface;

class Log implements LogInterface {

    public function debug($message, $array = null)
    {
        if (cvdapp()->config()->debug()) {
            $this->log($message, $array);
        }
    }

    public function log($message, $array = null)
    {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
        if ( $array ) {
          error_log( var_export($array, true) );
        }
    }

}