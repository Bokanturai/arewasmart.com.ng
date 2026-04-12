<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EducationalPurchaseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $pin;
    public $amount;
    public $reference;
    public $serviceType;
    public $transactionDate;
    public $profileId;

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->customerName = $data['customer_name'] ?? 'Valued Customer';
        $this->pin = $data['pin'];
        $this->amount = $data['amount'];
        $this->reference = $data['reference'];
        $this->serviceType = $data['service_type'];
        $this->transactionDate = $data['transaction_date'] ?? now()->format('d M Y, h:i A');
        $this->profileId = $data['profile_id'] ?? null;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = strtoupper($this->serviceType) . ' PIN Purchase Confirmation - ' . $this->reference;
        
        return $this->subject($subject)
                    ->view('emails.educational_purchase_notification');
    }
}
