<?php

namespace App\Repositories;
use App\Models\Transaction;

class TransactionRepository
{
    public function getTransaction($wallet_id){
        return Transaction::select('*')->where('wallet_id','=', $wallet_id)->paginate(5);
    }

}
