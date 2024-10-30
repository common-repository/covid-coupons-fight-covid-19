<?php
namespace CovidCoupons\WP;

class ShortcodeManager {

    const SHORTCODE = 'sell-gift-certificates';
    protected $version;

    public function __construct()
    {
        $root = cvdapp_root();
        $this->root = \plugin_dir_url($root);
        $this->version = cvdapp_version();
        $this->attach_hooks();
    }

    public function handle( $atts, $content = null )
    {
        return $this->enqueue()->html();
    }

    protected function enqueue()
    {
        $dependencies = array();
        \wp_enqueue_script( 'CovidCoupons', $this->root . 'dist/index.js', [], $this->version );
        // \wp_enqueue_style('CovidCoupons', $this->root . 'dist/style.css', [], $this->version );
        $obj = $this->localizeScript();
        if ( $obj ) {
            \wp_localize_script('CovidCoupons', 'ajax_object', $obj);
        }
        return $this;
    }

    protected function attach_hooks()
    {
        \add_shortcode(static::SHORTCODE, array($this, 'handle'));
    }

    protected function html()
    {
        return '<div id="cvdapp"></div>';
    }

    protected function localizeScript()
    {
        $conf = cvdapp()->config();
        $csrf = cvdapp()->csrf();

        return [
            'csrf_data'         => $csrf->getData(),
            'csrf_field'        => $csrf->getField(),
            
            'endpoint_intent'   => $conf->getIntentEndpoint(),
            'endpoint_save'     => $conf->getChargeEndpoint(),
            
            'locale'            => $conf->locale,
            'locale_fallback'   => $conf->fallback_locale,
            
            'company'           => $conf->seller_company_name,
            'default_amount'    => $conf->default_gift_amount,
            'seller_country'    => $conf->seller_country,
            'currency'          => $conf->currency,
            'currency_symbol'   => $conf->currency_symbol,
            'stripe_public_key' => $conf->stripe_public,
            'email_required'    => $conf->email_required,
        ];
    }

}