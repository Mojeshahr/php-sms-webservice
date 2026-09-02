<?php
/**
 * StatusByUserTraceId - وضعیت پیامک با شناسه‌هایی که خودتان داده‌اید.
 *
 * اگر UserTraceId را کلید رکورد پایگاه داده خودتان بگذارید، دیگر لازم
 * نیست Id سامانه را ذخیره کنید. این متد راه امن تشخیص ارسال تکراری هم
 * هست: بعد از قطع ارتباط، اول اینجا بپرسید ثبت شده یا نه.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   PAYAM_RESAN_API_KEY=... php examples/v3/status-by-user-trace-id.php
 */

declare(strict_types=1);

// docs:start
$payload = [
    'ApiKey'       => getenv('PAYAM_RESAN_API_KEY'),
    'UserTraceIds' => [1001, 1002],
];

$curl = curl_init('https://api.sms-webservice.com/api/V3/StatusByUserTraceId');
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
    // کد ۸ یعنی این شناسه در حساب شما نیست. بعد از یک timeout، همین
    // یعنی ارسال ثبت نشده و می‌توانید با خیال راحت دوباره بفرستید.
    if ($message['StatusCode'] === 8) {
        echo "{$message['UserTraceId']}: ثبت نشده\n";
        continue;
    }

    echo "{$message['UserTraceId']}: {$message['Status']}\n";
}
// docs:end
