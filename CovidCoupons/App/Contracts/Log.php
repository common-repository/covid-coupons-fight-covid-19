<?php
namespace CovidCoupons\App\Contracts;

interface Log {

    public function debug($message, $array = null);

    public function log($message, $array = null);

}