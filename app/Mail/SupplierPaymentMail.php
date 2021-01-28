<?php

namespace App\Mail;

use App\Models\SupplierPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierPaymentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public SupplierPayment $supplierPayment;

    public function __construct(SupplierPayment $supplierPayment)
    {
        $this->supplierPayment = $supplierPayment;
    }

    public function build(): Mailable
    {
        return $this->to('adir.yed@gmail.com')
            ->subject("[Notification] {$this->supplierPayment->supplierUser->username} requested a {$this->supplierPayment->formatted_method} payment! #{$this->supplierPayment->id}")
            ->markdown('emails.supplier_payments');
    }
}
