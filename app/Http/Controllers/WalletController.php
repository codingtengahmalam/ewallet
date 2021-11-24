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

    public function getDetail(){
        $wallet_id = auth()->user()->wallet->id;
        $detail = $this->wrepo->getWalletDetail($wallet_id);
        return $this->responseSuccess('success', $detail);
    }


    public function topup(Request $request){
        $this->validate($request, [
            'amount' => 'int|min:1|required'
        ]);

        $wallet_id = auth()->user()->wallet->id;
        $wallet = $this->wrepo->increaseBalance($wallet_id,$request->amount,'topup balance');
        return $this->responseSuccess('success topup wallet', $wallet);
    }

    public function transfer(Request $request){
        $this->validate($request, [
            'amount' => 'int|min:1|required',
            'wallet_id' => 'int|required',
            'note' => 'string|max:100'
        ]);

        $result = $this->wrepo->transferBalance($request->wallet_id, $request->amount, $request->note);

       return $this->responseSuccess('Success transfer balance', $result);
    }
}
