<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $fillable = [
        'package_id',
        'user_id',
        'amount',
        'transaction_code',
        'status'
    ];

    //add relation to table Package
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    //add relation to table User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
