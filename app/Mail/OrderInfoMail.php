<?php

namespace App\Mail;


use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderInfoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $url = url('/customer/orders/' . $this->order->id . '/invoice?i_otp=' . $this->order->invoice_otp);
        return $this->view('mails.order_info')->with([
            'otp' => $this->order->otp,
            'url' => $url
        ]);
    }
}
