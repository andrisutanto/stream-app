<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPremium;

class UserPremiumController extends Controller
{
    public function index()
    {
        //cari user yang sedang login
        $userId = auth()->user()->id;

        //kemudian cari paket dari user tersebut
        $userPremium = UserPremium::with('package')
            ->where('user_id', $userId)
            ->first();

        //jika bukan user premium, redirect ke halaman pricing
        if(!$userPremium) {
            return redirect()->route('pricing');
        }

        return view('member.subscription', ['user_premium' => $userPremium]);
    }
}
