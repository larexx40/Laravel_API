<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface {

    public function getAllUsers() 
    {
        // return User::all();
        return User::orderBy('id', 'desc')->get();
        
    }

    public function getUserById($userid) 
    {
        return User::findorFail($userid);
    }

    public function deleteUser($userid) 
    {
    //    return User::destroy($userid);
       return User::query()->where('userid', $userid)->delete();
    }

    public function createUser(array $userDetails) 
    {
        return User::create($userDetails);
    }

    public function updateUser($userid, array $newDetails) 
    {
        // return User::whereId($userid)->update($newDetails);
        return User::query()->where('userid', $userid)->update($newDetails);
    }
    
}