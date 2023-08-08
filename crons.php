<?php
//exit;

set_time_limit(0);
$start_time = microtime(true);
$timeOut = 58; // seconds

$projectUrl = 'https://notes.fogito.com';

function charge($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_exec($ch);
    curl_close($ch);
}

$charge_urls = array(
    //card announce for card users
    $projectUrl . '/crons/preparenotification',
//    $projectUrl . '/crons/sendmailnotification',

);

foreach ($charge_urls as $url) {
    charge($url);
}
?>
