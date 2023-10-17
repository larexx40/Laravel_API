<?php

namespace App\Config;

class APIErrorCode
{
    // Error code that starts with 1 is from us,2 is from third party

// FROM WHAT SYSTEM____FROM WHERE__ERRORTYPE

// FROM WHAT SYSTEM
// internal(our code) -1
// external(Third party API) -2

// FROM WHERE INTERNAL
// database insert error-->1
// databse update error-->2
// database delete error-->3
// user wrong action error ---> 4 (insufficient fund, empty data,authorization)
// Hacker attempt--->5 (wrong method/user not found)

// FROM WHERE EXTERNAL
// Call to API failed -->6
// Sent wrong data to API->7
// Failed to satisfy API need on their dashboard ->8(Insufficinet fund)

// ERRORTYPE
// 1--Fatal
// 2--Warning
    /**
     * Welcome message
     *
     * @var string
     */
    // General errors
    
    public static $internalUserWarning = 142;
    public static $internalHackerWarning = 151;
    public static $invalidDataSent = 152;
    public static $invalidUserDetail = 153;
    public static $invalidInfo = 147;

    public static $internalInsertDBFatal = 111;
    public static $internalUpdateDBFatal = 121;
    public static $internalDeleteDBFatal = 131;
    public static $internalHackerFatal = 151;

    public static $externalApiFailed = 261;
    public static $externalHackerWarning = 272;
    public static $externalApiDetailsWarning = 282;
    public static $externalApiDetailsError = 281;
}