<?php namespace CovidCoupons\App\Services;

use CovidCoupons\App\Contracts\Config;
use CovidCoupons\App\Contracts\Gateway;
use CovidCoupons\App\Exceptions\PaymentException;
use CovidCoupons\App\Exceptions\PaymentNextStep;

/**
 * @todo  - Still building
 *
 * Add to composer: liqpay/liqpay": "^1.2"
 */
class LiqPay implements Gateway
{

    private $version = 3;
    private $liqpay;

    public function __construct( Config $config)
    {
        $this->liqpay = new \LiqPay($config->liqpay_public, $config->liqpay_secret);
    }

    private function checkSignature($signature, $data)
    {
        return $this->createSignature($data) === $signature;
    }

    private function createSignature($data)
    {
        $key = cvdapp()->config()->liqpay_secret;
        return base64_encode(sha1($key.$data.$key,1));
    }

    public function createIntent($amount, $currency, $meta)
    {
        // somehow create an intent_id

        try {
            $html = $this->liqpay->cnb_form(array(
                'action'         => 'pay',
                'amount'         => $amount,
                'currency'       => $currency,
                'description'    => 'Gift Certificate',
                'order_id'       => $intent_id,
                'version'        => $this->version
            ));

        } catch (\Exception $e) {
            $this->handleException($e);
        }

        return $html;
    }

    public function checkIntent($intent_id, $amount, $currency)
    {

        try {

            $res = $this->liqpay->api("request", array(
                'action'        => 'status',
                'version'       => $this->version,
                'order_id'      => $intent_id
            ));


        } catch (\Except $e) {

            // cvdapp()->debug('Handling api exception:' . $e->getMessage());

            $this->handleException($e);
        }

        // cvdapp()->debug('got passed the intent.');

        if ($res->amount !== $amount) {
            throw new PaymentException('errors.payment');
        }

        if (strtolower($res->currency) !== strtolower($currency)) {
            throw new PaymentException('errors.payment');
        }


        // $this->checkForNextAction($intent);

        if ($res->status !== 'success') {
            throw new PaymentException('errors.payment');
        }

        return $res;
    }

    public function register()
    {
        // nothing here
    }

    private function checkForNextAction($intent)
    {
        // if ($intent->status === 'requires_action' && $intent->next_action->type == 'use_stripe_sdk') {
        //     throw new PaymentNextStep([
        //         'requires_action' => true,
        //         'payment_intent_client_secret' => $intent->client_secret
        //     ]);
        // }
    }

    private function handleException($e)
    {
        $msg = $e->getMessage();
        cvdapp()->log('Error: ' . $msg);
        
        if (strpos($msg, 'Invalid API Key') !== false) {
            $trans = 'errors.missing-key';
        } elseif (strpos($msg, 'previously confirmed') !== false ) {
            cvdapp()->log('Previously confirmed...');
            return;
        } else {
            $trans = 'errors.gateway';
        }


        throw new PaymentException($trans, null, $e);
    }


} ?>