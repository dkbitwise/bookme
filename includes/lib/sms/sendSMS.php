<?php
require_once "Twilio/autoload.php";
use Twilio\Rest\Client;

function bookme_send_sms_twilio($number, $msg){
    $AccountSid = bookme_get_settings('bookme_sms_accountsid');
    $AuthToken = bookme_get_settings('bookme_sms_authtoken');
    $from = bookme_get_settings('bookme_sms_phone_no');
    try {
        if ($AccountSid && $AuthToken && $from) {
            $client = new Client($AccountSid, $AuthToken);
            $client->account->messages->create(
                $number,
                array(
                    'from' => $from,
                    // the sms body
                    'body' => $msg
                )
            );
        }
    }catch(Exception $e){

    }
}