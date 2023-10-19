<?php

namespace App\Interfaces;

interface UserRepositoryInterface 
{
    public function getAllUsers();
    public function getUserById($userid);
    public function deleteUser($userid);
    public function createUser(array $userDetails);
    public function updateUser($userid, array $newDetails);
}