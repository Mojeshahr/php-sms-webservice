<?php
/**
 * SendBulk - یک متن به چند گیرنده، هر کدام با شناسه پی‌گیری خودتان.
 *
 * روش پیشنهادی برای ارسال عملیاتی. کلید در بدنه درخواست می‌رود نه در
 * نشانی، و برای هر گیرنده UserTraceId می‌پذیرد تا گزارش تحویل را بدون
 * نگه‌داشتن Id سامانه بگیرید.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   PAYAM_RESAN_API_KEY=... PAYAM_RESAN_SENDER=... php examples/v3/send-bulk.php
 */

declare(strict_types=1);

// docs:start
$apiKey = getenv('PAYAM_RESAN_API_KEY');
$sender = (int) getenv('PAYAM_RESAN_SENDER');

$payload = [
    'ApiKey' => $apiKey,
    'Sender' => $sender,
    'Text'   => 'سفارش شما ثبت شد.',

    // حداکثر ۹۹ گیرنده. UserTraceId شناسه خودتان است و در گزارش‌گیری
    // بعدی همان را پس می‌گیرید، پس معمولاً کلید رکورد پایگاه داده.
    'Recipients' => [
        ['Destination' => 9121112222, 'UserTraceId' => 1001],
        ['Destination' => 9121113333, 'UserTraceId' => 1002],
    ],
];

$curl = curl_init('https://api.sms-webservice.com/api/V3/SendBulk');
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
