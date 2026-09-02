<?php
/**
 * SendMultiple - متن و خط فرستنده جدا برای هر گیرنده.
 *
 * برای پیام‌های شخصی‌سازی‌شده که با یک قالب ثابت پوشش داده نمی‌شوند.
 * برخلاف SendBulk، اینجا Text و Sender در سطح هر گیرنده تعریف می‌شوند.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   SMS_API_KEY=... SMS_SENDER=... php examples/v3/standalone/send-multiple.php
 */

declare(strict_types=1);

// docs:start
$sender = (int) getenv('SMS_SENDER');

$payload = [
    'ApiKey' => getenv('SMS_API_KEY'),

    // حداکثر ۹۹ گیرنده، هر کدام با متن خودش.
    'Recipients' => [
        [
            'Sender'      => $sender,
            'Destination' => 9121112222,
            'Text'        => 'آقای محمدی، سفارش شما ارسال شد.',
            'UserTraceId' => 1001,
        ],
        [
            'Sender'      => $sender,
            'Destination' => 9121113333,
            'Text'        => 'خانم رضایی، سفارش شما ارسال شد.',
            'UserTraceId' => 1002,
        ],
    ],
];

$curl = curl_init('https://api.sms-webservice.com/api/V3/SendMultiple');
curl_setopt_array($curl, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json; charset=utf-8'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
]);

$raw = curl_exec($curl);
if ($raw === false) {
    exit('خطای شبکه: ' . curl_error($curl) . "\n");
}
curl_close($curl);

$response = json_decode($raw, true);

// سرویس همیشه HTTP 200 می‌دهد، حتی وقتی درخواست شکست خورده. موفقیت را
// فقط از فیلد Success بخوانید.
if (empty($response['Success'])) {
    exit("ناموفق. کد {$response['ErrorCode']}: {$response['Error']}\n");
}

foreach ($response['Result'] as $message) {
    echo "{$message['UserTraceId']} => شناسه {$message['Id']}\n";
}
// docs:end
