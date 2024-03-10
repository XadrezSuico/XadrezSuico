<?php

namespace App\Http\Controllers;

use App\Email;
use App\Enum\EmailType;

use App\Helper\EmailTemplateHelper;

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
            $schedule_email->enxadrista_id = $enxadrista->getId();
        }
        $schedule_email->save();
        return true;
    }

    public static function schedule($email, $object, $email_type, $enxadrista = null)
    {
        // aqui vai a chamada para o Helper
        $emailTemplateHelper = new EmailTemplateHelper;
        $email_template = $emailTemplateHelper->generate($email_type,$object);

        $schedule_email = new Email;
        $schedule_email->email = $email;
        $schedule_email->subject = $email_template->subject;
        $schedule_email->text = $email_template->message;
        if ($enxadrista != null) {
            $schedule_email->enxadrista_id = $enxadrista->getId();
        }
        $schedule_email->save();
        return true;
    }

    public static function sendScheduledEmails()
    {
        $emails = Email::where([["is_sent", "=", false]])->orderBy("id", "ASC")->limit(intval(date("i")/2) + 10)->get();
        foreach ($emails as $email) {
            $email_send = new EmailSend($email);
            if($email->enxadrista){
                Mail::to($email->email, $email->enxadrista->name)->send($email_send);
            }else {
                Mail::to($email->email, "XadrezSuíço")->send($email_send);
            }
            $email->is_sent = true;
            $email->sent_at = date("Y-m-d H:i:s");
            $email->save();
        }
    }
}
