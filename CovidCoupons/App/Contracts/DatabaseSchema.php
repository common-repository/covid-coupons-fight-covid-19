<?php
namespace CovidCoupons\App\Contracts;

interface DatabaseSchema {

    public function run($sql);

    public function getCollation();

    public function getPrefix();
}