<?php
/*
  Plugin Name: Covid Coupons - Fight Covid-19
  Version: 1.3.0
  Plugin URI: https://lambroschini.com/covid-coupons/
  Description: Sell Covid Coupons from your website. Help small businesses to make it through this tough time.
  Author: Erin Lambroschini
  Author URI: http://lambroschini.com
 */

// require_once __DIR__ . '/.env.php';
require_once __DIR__ . '/vendor/autoload.php';

if (!defined('COVID_COUPONS_ROOT')) {
  define('COVID_COUPONS_ROOT', dirname(__FILE__));
}

if (!defined('COVID_COUPONS_HOME')) {
  if ( ! function_exists( 'get_home_path' ) ) {
      include_once ABSPATH . '/wp-admin/includes/file.php';
  }
  define('COVID_COUPONS_HOME', \get_home_path());
}

if (!function_exists('cvdapp')) {
    function cvdapp()
    {
        return \CovidCoupons\App::getInstance(__FILE__);
    }
} 
    elseif ( !(cvdapp() instanceof \CovidCoupons\App))
{
    throw new Exception('There is a clash with another plugin. This plugin needs to use function `cvdapp()`');
}

if (!function_exists('cvdapp_version')) {
    function cvdapp_version()
    {
       $vfile = __dir__.'/version';
       $version = trim( (file_exists($vfile) ? file_get_contents($vfile) : null) ?? 'unknown');
       return $version;
    }
}

if (!function_exists('cvdapp_root')) {
    function cvdapp_root()
    {
       return __FILE__;
    }
}

cvdapp()->start();