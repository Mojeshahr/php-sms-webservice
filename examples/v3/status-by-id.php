<?php
/**
 * StatusById - وضعیت پیامک با شناسه‌هایی که متد ارسال برگردانده است.
 *
 * دسته‌ای بپرسید، نه یکی‌یکی. فاصله استعلام‌ها را هم کمتر از چند دقیقه
 * نگذارید، وگرنه به خطای ۲۰ می‌خورید.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   PAYAM_RESAN_API_KEY=... php examples/v3/status-by-id.php
 */

declare(strict_types=1);

// docs:start
$payload = [
    'ApiKey' => getenv('PAYAM_RESAN_API_KEY'),
    'Ids'    => [9903211, 9903212],
];

$curl = curl_init('https://api.sms-webservice.com/api/V3/StatusById');
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

// شرط را روی StatusCode بگذارید، نه روی متن Status. کدهای ۰، ۱، ۲، ۳
// و ۱۰ یعنی هنوز در راه است و باید بعداً دوباره بپرسید، نه اینکه
// دوباره بفرستید.
$pending = [0, 1, 2, 3, 10];

foreach ($response['Result'] as $message) {
    $again = in_array($message['StatusCode'], $pending, true) ? ' (بعداً دوباره بپرسید)' : '';
    echo "{$message['Id']}: {$message['Status']}$again\n";
}
// docs:end
