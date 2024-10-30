<?php namespace CovidCoupons;

use CovidCoupons\Adapters\WP\CSRF;
use CovidCoupons\Adapters\WP\Config;
use CovidCoupons\Adapters\WP\Database;
use CovidCoupons\Adapters\WP\DatabaseSchema;
use CovidCoupons\Adapters\WP\Log;
use CovidCoupons\Adapters\WP\Mailer;
use CovidCoupons\App\Contracts\CSRF as CSRFInterface;
use CovidCoupons\App\Contracts\Config as ConfigInterface;
use CovidCoupons\App\Contracts\Database as DatabaseInterface;
use CovidCoupons\App\Contracts\DatabaseSchema as DatabaseSchemaInterface;
use CovidCoupons\App\Contracts\Log as LogInterface;
use CovidCoupons\App\Contracts\Mailer as MailerInterface;
use CovidCoupons\App\ServiceProvider;
use CovidCoupons\WP\ActivationManager;
use CovidCoupons\WP\AdminManager;
use CovidCoupons\WP\AjaxManager;
use CovidCoupons\WP\ShortcodeManager;


class App extends ServiceProvider {

    protected $bind = [
        DatabaseInterface::class  =>  Database::class
    ];

    // Note that the order here matters.
    // E.g. the Stripe class depends on Config, so config needs to come first
    protected $singletons = [
        ConfigInterface::class          =>  Config::class,
        DatabaseSchemaInterface::class  =>  DatabaseSchema::class,
        MailerInterface::class          =>  Mailer::class,
        LogInterface::class             =>  Log::class,
        CSRFInterface::class            =>  CSRF::class,
    ];

    public function start()
    {
        // app()->config()->register();

        // load activation and uninstall hooks
        $a = new ActivationManager($this->root);

        // load api endpoints
        new AjaxManager();

        // loaa admin mgmt panel
        new AdminManager();

        // load the WP shortcode
        new ShortcodeManager();
    }

}