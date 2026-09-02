<?php
/**
 * Send - ساده‌ترین ارسال، یک متن به چند شماره با یک درخواست GET.
 *
 * برای آزمایش سریع خوب است. در محیط عملیاتی SendBulk را بردارید: کلید
 * را از نشانی بیرون می‌برد و برای هر گیرنده شناسه پی‌گیری می‌پذیرد.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   PAYAM_RESAN_API_KEY=... PAYAM_RESAN_SENDER=... php examples/v3/send.php
 */

declare(strict_types=1);

// docs:start
$query = [
    'ApiKey' => getenv('PAYAM_RESAN_API_KEY'),
    'Sender' => (int) getenv('PAYAM_RESAN_SENDER'),
    'Text'   => 'کد تأیید شما ۱۲۳۴۵۶ است',

    // رشته جداشده با کاما، نه آرایه. حداکثر ۹۹ شماره.
    'Recipients' => '9121112222,9121113333',
];

// http_build_query دقیقاً یک بار url-encode می‌کند. اگر متن را خودتان
// هم پیش از این encode کنید، پیامک با نویسه‌های %D8 به گوشی می‌رسد.
$url = 'https://api.sms-webservice.com/api/V3/Send?' . http_build_query($query);

$curl = curl_init($url);
curl_setopt_array($curl, [
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
    echo "شناسه {$message['Id']}\n";
}
// docs:end
