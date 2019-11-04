<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client as GuzClient;
use Twilio\Rest\Client;
use URL;

class Reminder extends Model
{

    /**
     * Get the scripture of the day
     * @param $to_whatsapp
     * @return string
     */
    public function todaysScripture($to_whatsapp = false) {
        $client = new GuzClient([
            'headers' => [ 
                // 'Content-Type' => 'application/json',
                'x-rapidapi-host' => 'uncovered-treasure-v1.p.rapidapi.com',
                'x-rapidapi-key' => '83cd1be657mshe250bae2394d643p1bcb74jsn8fd9ed783508'
            ]
        ]);

        $response = $client->get( 'https://uncovered-treasure-v1.p.rapidapi.com/today'
        );

        $data = json_decode( $response->getBody()->getContents() );

        $data = $data->results[0];
        $scriptures = "Scripture of the day: ".implode(",",$data->scriptures);
        $teaching = "<br> Teaching: ".$data->text;
        $topics = (count($data->topics)>0)? "<br> Topics".implode(",",$data->topics):"";
        $response = $scriptures.$teaching.$topics;

        if($to_whatsapp){
            return "Your ScriptureOfTheDay code is ".str_replace(" ","",$data->scriptures[0])." ".URL::to('scripture');;
        }
        return $response;
    }

    /**
     * Send reminder through Whatspp
     */
    public function sendReminder(){
        $sid    = getenv('ACCOUNT_SID');
        $token  = getenv('TWILIO_TOKEN');
        $sandbox_number=getenv('WHATSAPP_SANDBOX_NUMBER');
        $subscriber_number = "+254702558716";
        $message = $this->todaysScripture(true);

        $twilio = new Client($sid, $token);
        $message = $twilio->messages
                        ->create("whatsapp:".$subscriber_number,
                                array(
                                    "from" => "whatsapp:".$sandbox_number,
                                    "body" => $message
                                )
                        );
    }

}
