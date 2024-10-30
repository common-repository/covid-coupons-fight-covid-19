<?php namespace CovidCoupons\App\Services;

use CovidCoupons\App\Contracts\Config;
use CovidCoupons\App\Contracts\Gateway;
use CovidCoupons\App\Exceptions\PaymentException;
use CovidCoupons\App\Exceptions\PaymentNextStep;

class Stripe implements Gateway
{

    public function __construct( Config $config)
    {
        // @todo - Setup API key @config
        \Stripe\Stripe::setApiKey( $config->getStripeSecret() );        
    }

    public function createIntent($amount, $currency, $meta)
    {
        // cvdapp()->debug('Creating stripe intent... for ' . $currency . ' ' . $amount);

        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount'   => $amount * 100,
                'currency' => $currency,
                // Verify your integration in this guide by including this parameter
                'metadata' => ['integration_check' => 'accept_a_payment'],
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            $this->handleException($e);
        }

        return $intent->client_secret;
    }

    public function checkIntent($intent_id, $amount, $currency)
    {

        try {

            $intent = \Stripe\PaymentIntent::retrieve($intent_id);
            // $confirmed = $intent->confirm();
            // cvdapp()->debug('Confirmed:', $confirmed);


        } catch (\Stripe\Exception\ApiErrorException $e) {

            // cvdapp()->debug('Handling api exception:' . $e->getMessage());

            $this->handleException($e);
        }

        // cvdapp()->debug('got passed the intent.');

        if ($intent->amount !== $amount * 100) {
            throw new PaymentException('errors.payment');
        }

        if ($intent->currency !== strtolower($currency)) {
            throw new PaymentException('errors.payment');
        }


        $this->checkForNextAction($intent);

        if ($intent->status !== 'succeeded') {
            throw new PaymentException('errors.payment');
        }

        return $intent;
    }

    public static function getHomePath()
    {
        return COVID_COUPONS_HOME;
    }

    public static function getCopyFromFile()
    {
        return COVID_COUPONS_ROOT . '/apple-pay-domain-file';
    }

    public static function getCopyToFile()
    {
        return static::getWellKnownPath().'apple-developer-merchantid-domain-association';
    }

    public static function getWellKnownPath()
    {
        return static::getHomePath().'.well-known/';
    }

    public function getInstallationMessages()
    {
        $url = 'https://stripe.com/docs/apple-pay/web/v2#going-live';
        $messages = [];
        $path = static::getWellKnownPath();
        if (!is_dir($path)) {
            $messages[] = "Covid Coupon failed to create a new directory: <strong>{$path}</strong>. You can create the directory yourself. " . 
            "See <a href=\"{$url}\" target=\"_blank\">Stripe Docs</a>.";
        }
        if (!file_exists(static::getCopyToFile())) {
            $from = static::getCopyFromFile();
            $to = static::getCopyToFile();
            $messages[] = "Covid Coupon failed to copy your developer file for an Apple Pay button. " . 
                'You can do this manually by copying the file at <br /><strong><code>'.$from.'</code></strong><br> to the server location <br><strong><code>'.$to.'</code></strong>.<br> ' . 
                'See <a href="'.$url.'" target="_blank">Stripe Docs</a>.';
        }

        $result = static::registerApplePayDomain();

        if (!$result || !$result->id) {
            $messages[] = 'Covid Coupon failed to register your domain with Stripe in order to show an ApplePay or GPay button. You can register your domain on your Stripe dashboard, or try again.';
        }


        return $messages;
    }

    public function register($root = null)
    {
        // Move /wp/CovidCoupons/apple-pay-domain-file to be available from 
        // https://example.com/.well-known/apple-developer-merchantid-domain-association
        
        $success1 = false;
        $success2 = false;
        $success3 = false;
        try {
            $dir = static::getWellKnownPath();
            if (!is_dir($dir)) {
                if (!mkdir($dir, 755, true)) {
                    // $this->addDirFailedWarning($dir);
                } 
            } 
            $success1 = is_dir($dir);
            if ($success1) {
                $from = static::getCopyFromFile();
                $to = static::getCopyToFile();
                if (!file_exists(($to))) {
                    try {
                        copy( $from, $to);
                    } catch (\Throwable $e) {
                        // $this->copyFileFailedWarning($from, $to);
                    } catch (\Exception $e) {
                        // $this->copyFileFailedWarning($from, $to);
                    }
                }
                $success2 = file_exists($to);
            }
        } catch (\Throwable $e) {
            // $this->addDirFailedWarning($dir);
        } catch (\Exception $e) {
            // $this->addDirFailedWarning($dir);
        }
        $success3 = static::registerApplePayDomain();
        cvdapp()->config()->installed = $success1 && $success2 && $success3 ? true : false;
        cvdapp()->config()->save();
    }

    public static function registerApplePayDomain()
    {
        try {            
            // See https://stripe.com/docs/stripe-js/elements/payment-request-button#verifying-your-domain-with-apple-pay
            return \Stripe\ApplePayDomain::create([ 'domain_name' => cvdapp()->domain() ]);
        } catch (\Throwable $e) {
            // $this->addDomainFailedWarning($e->getMessage());
        } catch (\Exception $e) {
            // $this->addDomainFailedWarning($e->getMessage());
        }
    }

    private function checkForNextAction($intent)
    {
        if ($intent->status === 'requires_action' && $intent->next_action->type == 'use_stripe_sdk') {
            throw new PaymentNextStep([
                'requires_action' => true,
                'payment_intent_client_secret' => $intent->client_secret
            ]);
        }
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