<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Exception;
class MailController extends Controller
{
    public function  mail () {

        $details=[
            'title'=> 'Mail from w3Lab',
            'body' => 'This if for testing mail using gmail.'
        ];

        Mail::to('dzonidzonka@gmail.com')->send(new SendMail($details));
        return "email Sent";
    }




}
