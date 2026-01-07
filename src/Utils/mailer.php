<?php

require_once __DIR__ . '/../config.php';

function sendMailViaMailgun(string $to, string $subject, string $text): bool
{
    $url = "https://api.mailgun.net/v3/" . MAILGUN_DOMAIN . "/messages";

    $postData = http_build_query([
        'from'    => MAIL_FROM,
        'to'      => $to,
        'subject' => $subject,
        'text'    => $text,
    ]);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => 'api:' . MAILGUN_API_KEY,
        CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
        CURLOPT_TIMEOUT        => 10,
    ]);

    curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode >= 200 && $httpCode < 300);
}
