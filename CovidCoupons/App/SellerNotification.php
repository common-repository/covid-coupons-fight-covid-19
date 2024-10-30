<?php namespace CovidCoupons\App;

use CovidCoupons\App\Contracts\GiftCertificate;
use CovidCoupons\App\Contracts\Mailer;

class SellerNotification {

    protected $certificate;

    public function __construct(GiftCertificate $giftCertificate)
    {
        $this->certificate = $giftCertificate;
    }

    public function send()
    {
        $mailer = cvdapp()->resolve(Mailer::class);

        $admin = cvdapp()->config()->seller_email;
        
        return $mailer->send($admin, $this->getSubject(), $this->getMessage());
    }

    private function getSubject()
    {
        return 'Gift Certificate Purchased - ' . strtoupper($this->certificate->payment_currency) . ' ' . $this->certificate->payment_amount;
    }

    private function getMessage()
    {
        ob_start();
        ?>
        A new gift certificate was purchased:

        Amount:  <?php echo strtoupper($this->certificate->payment_currency) . ' ' . $this->certificate->payment_amount; ?>

        Name:  <?php echo $this->certificate->user_name; ?>

        Email: <?php echo $this->certificate->user_email; ?>

        Phone: <?php echo $this->certificate->user_phone; ?>

        Code: <?php echo $this->certificate->formattedCode(); ?>


        <?php
        return ob_get_clean();
    }
}