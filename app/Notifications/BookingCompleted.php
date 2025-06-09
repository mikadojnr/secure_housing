<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCompleted extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Booking Completed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your booking has been marked as completed:')
            ->line($this->booking->property->title)
            ->line('From: ' . $this->booking->move_in_date->format('M d, Y') . ' to ' . $this->booking->move_out_date->format('M d, Y'))
            ->line('We hope you enjoyed your stay!')
            ->line('Please consider leaving a review for the property.')
            ->action('Write a Review', route('properties.show', $this->booking->property))
            ->line('Thank you for using our platform!');
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
            'landlord_name' => $this->booking->property->landlord->name,
            'move_in_date' => $this->booking->move_in_date->format('Y-m-d'),
            'move_out_date' => $this->booking->move_out_date->format('Y-m-d'),
            'message' => 'Your booking has been completed',
        ];
    }
}
