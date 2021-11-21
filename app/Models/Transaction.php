<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    CONST TYPE_DEBIT = 'debit';
    CONST TYPE_CREDIT = 'credit';
    CONST ACTIVITY_TOPUP = 'topup';
    CONST ACTIVITY_TRANSFER = 'transfer';

    protected $fillable = [
        'activity', 'type','amount','description'
    ];

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
}
