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
        return User::where('userid', $userid)->first();
    }

    public function getUserByEmail($email){
        return User::where('email', $email)->first();
    }

    public function getUserByPubkey($userpubkey){
        return User::where('userpubkey', $userpubkey)->first();
    }

    public function getUser($column, $value)
    {
        return User::where($column, $value)->first();
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

    public function checkIfUserExist($column, $value){
        return User::where($column, $value)->exists();
    }

    public function saveToUser($details)
    {
        return User::create($details);
    }

    public function getUserData($column, $value, $whatToGet=[])
    {
        if (empty($whatToGet)) {
            // If $whatToGet is not specified, get all columns
            return User::where($column, $value)->first();
        } else {
            // If $whatToGet is specified, fetch only those columns
            return User::where($column, $value)->select($whatToGet)->first();
        }
    }

    public function verifyUserEmail($userid)
    {
        return User::where('userid', $userid)->update(['is_email_verified' => 1]);
    }

    public function verifyUserPhone($phoneno){
        return User::where('phoneno', $phoneno)->update(['is_phone_verified' => 1]);
    }

    public function setPin($userid, $pin)
    {
        return User::where('userid', $userid)->update(['pin'=>$pin]);
    }

    public function setProfilePic($userid, $profilepic)
    {
        return User::where('', $userid)->update(['profile_pic'=>$profilepic]);
    }

    public function getRegSummary($userid){
        return User::where('userid', $userid)->select("is_email_verified", "is_phone_verified", "profile_pic", "pin")->first();
    }



}
