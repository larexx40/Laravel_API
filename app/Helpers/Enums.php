<?php

namespace App\Config;

class Enums
{
    // User Status 1- active 2 - suspended 3 - banned 4 -frozen
    const USER_ACTIVE = 1;
    const USER_SUSPENDED = 2;
    const USER_BANNED = 3;
    const USER_FROZEN = 4;

    // Order Status
    const ORDER_PENDING = 'pending';
    const ORDER_PROCESSING = 'processing';
    const ORDER_COMPLETED = 'completed';

    // Other Enum Constants
}