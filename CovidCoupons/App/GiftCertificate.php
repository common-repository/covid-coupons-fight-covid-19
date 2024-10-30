<?php namespace CovidCoupons\App;

use CovidCoupons\App\Abstracts\AbstractModel;
use CovidCoupons\App\CodeGenerator;
use CovidCoupons\App\Contracts\GiftCertificate as GiftCertificateInterface;
use CovidCoupons\App\Contracts\Model;

class GiftCertificate extends AbstractModel implements GiftCertificateInterface, Model {

    protected $table = 'c19_coupons';

    protected $generator;

    public function create($attributes = null)
    {
        if ($attributes && !isset($attributes['gift_code'])) {
            $attributes['gift_code'] = $this->generator()->random();
        }
        return parent::create($attributes);
    }

    public function formattedCode()
    {
        return $this->generator()->format($this->gift_code);
    }

    public function qr()
    {
        // Generate QR from $this->code @todo @qr
    }

    public function markUsed()
    {
        $this->update(['used_at' => $this->now()]);
        return $this;
    }

    public function markUnused()
    {
        $this->update(['used_at' => null]);
        return $this;
    }

    public function markPaid()
    {
        $this->update(['paid_at' => $this->now(), 'cancelled_at' => null]);
        return $this;
    }

    public function markUnpaid()
    {
        $this->update(['paid_at' => null, 'accepted_at' => null]);
        return $this;
    }

    public function markCancelled()
    {
        $this->update(['cancelled_at' => $this->now(), 'accepted_at' => null]);
        return $this;
    }

    public function markAccepted()
    {
        $this->update(['cancelled_at' => null, 'accepted_at' => $this->now()]);
        return $this;
    }

    public function updateStatus($status)
    {
        $this->update(['status' => $status]);
        return $this;
    }

    public static function findByCode($code)
    {
        $result = static::DB()->findBy('code', $code);
        return $result ? new static( $result ) : null;
    }

    protected function generator()
    {
        if (!$this->generator) {
            $this->generator = new CodeGenerator;
        }
        return $this->generator;
    }

    private function now()
    {
        return date('Y-m-d H:i:s');
    }

}