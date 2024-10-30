<?php

namespace CovidCoupons\WP;

use CovidCoupons\App\Requests\ConfigFormRequest;
use CovidCoupons\WP\AbstractAdminPage;
use CovidCoupons\WP\AjaxManager;
use CovidCoupons\WP\ModelListTable;
use CovidCoupons\WP\ShortcodeManager;

class AdminOptionsPage extends AbstractAdminPage {

    public static $action = 'covid-coupons-options';
    public static $title  = 'Options';
    public static $slug   = 'covid-coupons-options';

    protected $endpoints = [
        'fetch_options' => 'fetchConfigOptions',
        'save_options' => 'saveConfigOptions',
    ];

    protected function enqueue()
    {
        $this->addScript('dist/options.js', $this->getAjaxObject());
        // $this->addStyle();
    }

    public function handle()
    {

        echo '<h1>CovidCoupons Options</h1>';
        
        $messages = cvdapp()->gateway()->getInstallationMessages();
        foreach ($messages as $message) {
            echo '<div class="notice notice-warning"><p>' . $message . '</p></div>';
        }

        echo '<div style="padding: 30px">';
        echo '<h2>Instructions</h2>';
        echo '<p>Covid Coupons are a means to support small businesses in your area that may be struggling due to closures and isolation of COVID-19. If you need ' . 
            'any help getting started, contact me on <a href="http://lambroschini.com/" target="_blank">my website</a>.</p>';
        echo '<ol>';
        echo '<li>If you do not have one already, contact your host to install a SSL certificate, so that you can access your website at <strong>https://</strong>.</li>';
        echo '<li>Sign up for a <a href="https://stripe.com/" target="_blank">Stripe.com</a> account to accept credit card payments.</li>';
        echo '<li>Complete the options below, including entering the API keys given by Stripe.</li>';
        echo '<li>Enter the shortcode <code>['. ShortcodeManager::SHORTCODE.']</code> in any page or post to allow visitors to buy Covid Coupons (gift certificates). ' . 
            'You can otherwise customize the page using text or photos like a normal WordPress pagee.</li>';
        echo '<li>After purchasing a coupon, the coupon code will be displayed on the screen, and emailed to the WordPress admin and the visitor\'s email address, if given.</li>';
        echo '<li>Track the coupons sold using the Admin table in WordPress. Be sure to check the payments went through on your Stripe account.</li>';
        echo '<li>When the COVID-19 crisis is finished in your area, let your customers cash in their Covid Coupons in your store or restaurant.</li>';
        echo '</ol>';

        echo '<div id="cvdapp"></div></div>';
    }

    public function fetchConfigOptions()
    {
        $options = cvdapp()->config()->all();
        return $this->respond($options);
    }

    public function saveConfigOptions()
    {
        // cvdapp()->debug('Saving options...');
        $response = (new ConfigFormRequest)->handle();
        return $this->respond($response);
    }

}
