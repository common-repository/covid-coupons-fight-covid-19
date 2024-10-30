<?php
namespace CovidCoupons\App\Contracts;

interface Mailer {

    public function send($to, $subject, $message, $headers = '', $attachments = []);

}