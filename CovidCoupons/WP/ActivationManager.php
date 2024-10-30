<?php
namespace CovidCoupons\WP;

use CovidCoupons\App\Contracts\Gateway;
use CovidCoupons\App\Contracts\Migrations;

class ActivationManager {

    protected $rootFile;
    protected $db;

    public function __construct( $root )
    {
        $this->rootFile = $root;
        \register_activation_hook( $this->rootFile, array(__CLASS__, 'activate'));
        \register_uninstall_hook( $this->rootFile, array(__CLASS__, 'deactivate'));
    }

    public static function activate()
    {
        $db = cvdapp()->resolve(Migrations::class);
        $db->createTable();

        static::setupPaymentGateway();
    }

    public static function deactivate()
    {
        $db = cvdapp()->resolve(Migrations::class);
        $db->deleteTable();
    }

    private static function setupPaymentGateway()
    {
        cvdapp()->gateway()->register();
    }
}