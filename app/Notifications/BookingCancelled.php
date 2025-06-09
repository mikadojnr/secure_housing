<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Booking Cancelled')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A booking has been cancelled:')
            ->line($this->booking->property->title)
            ->line('From: ' . $this->booking->move_in_date->format('M d, Y') . ' to ' . $this->booking->move_out_date->format('M d, Y'));

        if ($this->booking->cancellation_reason) {
            $mail->line('Reason: ' . $this->booking->cancellation_reason);
        }

        return $mail->action('View Booking Details', route('bookings.show', $this->booking))
            ->line('If you have any questions, please contact support.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'property_id' => $this->booking->property_id,
            'property_title' => $this->booking->property->title,
            'cancellation_reason' => $this->booking->cancellation_reason,
            'move_in_date' => $this->booking->move_in_date->format('Y-m-d'),
            'move_out_date' => $this->booking->move_out_date->format('Y-m-d'),
            'message' => 'Booking has been cancelled',
        ];
    }
}
