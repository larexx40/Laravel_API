<?php
    namespace App\Utilities;
    use App\Repositories\SystemDefaultRepository;
    use App\Utilities\UtilityFunctions;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\Log;
    use Nette\Utils\Helpers;

    class ExternalCalls {
        private $systemDefaultRepository;

        public function __construct() {
            $this->systemDefaultRepository = new SystemDefaultRepository();
        }
        public function sendSMSWithTermi($sendto, $smstosend) {
            $termidata = $this->systemDefaultRepository->getActiveTermi();
            $smssent = false;
            $dnum = substr($sendto, 1);
            $sendto = "234" . $dnum;
            $channel = $termidata['smschannel'];

            $starttimefortoday = strtotime("6:00 PM");
            $endtimefortoday = strtotime("9:20 AM");
            $currenttimeis = time();

            // Check if data is for the next day (10 PM - 8 AM)
            //     if ($starttimefortoday > $endtimefortoday) {
            //     if ($currenttimeis >= $starttimefortoday || $currenttimeis < $endtimefortoday) {
            //         $channel = $termidata['smschannel2'];
            //     }
            // } else if ($currenttimeis >= $starttimefortoday && $currenttimeis <= $endtimefortoday) {
            //     $channel = $termidata['smschannel2'];
            // }

            $arr = [
                "to" => $sendto,
                "sms" => $smstosend,
                "api_key" => $termidata['apikey'],
                "from" => "N-Alert", //$termidata['sendfrom'],
                "type" => $termidata['smstype'],
                "channel" => $channel,
            ];

            // Base URL
            $url = "https://termii.com/api/sms/send";

            $response = Http::post($url, $arr);

            if ($response->failed()) {
                Log::error("Failed to send SMS: " . $response->body());
                $smssent = false;
            } else {
                $theresponse = $response->json();
                if (isset($theresponse['code']) && $theresponse['code'] == "ok") {
                    $smssent = true;
                    $msgid = $theresponse['message_id'];
                    // Later, you can log the SMS sent here.
                } else {
                    $smssent = false;
                }
            }

            return $smssent;
        }

        public function sendSmsWithSimple($phoneno, $message, $channel)
        {
            $activeSimpuSms = $this->systemDefaultRepository->getActiveSimpleSMS();
            $secret_key = $activeSimpuSms->secret_key;
            $channel_id = $activeSimpuSms->channel_id;
            $phone = UtilityFunctions::stripFirstZeroInPhoneNo($phoneno);

            $data = [
                "recipients" => $phone,
                "content" => $message,
                "channel" => $channel,
                "channel_id" => $channel_id,
            ];

            $response = Http::withHeaders([
                'Authorization' => $secret_key,
                'Content-Type' => 'application/json',
            ])->post('https://api.simpu.co/sms/send', $data);

            $responseMessage = $response->json();

            if (isset($responseMessage['status'])) {
                return $responseMessage['status'];
            } else {
                return false;
            }
        }

        public function sendWithZepto($subject, $toemail, $messageinhtml)
        {
            $zeptoDetails = $this->systemDefaultRepository->getActiveZepto();
            $apikey = $zeptoDetails->apikey;
            $emailfrom = $zeptoDetails->emailfrom;
            $appName = Config::get('app.name');
            $body = [
                "from" => ["address" => $emailfrom],
                "to" => [
                    [
                        "email_address" => [
                            "address" => $toemail,
                            "name"=> $appName,
                        ]
                    ]
                ],
                "subject" => $subject,
                "htmlbody" => $messageinhtml
            ];

            $response = Http::withHeaders([
                'Authorization' => $apikey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post('https://api.zeptomail.com/v1.1/email', $body);

            if ($response->successful()) {
                return true;
            } else {
                return false;
            }
        }

        public static function sendWithZ($subject, $toemail, $messageinhtml){
            return true;
        }

    }


