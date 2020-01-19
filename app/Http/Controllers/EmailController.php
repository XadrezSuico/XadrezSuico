<?php

namespace App\Http\Controllers;

use App\Email;
use App\Mail\EmailSend;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public static function scheduleEmail($email, $subject, $text, $enxadrista = null)
    {
        $schedule_email = new Email;
        $schedule_email->email = $email;
        $schedule_email->subject = $subject;
        $schedule_email->text = $text;
        if ($enxadrista != null) {
            $schedule_email->enxadrista_id = $enxadrista->id;
        }
        $schedule_email->save();
        return true;
    }

    public static function sendScheduledEmails()
    {
        $emails = Email::where([["is_sent", "=", false]])->orderBy("id", "ASC")->limit(10)->get();
        foreach ($emails as $email) {
            $email_send = new EmailSend($email);
            Mail::to($email->email, $email->enxadrista->name)->send($email_send);
            $email->is_sent = true;
            $email->sent_at = date("Y-m-d H:i:s");
            $email->save();
        }
    }
}
