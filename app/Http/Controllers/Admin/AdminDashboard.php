<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDashboard extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        } else {
            return view('auth.login');
        }
    }
    public function getDashboard(Request $request)
    {
        //dd(Auth::user()->role_name);
        // if (Auth::check() && Auth::user()->role_name != 'SUPER-ADMIN') {
        //    return redirect()->back();
        // }
        $count['userCount'] = User::role('USER')->count();
        $count['activeUserCount'] = User::role('USER')->whereActive(1)->count();
        $count['blockedUserCount'] = User::role('USER')->whereActive(0)->count();
        $count['currentVisitorCount'] = Visitor::where('updated_at', '>=', Carbon::now()->subMinutes(10))->count();

        //site visitor graph start
        $date = Carbon::today()->subDays(7);
        $site_visit_days = Visitor::select('date', DB::raw('count(*) as count_visitor'))->where('date', '>=', $date)->groupBy('date')->orderBy('date', 'DESC')->get();
        $visit_last_days = [];
        foreach (range(0, 7) as $day) {
            $date_array = [];
            $date_array['date'] = Carbon::parse($date->copy()->addDays($day))->format('d M');
            $date_array['count_visitor'] = 0;
            $date_array['color'] = '';
            foreach ($site_visit_days as $value) {
                if ($value->date == Carbon::parse($date->copy()->addDays($day))->format('Y-m-d')) {
                    $date_array['count_visitor'] = $value->count_visitor;
                }
            }
            array_push($visit_last_days, $date_array);
        }


        return view('admin.dashboard', [
            'visit_last_days' => json_encode(array_reverse($visit_last_days)),
            'count' => $count
        ]);
    }
    public function getProfile()
    {
        try {
            return view('admin.profile');
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            //return redirect()->back()->with('error',"Something is woring");
            abort(500);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/admin');
    }
}
