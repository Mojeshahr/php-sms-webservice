<?php

/**
 * SendTokenSingleConcatParam - ارسال قالب با پارامترها در یک رشته.
 *
 * همان ارسال قالب به یک شماره است، ولی به‌جای p1 تا p10، مقدارها را
 * به ترتیب جای‌گاه در رشته p و با جداکننده ; می‌فرستید.
 * برای محیط عملیاتی این واریانت POST را به GET ترجیح بدهید تا کلید
 * حساب و مقادیر قالب داخل نشانی و لاگ وب‌سرور نروند.
 *
 * جز افزونه cURL به چیزی وابسته نیست. کپی کنید و در پروژه خودتان اجرا کنید.
 *
 *   PAYAM_RESAN_API_KEY=... php examples/v3/send-token-single-concat-param.php
 *
 * @package PayamResan_Examples
 */

declare(strict_types=1);

// docs:start
$baseUrl = 'https://www.payam-resan.com/api/V3';

$payload = [
    'ApiKey'      => getenv('PAYAM_RESAN_API_KEY'),
    'TemplateKey' => 'postcode',
    'Destination' => 9121112222,

    // قالب نمونه: «مرسوله شما از {2} تحویل پست شد. بارکد مرسوله پستی: {1}».
    // مقدار اول برای {1} است و مقدار دوم برای {2}، نه ترتیب ظاهرشان در متن.
    'p'           => 'BARCODE-AAA;شیراز',
];

$curl = curl_init($baseUrl . '/SendTokenSingleConcatParam');
curl_setopt_array(
    $curl,
    [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER     => [ 'Content-Type: application/json; charset=utf-8' ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
    ]
);

$raw = curl_exec($curl);
if (false === $raw) {
    exit('خطای شبکه: ' . curl_error($curl) . "\n");
}
unset($curl);

$response = json_decode($raw, true);

// پاسخ HTTP 200 هم می‌تواند ناموفق باشد. موفقیت را از فیلد Success بخوانید.
if (empty($response['Success'])) {
    exit("ناموفق. کد {$response['ErrorCode']}: {$response['Error']}\n");
}

foreach ($response['Result'] as $message) {
    echo "شناسه {$message['Id']} از خط {$message['Sender']}\n";
    echo "متن نهایی: {$message['FinalText']}\n";
}
// docs:end
