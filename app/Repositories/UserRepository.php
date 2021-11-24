<?php

namespace App\Repositories;
use App\Models\User;

class UserRepository
{
    public function create(array $data){
        return User::create($data);
    }

    public function getById(int $id){
        return User::whereId($id)->with(['wallet' => function($wallet){
            return $wallet->select('id','balance','status','user_id');
        }])->firstOrFail();
    }

}
