<?php
namespace CovidCoupons\App\Contracts;

interface Migrations {

    public function createTable();

    public function deleteTable();
    
}