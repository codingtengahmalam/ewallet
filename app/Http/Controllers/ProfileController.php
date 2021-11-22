<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $user = $this->urepo->getById(auth()->user()->id);
        return $this->responseSuccess("Success",$user);
    }

    public function registerWallet(){
        $wallet = $this->wrepo->getOrCreateWallet(auth()->user()->id);
        return $this->responseSuccess("Success", $wallet);
    }
}
