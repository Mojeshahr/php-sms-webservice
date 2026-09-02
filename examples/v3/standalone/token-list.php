<?php
/**
 * TokenList - قالب‌های حساب، با کلید و متن و وضعیت تأییدشان.
 *
 * برای پیدا کردن TemplateKey که متدهای ارسال قالب لازم دارند. این متد
 * هم مثل AccountInfo از بررسی اعتبار معاف است.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   SMS_API_KEY=... php examples/v3/standalone/token-list.php
 */

declare(strict_types=1);

// docs:start
$payload = ['ApiKey' => getenv('SMS_API_KEY')];

$curl = curl_init('https://api.sms-webservice.com/api/V3/TokenList');
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

// فقط قالبی که Status آن ۲ است قابل ارسال است. ۱ یعنی در انتظار بررسی
// و ۳ یعنی رد شده.
foreach ($response['Result'] as $template) {
    $sendable = $template['Status'] === 2 ? 'قابل ارسال' : 'قابل ارسال نیست';
    echo "{$template['Key']} ($sendable): {$template['TextTemplate']}\n";
}
// docs:end
