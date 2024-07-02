<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LogoutController extends Controller
{
    public function logout(Request $request) {

        // DB::table('tbluser_log')->insert([
        //     'ic' => Auth::user()->ic,
        //     'remark' => 'LOGOUT',
        //     'date' => Carbon::now()
        // ]);

        auth()->logout();
        return view('auth.login');
    }
}
