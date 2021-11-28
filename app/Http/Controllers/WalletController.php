<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    private $wrepo;

    public function __construct()
    {
        $this->wrepo = new WalletRepository();
    }

    public function getDetail(){
        $wallet_id = auth()->user()->wallet->id;

        $detail = Wallet::whereId($wallet_id)->with(['transactions' => function($trs){
            return $trs->orderBy('created_at','desc')->limit(5);
        }])->firstOrFail();

        return $this->responseSuccess('success', $detail);
    }


    public function topup(Request $request){
        $this->validate($request, [
            'amount' => 'int|min:1|required'
        ]);

        $wallet_id = auth()->user()->wallet->id;
        $amount = $request->amount;
        $note ='topup balance';

        $wallet = \DB::transaction(function () use ($wallet_id, $amount,$note) {
            $wallet = Wallet::whereId($wallet_id)->first();
            if (empty($wallet)){
                throw ValidationException::withMessages([
                    'wallet' => 'Wallet id not found'
                ]);
            }

            $wallet->balance += $amount;
            $wallet->update();

            $trs = New Transaction();
            $trs->type = Transaction::TYPE_CREDIT;
            $trs->amount = $amount;
            $trs->description = $note;
            $trs->wallet_id = $wallet->id;
            $trs->save();

            return $wallet;
        });

        return $this->responseSuccess('success topup wallet', $wallet);
    }

    public function transfer(Request $request){
        $this->validate($request, [
            'amount' => 'int|min:1|required',
            'wallet_id' => 'int|required',
            'note' => 'string|max:100'
        ]);


        $wallet_id = $request->wallet_id;
        $amount =  $request->amount;
        $note = $request->note;

        $result =  \DB::transaction(function () use ($wallet_id, $amount, $note) {
            $source = auth()->user()->wallet();

            if($source->balance < $amount) {
                throw new \Exception("your balance is not sufficient");
            }

            $source->balance -= $amount;
            $source->update();

            //new record decrease balance
            $source_history = New Transaction();
            $source_history->type = Transaction::TYPE_DEBIT;
            $source_history->amount = $amount;
            $source_history->description = $note;
            $source_history->wallet_id = $source->id;
            $source_history->save();


            $target = Wallet::whereId($wallet_id)->first();
            if (empty($wallet)){
                throw ValidationException::withMessages([
                    'wallet' => 'Wallet id not found'
                ]);
            }
            if ($target->status != Wallet::STATUS_ACTIVE){
                throw new \Exception("Wallet not active");
            }

            $target->balance += $amount;
            $target->update();

            //new record decrease balance
            $target_history = New Transaction();
            $target_history->type = Transaction::TYPE_CREDIT;
            $target_history->amount = $amount;
            $target_history->description = $note;
            $target_history->wallet_id = $target->id;
            $target_history->save();

            return $source;
        });


        return $this->responseSuccess('Success transfer balance', $result);
    }
}
