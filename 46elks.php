<?php

date_default_timezone_set('Europe/Stockholm');

require_once('library/API_46elks.php');

if (!file_exists('46elks_config.xml'))
    die("Config file missing");

$config = simplexml_load_file('46elks_config.xml');

$api = new API_46elks(array(
    'config' => array(
        'api_username' => $config->username,
        'api_password' => $config->password,
        'api_base_url' => $config->base_url
    )
));

/* Allocate a new phone number
    $number = $api->allocatePhoneNumber();
*/

/* List allocated phone numbers
    $numbers = $api->listPhoneNumbers();
*/

/* Send SMS message
    $response = $api->sendSmsMessage('Originator', '+46xxxxxxxxx', "SMS message text! :D");
*/

/* Initiate voice call
    $response = $api->initVoiceCall($n->number, '+46xxxxxxxxx', json_encode(
        array(
            'play' => 'http://example.org/play.ogg'
        )
    ));
*/
