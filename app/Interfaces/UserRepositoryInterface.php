<?php

namespace App\Interfaces;

use Ramsey\Uuid\Type\Integer;

interface UserRepositoryInterface
{
    public function getAllUsers();
    public function getUserByEmail($email);
    public function getUserByPubkey($userpubkey);
    public function checkIfUserExist($column, $value);
    public function getUser($column, $value);
    public function getUserData($column, $value, $whatToGet='');
    public function getUserById($userid);
    public function verifyUserEmail($userid);
    public function verifyUserPhone($phoneno);
    public function deleteUser($userid);
    public function createUser(array $userDetails);
    public function updateUser($userid, array $newDetails);
    public function saveToUser($details);
    public function setPin($userid, $pin);
}
