<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function home(Request $request)
    {
        $ip = hash('sha512', $request->ip());
        if (Visitor::where('date', today())->where('ip', $ip)->count() < 1) {
            Visitor::create(['date' => today(), 'ip' => $ip]);
        } else {
            Visitor::where('date', today())->where('ip', $ip)->update(['ip' => $ip]);
        }
        return view('welcome');
    }

    public function login()
    {
        return view('auth.login');
    }
    public function register()
    {
        return view('auth.register');
    }

    public function getDashboard()
    {
        return view('dashboard');
    }
}
