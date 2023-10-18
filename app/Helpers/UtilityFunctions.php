<?php
namespace App\Utilities;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Helpers;

class UtilityFunctions extends Helpers
{
    public static function isStringHasEmojis($string)
    {
        $emojisRegex =
            '/[\x{0080}-\x{02AF}'
            . '\x{0300}-\x{03FF}'
            . '\x{0600}-\x{06FF}'
            . '\x{0C00}-\x{0C7F}'
            . '\x{1DC0}-\x{1DFF}'
            . '\x{1E00}-\x{1EFF}'
            . '\x{2000}-\x{209F}'
            . '\x{20D0}-\x{214F}'
            . '\x{2190}-\x{23FF}'
            . '\x{2460}-\x{25FF}'
            . '\x{2600}-\x{27EF}'
            . '\x{2900}-\x{29FF}'
            . '\x{2B00}-\x{2BFF}'
            . '\x{2C60}-\x{2C7F}'
            . '\x{2E00}-\x{2E7F}'
            . '\x{3000}-\x{303F}'
            . '\x{A490}-\x{A4CF}'
            . '\x{E000}-\x{F8FF}'
            . '\x{FE00}-\x{FE0F}'
            . '\x{FE30}-\x{FE4F}'
            . '\x{1F000}-\x{1F02F}'
            . '\x{1F0A0}-\x{1F0FF}'
            . '\x{1F100}-\x{1F64F}'
            . '\x{1F680}-\x{1F6FF}'
            . '\x{1F910}-\x{1F96B}'
            . '\x{1F980}-\x{1F9E0}]/u';
        preg_match($emojisRegex, $string, $matches);
        return !empty($matches);
    }

    public static function escape($data)
    {
        // This removes all the HTML tags from a string and sanitizes the input.
        $input = strip_tags($data);

        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        // Optionally, remove any leading/trailing white spaces
        $input = trim($input);

        // Escape the input to prevent SQL injection
        $input = DB::connection()->getPdo()->quote($input);

        // Check if the input is empty or 'null'
        if (is_null($input) || $input === "''") {
            return "";
        }

        return $input;
    }
    

    public static function getCurrentFullURL()
    {
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], '/'))) . '://';
        $servername = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        $path = $_SERVER['PHP_SELF'];
        $endpoint = $protocol . $servername . ":" . $port . $path;
        return $endpoint;
    }

    public static function validate_input($data)
    {
        $incorrectdata = false;
        if (strlen($data) == 0) {
            $incorrectdata = true;
        } else if ($data == null) {
            $incorrectdata = true;
        } else if (empty($data)) {
            $incorrectdata = true;
        }

        return $incorrectdata;
    }

    public static function validateEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validateNumberOnly($name)
    {
        if (preg_match("/^([0-9' ]+)$/", $name)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validatePin($pin)
    {
        if (self::validateNumberOnly($pin) && (strlen($pin) == 4)) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateUniqueID($length)
    {
        return Str::random($length);
    }

    public static function checkIfCodeisInDB($tableName, $field, $pubkey)
    {
        $count = DB::table($tableName)
            ->where($field, $pubkey)
            ->count();
        return $count > 0;
    }

    public static function generateShortKey($strength){
        $input = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $output = static::generate_string($input, $strength);

        return $output;
    }
    public static function generate_string($input, $strength){
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
    
        return $random_string;
    }

    public static function getIPAddress()
    {
        return request()->ip();
    }

    public static function Password_encrypt($user_pass)
    {
        return bcrypt($user_pass);
    }

    public static function getLoc($userIp){
        $url = "http://ipinfo.io/".$userIp."/geo";
        $json     = file_get_contents($url);
        $json     = json_decode($json, true);
        // $country  = ($json['country']) ?  $json['country'] : "";
        // $region   = ($json['region']) ? $json['region'] : "";
        // $city     = ($json['city']) ? $json['city'] : "";
        
        if (array_key_exists('loc', $json) ){
            $location = ($json['loc']) ? $json['loc'] : "";

        }else{
            $location = "";
        }

        return $location;
    }

    public static function greetUsers(){
        $welcome_string="Welcome!";
        $numeric_date=date("G");

        //Start conditionals based on military time
        if($numeric_date>=0&&$numeric_date<=11)
        $welcome_string="🌅 Good Morning";
        else if($numeric_date>=12&&$numeric_date<=17)
        $welcome_string="☀️ Good Afternoon";
        else if($numeric_date>=18&&$numeric_date<=23)
        $welcome_string="😴 Good Evening";

        return $welcome_string;
    }

    public static function checkPassword($password, $storedPass){
        if (Hash::check($password, $storedPass)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getSingleColumnFromField($tableName, $column, $fieldName, $fieldValue)
    {
        $result = DB::table($tableName)
            ->where($fieldName, $fieldValue)
            ->value($column);

        return $result;
    }

    public static function getMultipleColumFromField($tableName, $columns, $whereClause, $whereValue = [])
    {
        $query = DB::table($tableName)
            ->select(DB::raw($columns))
            ->whereRaw($whereClause, $whereValue)
            ->first();

        return $query;
    }

    public static function deleteTableImage($image, $path)
    {
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            // Handle deleting remote image if necessary.
        } else {
            $filepath = public_path("assets/images/$path/$image");

            if (file_exists($filepath)) {
                Storage::delete("assets/images/$path/$image");
                return true;
            }
        }
        return true;
    }

    public static function generateUniquePubKey($tableName, $field)
    {
        $loop = 0;
        
        while ($loop == 0) {
            $userKey = "USDTAFR" . static::generatePubKey(37) . $tableName;

            if (static::checkIfCodeIsInDB($tableName, $field, $userKey)) {
                $loop = 0;
            } else {
                $loop = 1;
                break;
            }
        }

        return $userKey;
    }

    public static function generatePubKey($strength){
        $input = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $output = static::generate_string($input, $strength);

        return $output;
    }
    

    public static function generateNumericKey($strength){
        $input = "01234567890987654321";
        $output = static::generate_string($input, $strength);

        return $output;
    }

    public static function generateUniqueShortKey($tableName, $field){
        $loop = 0;
        while ($loop == 0){
            $userKey = "USAFR".static::generateShortKey(5);
            if ( static::checkIfCodeisInDB($tableName, $field ,$userKey) ){
                $loop = 0;
            }else {
                $loop = 1;
                break;
            }
        }
        return $userKey;
    }

    public function validatePhone( $value)
    {
        return preg_match('/^[0-9]{11}+$/', $value);
    }

    public static function checkIsEmailorPhone($userIdentity)
    {
        $phone = static::validatePhone($userIdentity) ? 2 : null;
        $email = filter_var($userIdentity, FILTER_VALIDATE_EMAIL) ? 1 : null;

        if ($phone) {
            return $phone;
        }

        if ($email) {
            return $email;
        }

        return null;
    }

    public static function checkIfExist($table, $field, $data)
    {
        $result = DB::table($table)
            ->where($field, $data)
            ->exists();

        return $result;
    }

    public static function changeStatus($table, $status, $field, $data)
    {
        $result = DB::table($table)
            ->where($field, $data)
            ->update(['status' => $status]);

        if ($result !== false) {
            return $result > 0 ? $result : "no";
        }

        return false;
    }

    public static function date_duration($date)
    {
        $date1 = Carbon::parse($date);
        $date2 = Carbon::now();
        $interval = $date1->diffForHumans($date2);
        return $interval;
    }

    public static function validateDate($date, $format = 'm-d-Y')
    {
        return \Illuminate\Support\Facades\Date::createFromFormat($format, $date) !== false;
    }

    public static function validateTime($time, $format = 'H:i')
    {
        return \Illuminate\Support\Facades\Date::createFromFormat($format, $time) !== false;
    }

    public static function validateDate2($date, $format = 'Y-m-d')
    {
        return \Illuminate\Support\Facades\Date::createFromFormat($format, $date) !== false;
    }

    public static function validateDateTime($date, $format = 'Y-m-d H:i:s')
    {
        return \Illuminate\Support\Facades\Date::createFromFormat($format, $date) !== false;
    }

    public static function gettheTimeAndDate($time)
    {
        return Carbon::createFromTimestamp($time)->format('d/M/Y h:ia');
    }

    public static function gettheDate($time)
    {
        return Carbon::createFromTimestamp($time)->format('d/M/Y');
    }

    public static function gettheDateFormat($time)
    {
        return Carbon::createFromTimestamp($time)->format('Y-m-d');
    }

    public static function uploadImage($file, $path)
    {
        if ($file->isValid()) {
            $img_ex_lc = strtolower($file->getClientOriginalExtension());
            $allowed_exs = ['jpg', 'jpeg', 'svg', 'png', 'gif', 'webp', 'jiff'];

            if (in_array($img_ex_lc, $allowed_exs)) {
                $new_img_name = uniqid("CNG-IMG-", true) . '.' . $img_ex_lc;

                // Store the image in the specified path
                Storage::putFileAs($path, $file, $new_img_name);

                return $new_img_name;
            } else {
                // Handle unsupported image types
                // You can return an error response or throw an exception
            }
        } else {
            // Handle file upload errors
            // You can return an error response or throw an exception
        }
    }

    public static function deleteImage($path, $file_name)
    {
        $file_path = "$path/$file_name";

        if (Storage::exists($file_path)) {
            Storage::delete($file_path);
            return true;
        }

        return false;
    }

    public static function getNumberOfDaysLeft($expiry_time)
    {
        $current = Carbon::now();
        $target = Carbon::parse($expiry_time);

        if ($target > $current) {
            return $current->diffInDays($target);
        } else {
            return 0;
        }
    }

    public static function showPost($text)
    {
        $text = $text ?? '';
        $text = str_replace("\r\n", '', $text);
        $text = trim(preg_replace('/\t+/', '', $text));

        // Convert newline characters to HTML line breaks
        $text = nl2br($text);

        return $text;
    }

    public static function stripFirstZeroInPhoneNo($phone)
    {
        if (strpos($phone, "+") !== false) {
            $phone = str_replace("+", "", $phone);
        }

        if (strpos(substr($phone, 0, 4), "234") !== false) {
            return $phone;
        } else {
            $strnew = ltrim($phone, '0');
            $output = "234" . $strnew;
            return $output;
        }
    }

}