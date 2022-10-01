<?php

namespace App\Http\Controllers\Auth;


use Illuminate\Http\Request;

class AuthFormController extends Controller
{
    public function loginform()
    {
        return view('loginform')->with("message","Please fill the following form");

    }
    public function registerform()
    {
        return view('registerform')->with("message","Please fill the following form");

    }
}
