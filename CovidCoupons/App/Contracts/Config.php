<?php
namespace CovidCoupons\App\Contracts;

interface Config {

    public function isAdmin();

    public function debug();

    public function siteUrl();

    public function save();

    public function fill($settings);

    public function all();
}