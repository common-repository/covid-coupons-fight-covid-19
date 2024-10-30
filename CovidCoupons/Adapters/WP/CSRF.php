<?php
namespace CovidCoupons\Adapters\WP;

use CovidCoupons\App\Contracts\CSRF as CSRFInterface;

class CSRF implements CSRFInterface {

    const NONCE_NAME = 'fight-covid-19-shortcode';
    const NONCE_FIELD = 'nonce';

    public function getField()
    {
        return static::NONCE_FIELD;
    }

    public function getData()
    {
        return \wp_create_nonce(static::NONCE_NAME);
    }

    public function check()
    {
        \check_ajax_referer(static::NONCE_NAME, static::NONCE_FIELD);
    }

}