<?php

namespace CovidCoupons\WP;

use CovidCoupons\Adapters\WP\Config;
use CovidCoupons\App\Contracts\Request;
use CovidCoupons\App\Requests\IntentFormRequest;
use CovidCoupons\App\Requests\PaymentFormRequest;

class AjaxManager {

    public function __construct()
    {
        $this->on(Config::$intent_action, 'handleIntent');
		$this->on(Config::$charge_action, 'handleCharge');
    }

    protected function on($action, $method)
    {
        \add_action('wp_ajax_'.$action, [$this, $method]);
        \add_action('wp_ajax_nopriv_'.$action, [$this, $method]);
    }

    public function handleIntent()
    {
        $response = $this->request( new IntentFormRequest );
        return $this->respond($response);
    }

    public function handleCharge()
    {
        $response = $this->request( new PaymentFormRequest );
        return $this->respond($response);
    }


    private function respond($data)
    {
        \wp_send_json_success($data);
        \wp_die();
    }

    private function request(Request $request)
    {
        try {
            return $request->handle();
        } catch (\Exception $e) {
            if ($e instanceof \CovidCoupons\App\Contracts\Exception) {
                \wp_send_json_error($e->toArray(), $e->getCode());
                \wp_die();
            } else {
                throw $e;
            }
        }
    }

    public static function export( $csv, $filename )
    {
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m-d") . ".csv");
        header( "Content-disposition: filename=".$filename.".csv");
        print $csv;
        exit;
    }

}