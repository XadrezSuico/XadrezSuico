<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CronController extends Controller
{
	public function index(){
        EmailController::sendScheduledEmails();
	}
}
