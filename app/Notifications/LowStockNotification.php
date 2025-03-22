<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;
    protected $products;


    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
                ->subject('Low Stock Alert')
                ->greeting('Hello ' . $notifiable->name . ',')
                ->line('The following products are low in stock:');

            foreach ($this->products as $product) {
                $mailMessage->line("Product: {$product->name} - Current Stock: {$product->stock}");
            }
            return $mailMessage;

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
