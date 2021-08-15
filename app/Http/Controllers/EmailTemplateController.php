<?php

namespace App\Http\Controllers;

use App\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $templates = EmailTemplate::whereNull("grupo_evento_id")->whereNull("evento_id")->get();
        return view('emailtemplate.index', compact("templates"));
    }
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $email_template = EmailTemplate::find($id);
        return view('emailtemplate.edit', compact("email_template"));
    }
    public function edit_post($id, Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobalbyPerfil([1, 2])) {
            return redirect("/");
        }

        $email_template = EmailTemplate::find($id);
        $email_template->name = $request->input("name");
        $email_template->subject = $request->input("subject");
        $email_template->message = $request->input("message");
        $email_template->save();

        return redirect("/emailtemplate/edit/" . $email_template->id);
    }
}
