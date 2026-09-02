<?php
/**
 * GetInbox - پیامک‌هایی که کاربران به خطوط حساب شما فرستاده‌اند.
 *
 * این یک استعلام است، نه webhook: سامانه چیزی به سرور شما نمی‌فرستد و
 * باید خودتان دوره‌ای صدایش بزنید. فاصله را کمتر از چند دقیقه نگذارید،
 * وگرنه به خطای ۲۰ می‌خورید.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   PAYAM_RESAN_API_KEY=... php examples/v3/get-inbox.php
 */

declare(strict_types=1);

// docs:start
$payload = ['ApiKey' => getenv('PAYAM_RESAN_API_KEY')];

$curl = curl_init('https://api.sms-webservice.com/api/V3/GetInbox');
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

foreach ($response['Result'] as $sms) {
    // نام فیلد فرستنده در خود سرویس Form است، نه From. دنبال From نگردید.
    echo "{$sms['Time']}  {$sms['Form']} -> {$sms['To']}: {$sms['Text']}\n";

    // هر فراخوانی ممکن است پیام‌های قبلی را دوباره بدهد. Id را ذخیره
    // کنید تا یک پیام دو بار پردازش نشود.
}
// docs:end
