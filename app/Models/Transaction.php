<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    CONST TYPE_DEBIT = 'debit';
    CONST TYPE_CREDIT = 'credit';

    protected $fillable = [
        'type','amount','description','wallet_id'
    ];

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
}
