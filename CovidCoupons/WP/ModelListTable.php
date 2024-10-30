<?php
namespace CovidCoupons\WP;

use CovidCoupons\App\Contracts\GiftCertificate;
use CovidCoupons\WP\AdminManager;
use CovidCoupons\WP\AjaxManager;

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ModelListTable extends \WP_List_Table {

    public $items;

    protected $_column_headers;

    public function __construct( $args = array() )
    {
        $this->_column_headers = [
            $this->get_columns(),
            array(),
            $this->get_sortable_columns(),
            'actions'
        ];
        parent::__construct( $args );
    }

    public function getTitle()
    {
        return 'Covid Coupons';
    }

    public function get_columns()
    {
        return static::getColumns();
    }

    public static function getColumns()
    {
        return [
            'cb'             => '<input type="checkbox" />',
            'id'             => 'ID',
            'user_name'      => 'Name',
            'user_email'     => 'Email',
            'user_phone'     => 'Phone',
            'gift_code'      => 'Code',
            'payment_method' => 'Method',
            'payment_status' => 'Status',
            'payment_amount' => 'Amount',
            'used_at'        => 'Used',
            'cancelled_at'   => 'Cancelled',
            'paid_at'        => 'Paid',
            'accepted_at'    => 'Accepted',
            'actions'        => 'Actions'
        ];
    }

    public function column_default( $item, $column_name ) {

        return static::columnFor($item, $column_name);
    }

    public static function htmlFor($item)
    {
        $cols = array_keys(static::getColumns());
        $html = '<tr>';
        foreach($cols as $col) {
            $html .= '<td>'.static::columnFor($item, $col).'</td>';
        }
        $html .= '</tr>';
        return $html;
    }

    public static function columnFor($item, $column_name) 
    {
        switch ( $column_name ) {
            case 'cb':
                return '<input type="checkbox" data-id="'.\esc_attr($item->id).'" />';

            case 'user_email':
                return '<a href="mailto:'.\esc_attr($item->user_email).'">'.\esc_html($item->user_email).'</a>';

            case 'user_phone':
                return '<a href="tel:'.\esc_attr($item->user_phone).'">'.\esc_html($item->user_phone).'</a>';

            case 'gift_code': 
                return $item->formattedCode();            

            case 'actions':
                return '<actions id="'.\esc_attr($item->id).'" :attributes="'. \esc_attr(json_encode($item->getAttributes())).'"></actions>';

            case 'payment_amount':
                return strtoupper($item->payment_currency) . ' ' . $item->payment_amount;



            case 'accepted_at':
            case 'paid_at':
            case 'cancelled_at':
            case 'used_at':
                return '<span title="' . \esc_attr($item->$column_name) . '">'.static::niceTime($item->$column_name).'</span>';
            default:
                return \esc_html($item->$column_name);
        }
    }

    protected static function niceTime($date)
    {
        if (!$date) {
            return '';
        }
        $timestamp = strtotime($date);
        return \human_time_diff($timestamp) . ' ago';
    }

    protected function shortenString($string)
    {
        if (strlen($string) >= 20) {
            return '<span title="'. \esc_attr($string) . '">'.substr($string, 0, 17). " ...</span>";
        }
        else {
            return $string;
        }
    }

    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        $this->items = cvdapp()->resolve(GiftCertificate::class)->all();

    }

    public function no_items() {
        return 'No items yet.';
    }
}