<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Package;
use App\Models\UserPremium;
use Illuminate\Support\Carbon;

class WebhookController extends Controller
{
    //untuk handle notifikasi dari midtrans
    public function handler(Request $request)
    {
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        $notif = new \Midtrans\Notification();

        $transactionStatus = $notif->transaction_status;
        //$type = $notif->payment_type;
        $transactionCode = $notif->order_id;
        $fraudStatus = $notif->fraud_status;

        $status = '';

        //dari midtrans
        if ($transactionStatus == 'capture'){
            if ($fraudStatus == 'challenge'){
                // TODO set transaction status on your database to 'challenge'
                // and response with 200 OK
                $status = 'challenge';
            } else if ($fraudStatus == 'accept'){
                // TODO set transaction status on your database to 'success'
                // and response with 200 OK
                $status = 'success';
            }
        } else if ($transactionStatus == 'settlement'){
            // TODO set transaction status on your database to 'success'
            // and response with 200 OK
            $status = 'success';
        } else if ($transactionStatus == 'cancel' ||
            $transactionStatus == 'deny' ||
            $transactionStatus == 'expire'){
            // TODO set transaction status on your database to 'failure'
            // and response with 200 OK
            $status = 'failure';
        } else if ($transactionStatus == 'pending'){
            // TODO set transaction status on your database to 'pending' / waiting payment
            // and response with 200 OK
            $status = 'pending';
        }

        //cari data transaksi berdasarkan data yang dikirim oleh midtrans
        $transaction = Transaction::with('package')
            ->where('transaction_code', $transactionCode)
            ->first();

        


        //jika status sukses, maka tambahkan ke user premium
        if($status === 'success'){
            //cari dahulu, apakah user sudah pernah beli atau belum
            $UserPremium = UserPremium::where('user_id', $transaction->user_id)->first();

            //kalau datanya ada/ketemu, maka:
            if($UserPremium) {
                //cari end of subscription yang lama
                $endOfSubscription = $UserPremium->end_of_subscription;
                //format tanggalnya menggunakan carbon
                $date = Carbon::createFromFormat('Y-m-d', $endOfSubscription);
                //buat tanggal subscription yang baru
                $newEndOfSubscription = $date->addDays($transaction->package->max_days)->format('Y-m-d');

                //lalu update userpremiumnya
                $UserPremium->update([
                    'package_id' => $transaction->package_id,
                    'end_of_subscription' => $newEndOfSubscription
                ]);
            } else {
                //kalau tidak ada di database user premium, maka langsung create berdasarkan tanggal hari ini
                UserPremium::create([
                    'package_id' => $transaction->package->id,
                    'user_id' => $transaction->user_id,
                    'end_of_subscription' => now()->addDays($transaction->package->max_days)
                ]);
            }
        }

        //jika sudah, update status transaksinya menjadi success
        $transaction->update([
            'status' => $status
        ]);
    }
}
