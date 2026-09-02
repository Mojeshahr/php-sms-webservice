<?php
/**
 * SendTokenSingle - ارسال قالب به یک شماره، با بدنه JSON.
 *
 * مسیر معمول رمز یک‌بارمصرف. خط فرستنده ورودی ندارد؛ سامانه آن را از
 * روی خود قالب برمی‌دارد. همین واریانت POST را به کار ببرید، نه GET:
 * در GET هم کلید حساب و هم خود رمز داخل نشانی و لاگ وب‌سرور می‌نشینند.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   SMS_API_KEY=... php examples/v3/standalone/send-token-single.php
 */

declare(strict_types=1);

// docs:start
$payload = [
    'ApiKey' => getenv('SMS_API_KEY'),

    // کلید قالبِ تأییدشده. با token-list آن را پیدا کنید.
    'TemplateKey' => 'verifycode',
    'Destination' => 9121112222,

    // جای‌گاه {1} قالب. تا p10 پشتیبانی می‌شود و فقط به تعداد
    // جای‌گاه‌های خود قالب بفرستید.
    'p1' => '123456',
];

$curl = curl_init('https://api.sms-webservice.com/api/V3/SendTokenSingle');
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

// این متد UserTraceId در ورودی ندارد، پس در پاسخ null برمی‌گردد. اگر
// شناسه پی‌گیری لازم دارید، SendTokenMulti را حتی برای یک گیرنده هم
// می‌شود به کار برد.
foreach ($response['Result'] as $message) {
    echo "شناسه {$message['Id']} از خط {$message['Sender']}\n";
    echo "متن نهایی: {$message['FinalText']}\n";
}
// docs:end
