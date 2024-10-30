<?php namespace CovidCoupons\Adapters\WP;

use CovidCoupons\App\Contracts\DatabaseSchema as DatabaseSchemaInterface;

class DatabaseSchema implements DatabaseSchemaInterface {

    private $db;

    private function db()
    {
        if (!$this->db) {
            global $wpdb;
            $this->db = $wpdb;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }
        return $this->db;
    }

    public function run($sql)
    {
        return \dbDelta( $sql );
    }

    public function getCollation()
    {
        return 'utf8_unicode_ci';//$this->db()->get_charset_collate();
    }

    public function getPrefix()
    {
        return $this->db()->prefix;
    }
}