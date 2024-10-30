<?php
namespace CovidCoupons\App\Contracts;

interface GiftCertificate {

    public function create($attributes);

    public function save();

    public function delete();

    public function update($attributes);

    public function formattedCode();

    public function qr();

    public function markUsed();

    public function markPaid();

    public function markCancelled();

    public static function find($id);

    public static function findByCode($code);

}