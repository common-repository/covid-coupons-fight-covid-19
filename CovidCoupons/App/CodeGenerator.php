<?php namespace CovidCoupons\App;

class CodeGenerator {

    private $size = 12;

    public function random($formatted = false)
    {
        $chars = "23456789ABCDEFGHJKLMNPQRSTUVWXYZ";
        $res = "";
        for ($i = 0; $i < $this->size; $i++) {
            $res .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        return $formatted ? $this->format($res) : $res;;
    }

    public function make($attributes, $formatted = false)
    {
        // create a qr
    }

    public static function format($str)
    {
        return substr($str,0,4).'-'.substr($str,4,4).'-'.substr($str,8,4);   
    }

    public static function raw($str)
    {
        return str_replace('-', '', $str);
    }

}