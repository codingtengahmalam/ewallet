<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    private $wrepo;

    public function __construct()
    {
        $this->wrepo = new WalletRepository();
    }


    public function topup(Request $request){
        $this->validate($request, [
            'amount' => 'int|min:1|required'
        ]);

        $wallet_id = auth()->user()->wallet->id;
        $wallet = $this->wrepo->increaseBalance($wallet_id,$request->amount,'topup balance');
        return $this->responseSuccess('success topup wallet', $wallet);
    }
}
