<?php namespace CovidCoupons\App\Requests;

use CovidCoupons\App\Abstracts\Request;
use CovidCoupons\App\Exceptions\ValidationException;
use CovidCoupons\App\Contracts\Request as RequestInterface;

class ConfigFormRequest extends Request implements RequestInterface
{

    public function handle()
    {
        cvdapp()->csrf()->check();
        $this->validate();
        $this->saveOptions();
        return ['options' => $this->input()];
    }

    public function saveOptions()
    {
        $c = cvdapp()->config();
        $c->fill($this->input())->save();

        $all = $c->all();

        cvdapp()->debug('All values are:', $all);

        return $all;
    }

    public function build()
    {
        return [
             'stripe_secret'       => $this->postedString('stripe_secret'),
             'stripe_public'       => $this->postedString('stripe_public'),
             'liqpay_public'       => $this->postedString('liqpay_public'),
             'liqpay_secret'       => $this->postedString('liqpay_secret'),
             'default_gift_amount' => $this->postedFloat('default_gift_amount'),
             'locale'              => $this->postedString('locale'),
             'fallback_locale'     => $this->postedString('fallback_locale'),
             'seller_country'      => $this->postedString('seller_country'),
             'seller_email'        => $this->postedString('seller_email'),
             'seller_company_name' => $this->postedString('seller_company_name'),
             'currency'            => $this->postedString('currency'),
             'currency_symbol'     => $this->postedString('currency_symbol'),
             'email_required'      => $this->postedString('email_required'),
             'min_payment'         => $this->postedFloat('min_payment'),
             'max_payment'         => $this->postedFloat('max_payment'),
             'gateway_name'        => $this->postedString('gateway_name'),
        ];
    }

    public function validate()
    {
        return true;
    }
}