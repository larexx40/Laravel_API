<?php
namespace App\Config;

use Illuminate\Support\Facades\Config;

$appname = config('app.name');
class APIUserResponse
{
    // General errors
    public static $methodUsedNotAllowed = "Method Used is not valid";
    public static $respondValidationError = "Incorrect Information Sent";
    public static $invalidUserDetail = "Invalid Email or password";
    public static $invalidAdmin = "Invalid admin id or email";
    public static $loginSuccessful = "Login Successful";
    public static $unauthorizedToken = "User not authorized";
    public static $unauthorizedAccess = "You did not have permission to access this endpoint";

    public static $welcomeMessage;
    public function __construct()
    {
        self::$welcomeMessage = "Welcome to " . Config::get('app.name');
    }

    // Login
    public static $loginFailedError = "One or both of the data provided is invalid";
    public static $invalidLoginError = "Invalid email or password";
    public static $suspendReason = "Your account has been suspended";
    public static $frozenAccount = "Your account has been frozen";
    public static $bannedAccount = "Your account has been banned";
    public static $userNotAllowed = "User not allowed";
    public static $validEmail = "Your Email isn't Valid";
    public static $deletedUser = "User Account has been deleted";
    public static $passwordUpdated = "Password Updated";


    public static $unExpectedError = "Unexpected error occurred";
    // DB error
    public static $dbInsertError = "Error inserting to the database";
    public static $dbUpdatingError = "Error updating the database record";
    public static $nothingToUpdate = "Nothing to update";
    public static $deletingError = "Error deleting the database record";
    public static $dbQueryError = "Error querying the database";
    public static $dbOperationError = "Error performing database operation";


    // Forgot password
    public static $forgotMailSent = "Recovery Mail sent successfully, kindly check your mail";
    public static $errorOccurred = "An Error occurred, Please contact support";
    public static $emailNotExist = "Account with this email does not exist";

    // Image
    public static $invalidImageSent = "Invalid image sent";
    public static $imageTooLarge = "Image too large";
    public static $imageTypeNotAllowed = "Image type not allowed";
    public static $unknownErrorImageUpload = "Unknown error occurred";
    public static $imageUploadFailed = "Error uploading image";

    // Signup
    public static $invalidPassword = "Invalid password";
    public static $incorrectPassword = "Incorrect password";
    public static $invalidEmail = "Invalid email";
    public static $emailExist = "Email already exists";
    public static $usernameExist = "Username already exists";
    public static $invalidPhone = "Invalid phone number";
    public static $phoneExist = "Phone number already exists";
    public static $weakPassword = "Password too weak";
    public static $registerFail = "Unable to register";
    public static $unauthorizedUser = "User not authorized";
    public static $unableToVerified = "Unable to verify mail";

    // Email verification
    public static $tokenExpired = "OTP Expired";
    public static $userEmailNotExist = "Email does not exist";
    public static $successEmail = "Email verified successfully";
    public static $sendOTPError = "Unable to send OTP";
    public static $alreadyVerified = "Email already verified";
    public static $unableToVerifyMail = "Unable to verify email";

    // Phone verification
    public static $successPhoneVerify = "Phone verified successfully";
    public static $phoneAlreadyVerified = "Phone already verified";
    public static $unableToVerifyPhone = "Unable to verify phone";


    // OTP
    public static $invalidToken = "Invalid token";
    public static $forgotPasswordOTP = "Forgot password OTP sent to your mail";
    public static $OTPSentViaMail = "OTP sent to your mail";
    public static $OTPSentViaSMS = "OTP sent to your phone";
    public static $invalidOTP = "Incorrect token";
    public static $OTPExpire = "OTP expired";
    public static $OTPUsed = "Code has already been used";
    public static $validOTP = "OTP Valid";

    // Pin
    public static $invalidPin = "Invalid Pin";
    public static $pinSet = "Pin set successfully";

    // Success message
    public static $registerSuccess = "Register Successful";
    public static $profileUpdateSuccessful = "Profile updated successfully";
    public static $profilePicUpdateSuccessful = "Profile picture updated";

    // Password
    public static $resetPasswordMessage = "Password reset successfully";
    public static $changePasswordMessage = "Password changed successfully";
    public static $validatePassword = "Password too weak";

    // User
    public static $unableToGetUserDetail = "Unable to get user details";
    public static $emailAlreadyVerified = "Email already verified";
    public static $invalidUserIdentity = "User with account not found";
    public static $invalidUserType = "Invalid user type";
    public static $getRequestFetched = "Request Fetched";
    public static $getRequestNoRecords = "No Records Found";

    // Admin
    public static $adminDetailsUpdate = "Admin details updated successfully";
    public static $adminAdded = "New admin added successfully";
    public static $invalidAdminId = "Invalid admin id";
    public static $getAdminError = "Unable to fetch admin details";
    public static $errorResetPass = "Unable to reset admin password";
    public static $sendResetPassword = "Unable to send a new password";
    public static $resetPasswordMailSent = "New Password sent successfully";
    public static $deleteSuperAdmin = "Super admin cannot be deleted";
    public static $deleteAdmin = "Admin deleted successfully";

    // Status
    public static $statusChangedMessage = "Status changed successfully";
    public static $nothingChanged = "Nothing changed, pass a new value";
    public static $invalidStatusSent = "Please send a valid status";

    // Delete success
    public static $adminDelete = "Admin deleted successfully";
    public static $adminLevelDelete = "Admin level deleted successfully";

    // General input error
    public static $invalidImageType = "Invalid image type sent";
    public static $invalidImageUrl = "Invalid Image URL sent";

    //Bank Account
    public static $addBankAccount = "Bank account added successfully";



}
