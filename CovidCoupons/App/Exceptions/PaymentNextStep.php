<?php namespace CovidCoupons\App\Exceptions;

use CovidCoupons\App\Contracts\Exception;

class PaymentNextStep extends \RuntimeException implements Exception
{

    protected $array;

    public function __construct($array)
    {
        $this->array;

        parent::__construct($e->getMessage(), 200, $e);
    }

    public function toArray()
    {
        return $this->array;
    }
}