<?php namespace CovidCoupons\App\Exceptions;

use CovidCoupons\App\Contracts\Exception;

class PaymentException extends \RuntimeException implements Exception
{

    protected $trans;
    protected $other;

    public function __construct($translationKey, $other = null, $e = null)
    {
        $this->trans = $translationKey;
        $this->other = $other;

        parent::__construct( $e ? $e->getMessage() : '', 400, $e);
    }

    public function toArray()
    {
        return [
            'trans' => $this->trans,
            'other' => $this->other
        ];
    }
}