<?php
namespace CovidCoupons\Adapters\WP;

use CovidCoupons\App\Contracts\Config as ConfigInterface;
use CovidCoupons\WP\AjaxManager;

class Config implements ConfigInterface {

    public static $charge_action   = 'buy_covid_cert';
    public static $intent_action = 'intent_covid_cert';

    private $encryptKeys = ['ss', 'stripe_secret', 'ls', 'liqpay_secret'];

    /**
     * Overloaded data
     * @var [type]
     */
    private static $options;

    private $keys = [
        'ss' => 'stripe_secret',
        'sp' => 'stripe_public',
        'lp' => 'liqpay_public',
        'ls' => 'liqpay_secret',
        'da' => 'default_gift_amount',
        'lc' => 'locale',
        'fl' => 'fallback_locale',
        'sc' => 'seller_country',
        'se' => 'seller_email',
        'cn' => 'seller_company_name',
        'cc' => 'currency',
        'cs' => 'currency_symbol',
        'er' => 'email_required',
        'np' => 'min_payment',
        'xp' => 'max_payment',
        'gn' => 'gateway_name',
    ];

    private function secret1()
    {
        return 'my_simple_secret_key';
    }

    private function secret2()
    {
        return 'my_simple_secret_iv';
    }

    public function getSellerEmail($val)
    {
        return $val ?? \get_option('admin_email');
    }

    public function getCurrencySymbol($val)
    {
        return $val ?: '$';
    }

    public function getEmailRequired($val)
    {
        return $val ? 1 : 0;
    }

    public function setEmailRequired($val, $key)
    {
        return static::$options->$key = $val && $val !== 'false' && $val !== '0' ? 1 : 0;
    }

    public function getMinPayment($val)
    {
        return (int) $val ?: 1;
    }

    public function getMaxPayment($val)
    {
        return (int) $val ?: 1000;
    }

    public function getDefaultGiftAmount($val)
    {
        return (int) $val ?: 50;
    }

    public function debug()
    {
        return WP_DEBUG;
    }

    public function siteUrl()
    {
        return get_site_url();
    }

    public function getIntentEndpoint()
    {
        return $this->ajaxUrl(static::$intent_action);
    }

    public function getChargeEndpoint()
    {
        return $this->ajaxUrl(static::$charge_action);
    }

    public function getGatewayName($val)
    {
        return $val ?: 'stripe';
    }

    protected function ajaxUrl($action)
    {
        return \admin_url( 'admin-ajax.php' ) . "?action={$action}";
    }

    private function encrypt($string)
    {
        // return $string;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $this->secret1());
        $iv = substr( hash( 'sha256', $this->secret2() ), 0, 16 );
        return base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }

    private function decrypt($string)
    {
        // return $string;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $this->secret1());
        $iv = substr( hash( 'sha256', $this->secret2() ), 0, 16 );
        $decrypted = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        return $decrypted;
    }

    private function camelCase($string, $dontStrip = []){
        return implode('', array_map('ucfirst', explode('_', $string)));
    }

    private function snakeCase($string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    private function shortKey($key)
    {
        if (in_array($key, $this->keys)) {
            $key = array_search($key, $this->keys);
        }
        return $key;
    }

    private function longKey($key)
    {
        return isset($this->keys[$key]) ? $this->keys[$key] : $key;
    }

    private function shouldBeEncrypted($key)
    {
        return in_array($key, $this->encryptKeys);
    }

    private function fetch()
    {
        $opt = json_decode(\get_option('cvdapp'));
        if (!$opt) {
            $keys = array_keys($this->keys);
            $opt = (object)[];
            foreach ($keys as $key) {
                $opt->$key = null;
            }
        }
        static::$options = $opt;
    }

    public function save()
    {
        \update_option('cvdapp', json_encode(static::$options));
        return $this;
    }

    public function fill($settings)
    {
        if (!$this->isAdmin()) {
            exit;
        }
        foreach ($settings as $key => $val) {
            $val = $val === 'null' ? null : $val;
            $this->__set($key, $val);
        }
        return $this;
    }

    public function all()
    {
        if (!$this->isAdmin()) {
            exit;
        }

        $longKeys = array_map(function ($shortKey) {
            return $this->longKey($shortKey);
        }, array_keys( (array) static::$options));

        $out = [];
        foreach ($longKeys as $key) {
            $out[$key] = $this->__get($key);
        }
        return $out;
    }

    public function isAdmin()
    {
        return function_exists('wp_get_current_user') ? \current_user_can('manage_options') : null;
    }

    public function __get($attribute)
    {
        if (!static::$options) {
            $this->fetch();
        }

        $key = $this->shortKey($attribute);
        $val = isset(static::$options->$key) ? static::$options->$key : null;
        $method = 'get'.$this->camelCase($attribute);
        if (method_exists($this, $method)) {
            return $this->$method($val);
        }
        return $val && $this->shouldBeEncrypted($attribute) ? $this->decrypt($val) : $val;
    }

    public function __set($attribute, $value)
    {
        if (property_exists($this, $attribute)) {
            $this->$attribute = $value;
        }

        if (!static::$options) {
            $this->fetch();
        }

        $k = $this->shortKey($attribute);

        $method = 'set'.$this->camelCase($attribute);
        if (method_exists($this, $method)) {
            return $this->$method($value, $k);
        }

        static::$options->$k = $value && $this->shouldBeEncrypted($attribute) ? $this->encrypt($value) : $value;
    }

    public function __call($function, $args)
    {
        if (substr($function, 0,3) === 'get') {
            $str = $this->snakeCase(str_replace('get', '', $function));
            return $this->__get($str);
        }

        if (substr($function, 0,3) === 'set') {
            $str = $this->snakeCase(str_replace('set', '', $function));
            return $this->__set($str, $args[0]);
        }
    }

}