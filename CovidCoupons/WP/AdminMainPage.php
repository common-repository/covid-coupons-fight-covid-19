<?php

namespace CovidCoupons\WP;

use CovidCoupons\App\Requests\CertificateRequest;
use CovidCoupons\WP\AbstractAdminPage;
use CovidCoupons\WP\ModelListTable;

class AdminMainPage extends AbstractAdminPage {

    public static $action = 'covid-coupons-main';
    public static $title  = 'Covid Coupons';
    public static $slug   = 'covid-coupons-main';

    protected $endpoints = [
        'cvdapp_cancel' => 'markCertAsCancelled',
        'cvdapp_accept' => 'markCertAsAccepted',
        'cvdapp_use'    => 'markCertAsUsed',
        'cvdapp_unuse'  => 'markCertAsUnused',
        'cvdapp_paid'   => 'markCertAsPaid',
        'cvdapp_unpaid' => 'markCertAsUnpaid',
        'cvdapp_delete' => 'markCertAsDeleted',
    ];

    protected function enqueue()
    {
        $this->addScript('dist/table.js', $this->getAjaxObject());
        // $this->addStyle();
    }

    public function handle()
    {
        echo '<h1>Covid Coupons</h1>';

        $table = new ModelListTable();
        $table->prepare_items();
        $this->printTable($table);

        // echo '<div style="padding: 30px"><div id="cvdapp"></div></div>';
    }

    private function handleRequest($action)
    {
        $request = new CertificateRequest($action);

        try {
            $response = $request->handle();
        } catch (\Exception $e) {
            if ($e instanceof \CovidCoupons\App\Contracts\Exception) {
                \wp_send_json_error($e->toArray(), $e->getCode());
                \wp_die();
            } else {
                throw $e;
            }
        }

        return $this->respond($response);
    }

    public function markCertAsCancelled()
    {
        return $this->handleRequest('cancel');
    }

    public function markCertAsAccepted()
    {
        return $this->handleRequest('accept');
    }

    public function markCertAsUsed()
    {
        return $this->handleRequest('use');
    }

    public function markCertAsDeleted()
    {
        return $this->handleRequest('delete');
    }

    public function markCertAsPaid()
    {
        return $this->handleRequest('paid');
    }

    public function markCertAsUnpaid()
    {
        return $this->handleRequest('unpaid');
    }

    public function markCertAsUnused()
    {
        return $this->handleRequest('unused');
    }

    private function printTable($table)
    {
        $count = count( $table->items );
        $money = $this->getMoney($table->items);
        echo '<div class="wrap"><h1 class="wp-heading-inline">' . $table->getTitle() . '</h1>';
        echo '<span class="title-count theme-count">'.$count.' Coupon(s)</span>';
        echo '<span class="title-count theme-count">'.
            'Total: ' . cvdapp()->config()->currency_symbol . ' ' . $money.'</span>';
        // echo '<a class="page-title-action" href="' . \admin_url( 'admin-ajax.php' ) . '?action=' . $this->exportAction . '" target="_blank">Export All</a>';
        // echo '<a class="page-title-action" href="' . \admin_url( 'admin-ajax.php' ) . '?action=' . $this->mailAction . '" target="_blank">Email All</a>';
        echo '<hr class="wp-header-end">';

        echo '<div id="cvdapp"><form id="full-list-form" method="get"><cvdapp-table>';

        $table->display();

        echo '</cvdapp-table></form></div></div>';

        // add_action('admin_footer', [$this, 'footerScript']);
    }

    private function getMoney($items)
    {
        $amount = 0;
        foreach($items as $item) {
            $amount += $item->payment_amount;
        }
        return $amount;
    }

    public function footerScript()
    {
        ?>
            <script type="text/javascript" >
            
            </script> 
        <?php
    }
}
