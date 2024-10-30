<?php namespace CovidCoupons\App\Requests;

use CovidCoupons\App\Abstracts\Request;
use CovidCoupons\App\Contracts\Gateway;
use CovidCoupons\App\Contracts\GiftCertificate;
use CovidCoupons\App\Exceptions\ValidationException;
use CovidCoupons\App\Contracts\Request as RequestInterface;

class IntentFormRequest extends Request implements RequestInterface
{

    public function handle()
    {
        cvdapp()->csrf()->check();
        
        $this->validate();

        $code = $this->getIntentCode();

        return ['token' => $code];
    }

    protected function getIntentCode()
    {
        $cur  = $this->get('currency');
        $amt  = $this->get('amount');
        $meta = $this->get('meta');

        return cvdapp()->gateway()->createIntent( $amt, $cur, $meta);
    }

    public function build()
    {
        return [
            'amount'   => $this->postedFloat('u_amount'),
            'currency' => $this->postedString('u_currency'),
            'meta'     => $this->postedArray('u_meta')
        ];
    }

    public function validate()
    {
        $config = cvdapp()->config();

        $amount = $this->get('amount');
        if (!$amount) {
            throw new ValidationException('validation.amount.required');
        }
        $min = $config->min_payment;
        if ($amount < $min) {
            throw new ValidationException('validation.amount.min', ['min' => $min]);   
        }
        $max = $config->max_payment;
        if ($amount > $max) {
            throw new ValidationException('validation.amount.max', ['max' => $max]);   
        }

        $currency = strtolower($this->get('currency'));
        if (!$currency) {
            throw new ValidationException('validation.currency.required');
        }
        if (strlen($currency) !== 3 || strtolower($config->currency) !== $currency) {
            throw new ValidationException('validation.currency.valid');
        }

        return true;
    }
}