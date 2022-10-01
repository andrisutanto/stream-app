<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('member.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        //ambil request, kecuali token
        $data = $request->except("_token");

        //validate user email dgn mencari di table user
        $isEmailExist = User::where('email', $request->email)->exists();

        //jika emailnya sudah ada, kasih error message
        if($isEmailExist) {
            return back()
                ->withErrors([
                    'email' => 'Email already exist'
                ])
                ->withInput();
        }

        $data['role'] = 'member';

        //password di encrypt
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return back();
        //return redirect()->route('member.login');
    }
}
