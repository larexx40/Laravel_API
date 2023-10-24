<?php

namespace App\Interfaces;

interface UserRepositoryInterface 
{
    public function getAllUsers();
    public function getUserByEmail($email);
    public function getUserByPubkey($userpubkey);
    public function checkIfUserExist($column, $value);
    public function getUser($column, $value);
    public function getUserData($column, $value, $whatToGet='');
    public function getUserById($userid);
    public function deleteUser($userid);
    public function createUser(array $userDetails);
    public function updateUser($userid, array $newDetails);
    public function saveToUser($details);
}