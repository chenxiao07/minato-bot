<?php

include('phpqrcode/qrlib.php'); 

$json_string = file_get_contents('php://input');
$json_object = json_decode($json_string);

foreach ($json_object->events as $event) {
    if('message' == $event->type){
        reply_message($event->replyToken, $event->message->text);
    }
}

function qr_code ($txt)
{
    $name = uniqid(rand()).".png";

    QRcode::png($txt, $name);

    $post = array(
        'replyToken' => $token,
        'messages' => array(
            array(
                'type' => 'image',
                'originalContentUrl' => 'https://minato-bot.herokuapp.com/'.$name,
                'previewImageUrl' => 'https://minato-bot.herokuapp.com/'.$name
            )
        )
    );

    return $post;
}

function reply_message($token, $txt) {
    $url = 'https://api.line.me/v2/bot/message/reply';
    $channel_access_token = 'P5pLeFX5jRoU9l9NNGPDDbceTn92PiKdIb/rrB9U6ecfQKTT67W2q5GCnEgH66whzuxb3yzfbLdecax3sMtzWkBY9cYBmt+NvU7DfOJ19rEFI0Mz5vtGabhp0EanclclCgMvvZT9ydvHnYl0JDKvWwdB04t89/1O/w1cDnyilFU=';
    $headers = array(
        'Content-Type: application/json',
        "Authorization: Bearer {$channel_access_token}"
    );

    $post = qr_code($txt);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl);
}