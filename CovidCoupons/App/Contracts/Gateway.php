<?php
namespace CovidCoupons\App\Contracts;

interface Gateway {

    public function createIntent($amount, $currency, $meta);

    public function checkIntent($intent_id, $amount, $currency);

    public function register();

}