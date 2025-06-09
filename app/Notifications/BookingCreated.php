<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification implements ShouldQueue
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
            ->subject('New Booking Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a new booking request for your property:')
            ->line($this->booking->property->title)
            ->line('From: ' . $this->booking->move_in_date->format('M d, Y') . ' to ' . $this->booking->move_out_date->format('M d, Y'))
            ->line('Total Amount: $' . number_format($this->booking->total_amount, 2))
            ->action('View Booking Details', route('bookings.show', $this->booking))
            ->line('Please review and respond to this booking request as soon as possible.');
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
            'student_name' => $this->booking->student->name,
            'move_in_date' => $this->booking->move_in_date->format('Y-m-d'),
            'move_out_date' => $this->booking->move_out_date->format('Y-m-d'),
            'total_amount' => $this->booking->total_amount,
            'message' => 'New booking request received',
        ];
    }
}
