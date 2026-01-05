<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\SalarySlip;


class SalarySlipUploaded extends Notification
{
    
    use Queueable;

    public $salarySlip;

    public function __construct(SalarySlip $salarySlip)
    {
        $this->salarySlip = $salarySlip;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Salary Slip Uploaded')
                    ->greeting('Hello Sk')
                    ->line('A new salary slip has been uploaded for you.')
                    ->line('Month: ' . $this->salarySlip->month . ' ' . $this->salarySlip->year)
                    ->action('View Salary Slip', url('/salary-slips'))
                    ->line('Thank you.');
    }
}
