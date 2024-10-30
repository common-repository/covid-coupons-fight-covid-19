<?php namespace CovidCoupons\App;

class SqlManager {

    // public function createPaymentTable($table_name, $charset_collate = '')
    // {
    //   return "CREATE TABLE IF NOT EXISTS `{$table_name}` ( 
    //         `id` int(11) NOT NULL AUTO_INCREMENT,
    //         `user_name` varchar(255) DEFAULT NULL,
    //         `user_email` varchar(255) NOT NULL,
    //         `user_phone` varchar(255) DEFAULT NULL,
    //         `payment_token` varchar(255) DEFAULT NULL,
    //         `payment_meta` text,
    //         `payment_method` varchar(255) DEFAULT NULL,
    //         `payment_amount` float DEFAULT NULL,
    //         `payment_currency` varchar(5) DEFAULT NULL,
    //         `payment_status` varchar(255) DEFAULT NULL,
    //         `payment_id` varchar(255) DEFAULT NULL,
    //         `paid_at` datetime NULL DEFAULT NULL,
    //         `accepted_at` datetime NULL DEFAULT NULL,
    //         `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    //         PRIMARY KEY (`id`)
    //       ) $charset_collate;";
    // }

    public function createTable($table_name, $charset_collate = '')
    {
      return "CREATE TABLE IF NOT EXISTS `{$table_name}` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_name` varchar(255) DEFAULT NULL,
          `user_email` varchar(255) DEFAULT NULL,
          `user_phone` varchar(255) DEFAULT NULL,
          `gift_code` varchar(20) NOT NULL,
          `intent_id` varchar(255) DEFAULT NULL,
          `payment_method` varchar(100) DEFAULT NULL,
          `payment_id` varchar(255) DEFAULT NULL,
          `payment_status` varchar(100) DEFAULT NULL,
          `payment_amount` float DEFAULT NULL,
          `payment_currency` varchar(20) DEFAULT NULL,
          `payment_meta` text,
          `used_at` datetime DEFAULT NULL,
          `cancelled_at` datetime DEFAULT NULL,
          `paid_at` datetime DEFAULT NULL,
          `accepted_at` datetime DEFAULT NULL,
          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `gift_code` (`gift_code`),
          UNIQUE KEY `payment_id` (`payment_id`),
          UNIQUE KEY `intent_id` (`intent_id`)
        );";

    }

    public function dropTable($table_name)
    {
      return "DROP TABLE IF EXISTS {$table_name};";
    }

}