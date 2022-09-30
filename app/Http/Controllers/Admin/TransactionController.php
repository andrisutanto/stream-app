<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        //untuk nama package dan user, harus sesuai dgn yang dibuat pada model
        $transactions = Transaction::with([
            'package',
            'user'
        ])->get();

        // dd($transactions);
        return view('admin.transactions',['transactions' => $transactions]);
    }
}
