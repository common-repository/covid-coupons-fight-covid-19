<?php
namespace CovidCoupons\App\Contracts;

interface Model {

    public function create($attributes = null);

    public function save();

    public function delete();

    public function update($attributes = null);

    public static function find($id);

    public static function all();

}