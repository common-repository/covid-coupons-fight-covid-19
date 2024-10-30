<?php namespace CovidCoupons\App\Abstracts;

abstract class Request {

    protected $posted;
    protected $method;

    public function __construct()
    {
        $this->posted = $this->build();
        $this->method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED);
    }

    abstract public function build();

    abstract public function validate();

    public function get($attribute)
    {
        return $this->has($attribute) ? $this->posted[$attribute] : null;
    }

    public function has($attribute)
    {
        return array_key_exists($attribute, $this->posted);
    }

    public function input()
    {
        return (object) $this->posted;
    }

    public function isMethod($test)
    {
        return strtolower($test) === strtolower($this->method);
    }

    protected function isEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function isFloat($value)
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    protected function postedString($attribute)
    {
        return filter_input(INPUT_POST, $attribute, FILTER_SANITIZE_STRING);
    }

    protected function postedEmail($attribute)
    {
        return filter_input(INPUT_POST, $attribute, FILTER_SANITIZE_EMAIL);
    }

    protected function postedFloat($attribute)
    {
        return filter_input(INPUT_POST, $attribute, FILTER_SANITIZE_NUMBER_FLOAT);
    }

    protected function postedInt($attribute)
    {
        return filter_input(INPUT_POST, $attribute, FILTER_SANITIZE_NUMBER_INT);
    }

    protected function postedArray($attribute)
    {
        return filter_input(INPUT_POST, $attribute, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    } 

}