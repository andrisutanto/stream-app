<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\UserPremium;
use Illuminate\Support\Carbon;

class MovieController extends Controller
{
    public function show($id)
    {
        //ambil ID movie, dan cari dari database
        $movie = Movie::find($id);

        return view('member.movie-detail', ['movie' => $movie]);
    }

    public function watch($id)
    {
        //ambil dahulu user yang sedang login
        $userId = auth()->user()->id;

        //cek, apakah user tersebut memiliki premium atau tidak
        $userPremium = UserPremium::where('user_id', $userId)->first();

        //jika ada di table premium, maka
        if($userPremium) {
            //cari tanggal habis/end subscription
            $endOfSubscription = $userPremium->end_of_subscription;

            //convert format datenya menggunakan carbon
            $date = Carbon::createFromFormat('Y-m-d', $endOfSubscription);

            //cek, apakah masih valid tanggalnya dibandingkan dengan hari ini
            $isValidSubscription = $date->greaterThan(now());

            //cek subscription
            if($isValidSubscription){
                //kalau valid, cari id movie nya
                $movie = Movie::find($id);
                //kemudian return ke halaman wathcing movie
                return view('member.movie-watching', ['movie' => $movie]);
            }
        }

        return redirect()->route('pricing');
    }
}
