<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private $urepo;
    private $wrepo;

    public function __construct()
    {
        $this->urepo = New UserRepository();
        $this->wrepo = new WalletRepository();
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $user  = User::whereId(auth()->user()->id)->with(['wallet' => function($wallet){
            return $wallet->select('id','balance','status','user_id');
        }])->firstOrFail();

        return $this->responseSuccess("Success",$user);
    }

    public function registerWallet(){

        $wallet = Wallet::firstOrCreate([
            'user_id' => auth()->user()->id
        ],[
            'balance' => 0,
            'status' => Wallet::STATUS_ACTIVE,
        ]);

        return $this->responseSuccess("Success", $wallet);
    }
}
