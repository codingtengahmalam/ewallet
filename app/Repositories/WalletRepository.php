<?php

namespace App\Repositories;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Validation\ValidationException;

class WalletRepository
{
    public function getOrCreateWallet(int $user_id){
        return Wallet::firstOrCreate([
                'user_id' => $user_id
            ],[
                'balance' => 0,
                'status' => Wallet::STATUS_ACTIVE,
            ]);
    }

    public function getWalletDetail($id){
       return Wallet::whereId($id)->with(['transactions' => function($trs){
            return $trs->orderBy('created_at','desc')->limit(5);
        }])->firstOrFail();
    }

    public function transferBalance(int $wallet_id, int $amount, string $note){
       return \DB::transaction(function () use ($wallet_id, $amount, $note) {
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

    }

    public function checkBalance(int $wallet_id){
        $wallet = Wallet::whereId($wallet_id)->first();
        if (empty($wallet)){
            throw ValidationException::withMessages([
                'wallet' => 'Wallet id not found'
            ]);
        }

        return $wallet->balance;
    }

    public function decreaseBalance(int $wallet_id, int $amount, string $note = 'decrease balance'){
        return \DB::transaction(function () use ($wallet_id, $amount, $note) {
            $wallet = Wallet::whereId($wallet_id)->first();
            if (empty($wallet)){
                throw ValidationException::withMessages([
                    'wallet' => 'Wallet id not found'
                ]);
            }

            if ($wallet->status != Wallet::STATUS_ACTIVE){
                throw new \Exception("Wallet not active");
            }

            $wallet->balance -= $amount;
            $wallet->update();

            //new record decrease balance
            $trs = New Transaction();
            $trs->type = Transaction::TYPE_DEBIT;
            $trs->amount = $amount;
            $trs->description = $note;
            $trs->wallet_id = $wallet->id;

            $trs->save();

            return $wallet;
        });

    }

    public function increaseBalance(int $wallet_id,int $amount,  string $note = 'increase balance'){
        return \DB::transaction(function () use ($wallet_id, $amount, $note) {
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
    }

}
