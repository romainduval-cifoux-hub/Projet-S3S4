<?php

require_once __DIR__ . '/../config.php';

function mailgunApiBase(): string
{
    return 'https://api.eu.mailgun.net/v3/';
}

function sendMailViaMailgun(string $to, string $subject, string $text, ?string $replyTo = null): bool
{
    $url = mailgunApiBase() . MAILGUN_DOMAIN . "/messages";

    $data = [
        'from'    => MAIL_FROM,
        'to'      => $to,
        'subject' => $subject,
        'text'    => $text,
    ];

    if ($replyTo) {
        $data['h:Reply-To'] = $replyTo;
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => 'api:' . MAILGUN_API_KEY,
        CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
        CURLOPT_TIMEOUT        => 10,
    ]);

    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    error_log("Mailgun URL=" . $url);
    error_log("Mailgun HTTP=" . $httpCode);
    if ($curlErr) error_log("Mailgun cURL error=" . $curlErr);
    if (is_string($response) && $response !== '') error_log("Mailgun resp=" . $response);

    return ($httpCode >= 200 && $httpCode < 300);
}

function sendMailViaMailgunWithPdf(string $to, string $subject, string $text, string $pdfPath, ?string $replyTo = null): bool
{
    $url = mailgunApiBase() . MAILGUN_DOMAIN . "/messages";

    $postFields = [
        'from'    => MAIL_FROM,
        'to'      => $to,
        'subject' => $subject,
        'text'    => $text,
    ];

    if ($replyTo) {
        $postFields['h:Reply-To'] = $replyTo;
    }

    $postFields['attachment'] = new CURLFile($pdfPath, 'application/pdf', basename($pdfPath));

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postFields,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => 'api:' . MAILGUN_API_KEY,
        CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
        CURLOPT_TIMEOUT        => 20,
    ]);

    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("Mailgun URL=" . $url);
    error_log("Mailgun HTTP=" . $httpCode);
    if ($curlErr) error_log("Mailgun cURL error=" . $curlErr);
    if (is_string($response) && $response !== '') error_log("Mailgun resp=" . $response);

    return ($httpCode >= 200 && $httpCode < 300);
}
