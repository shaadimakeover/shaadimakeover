<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VendorDashboard extends Controller
{
    public function login()
    {
        try {
            if (Auth::check()) {
                return redirect()->route('vendor.dashboard');
            } else {
                return view('auth.vendor-login');
            }
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            //return redirect()->back()->with('error', "Something went wrong, please try again!");
            abort(500);
        }
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        try {
            $user = User::role('VENDOR')->where('email', $request->email)->where('active', 1)->first();
            if ($user && Hash::check($request->password, $user->password)) {
                $credentials = $request->only('email', 'password');
                if (Auth::attempt($credentials)) {
                    return redirect()->route('vendor.dashboard');
                }
                return redirect()->back()->withErrors('Login failed,please try again!');
            } else {
                return redirect()->back()->withErrors('Unauthorized the login credentials are not valid');
            }
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            //return redirect()->back()->with('error', "Something went wrong, please try again!");
            abort(500);
        }
    }

    public function getDashboard(Request $request)
    {
        // if (Auth::check() && Auth::user()->role_name != 'VENDOR') {
        //     return redirect()->back()->with('error', 'working');
        // }

        $count['userCount'] = User::role('USER')->count();
        return view('vendors.dashboard', compact('count'));
    }
    public function getProfile()
    {
        try {
            return view('vendors.profile');
        } catch (\Exception $e) {
            Log::error(" :: EXCEPTION :: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            //return redirect()->back()->with('error', "Something went wrong, please try again!");
            abort(500);
        }
    }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect('/vendor');
    }
}
