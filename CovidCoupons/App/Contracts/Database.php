<?php namespace CovidCoupons\App\Contracts;

interface Database {

    public function __construct($table);

    public function create($attributes);

    public function update($id, $attributes);

    public function delete($id);

    public function exists($key, $val);

    public function all($orderby = null, $order = null);

    public function find($id);

    public function findBy($key, $val, $orderby = null, $order = null);

    public function getPrefix();
}