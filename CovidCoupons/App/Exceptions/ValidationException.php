<?php namespace CovidCoupons\App\Exceptions;

use CovidCoupons\App\Contracts\Exception;

class ValidationException extends \RuntimeException implements Exception
{

    protected $trans;
    protected $other;

    public function __construct($translationKey, $other = null)
    {
        $this->trans = $translationKey;
        $this->other = $other;

        parent::__construct('The data was not valid.', 422);
    }

    public function toArray()
    {
        return [
            'trans' => $this->trans,
            'other' => $this->other ?: []
        ];
    }
}