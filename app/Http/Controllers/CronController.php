<?php

namespace App\Http\Controllers;

class CronController extends Controller
{
    public function index()
    {
        EmailController::sendScheduledEmails();
    }
}
