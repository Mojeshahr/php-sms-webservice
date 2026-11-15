<?php

/**
 * SendTokenSingleConcatParam با GET - پارامترهای قالب در رشته p داخل نشانی.
 *
 * همان واریانت POST است، با ورودی در query. مقدارهای قالب به ترتیب
 * جای‌گاه و با جداکننده ; در رشته p قرار می‌گیرند.
 * برای محیط عملیاتی POST را ترجیح بدهید: در GET کلید حساب و مقادیر
 * قالب داخل نشانی و لاگ وب‌سرور می‌نشینند.
 *
 * جز افزونه cURL به چیزی وابسته نیست. کپی کنید و در پروژه خودتان اجرا کنید.
 *
 *   PAYAM_RESAN_API_KEY=... php examples/v3/send-token-single-concat-param-get.php
 *
 * @package PayamResan_Examples
 */

declare(strict_types=1);

// docs:start
$baseUrl = 'https://www.payam-resan.com/api/V3';

$query = [
    'ApiKey'      => getenv('PAYAM_RESAN_API_KEY'),
    'TemplateKey' => 'postcode',
    'Destination' => 9121112222,

    // قالب نمونه: «مرسوله شما از {2} تحویل پست شد. بارکد مرسوله پستی: {1}».
    'p'           => 'BARCODE-AAA;شیراز',
];

// http_build_query رشته p، از جمله ; و متن فارسی، را دقیقاً یک بار encode می‌کند.
$url = $baseUrl . '/SendTokenSingleConcatParam?' . http_build_query($query);

$curl = curl_init($url);
curl_setopt_array(
    $curl,
    [
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
    echo "شناسه {$message['Id']}، متن نهایی: {$message['FinalText']}\n";
}
// docs:end
