<?php
/**
 * AccountInfo - اعتبار باقی‌مانده و خطوط فعال حساب.
 *
 * سبک‌ترین متد سرویس و بهترین راه آزمودن کلید: چیزی ارسال نمی‌کند،
 * اعتباری مصرف نمی‌کند، و حتی با اعتبار صفر هم جواب می‌دهد.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   SMS_API_KEY=... php examples/v3/standalone/account-info.php
 */

declare(strict_types=1);

// docs:start
$payload = ['ApiKey' => getenv('SMS_API_KEY')];

$curl = curl_init('https://api.sms-webservice.com/api/V3/AccountInfo');
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

echo "اعتبار: {$response['Result']['Credit']}\n";

// هر کدام از این خطوط می‌تواند Sender متدهای ارسال متن باشد.
foreach ($response['Result']['AvailableSenders'] as $line) {
    echo "خط: $line\n";
}
// docs:end
