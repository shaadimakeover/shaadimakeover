<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function firebaseRegister(){
        return view('firebase-register');
    }
}
