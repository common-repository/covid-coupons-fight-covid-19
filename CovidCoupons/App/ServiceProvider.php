<?php namespace CovidCoupons\App;

use CovidCoupons\App\Container;
use CovidCoupons\App\Contracts\CSRF;
use CovidCoupons\App\Contracts\Config;
use CovidCoupons\App\Contracts\Gateway;
use CovidCoupons\App\Contracts\Log;
use CovidCoupons\App\Services\Stripe;

class ServiceProvider {

    protected $root;
    protected $bind = [];
    protected $singletons = [];
    protected static $instance;

    public $container;

    public function __construct($root)
    {
        $this->root = $root;

        $this->container = Container::getInstance();

        foreach ($this->bind as $interface => $class) {
            $this->container->bind($interface, $class);
        }

        foreach ($this->singletons as $interface => $class) {
            $this->container->singleton($interface, $class);
        }

        $this->container->bind(\CovidCoupons\App\Contracts\GiftCertificate::class, \CovidCoupons\App\GiftCertificate::class);

        $this->container->singleton(\CovidCoupons\App\Contracts\Migrations::class, \CovidCoupons\App\Migrations::class);

        $this->registerGateway();
    }

    public function resolve($class)
    {
        return $this->container->resolve($class);
    }

    public function log($message, $array = null)
    {
        return $this->resolve(Log::class)->log($message, $array);
    }

    public function config()
    {
        return $this->resolve(Config::class);
    }

    public function csrf()
    {
        return $this->resolve(CSRF::class);
    }

    public function debug($message, $array = null)
    {
        return $this->resolve(Log::class)->debug($message, $array);
    }

    public function domain()
    {
        $protocols = array('http://', 'http://www.', 'www.');
        return str_replace($protocols, '', $this->config()->siteUrl());
    }

    protected function registerGateway()
    {
        $gatewayName = $this->config()->gateway_name;

        switch ($gatewayName) {
            case 'stripe':
            default:
                $className = Stripe::class;
                break;
        }

        $this->container->singleton(Gateway::class, $className);
    }

    public function gateway()
    {
        return  $this->resolve(Gateway::class);
    }

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance($root)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static($root);
        }

        return static::$instance;
    }




}