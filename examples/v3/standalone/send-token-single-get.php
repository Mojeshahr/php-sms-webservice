<?php
/**
 * SendTokenSingle با GET - همان ارسال قالب، با ورودی در نشانی.
 *
 * برای آزمایش دستی مناسب است، برای محیط عملیاتی نه: در GET هم کلید
 * حساب و هم مقدار رمز یک‌بارمصرف داخل نشانی می‌نشینند و در لاگ
 * وب‌سرور و هدر Referer ثبت می‌شوند. واریانت POST را بردارید.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   SMS_API_KEY=... php examples/v3/standalone/send-token-single-get.php
 */

declare(strict_types=1);

// docs:start
$query = [
    'ApiKey'      => getenv('SMS_API_KEY'),
    'TemplateKey' => 'verifycode',
    'Destination' => 9121112222,
    'p1'          => '123456',
];

$url = 'https://api.sms-webservice.com/api/V3/SendTokenSingle?' . http_build_query($query);

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
    echo "شناسه {$message['Id']}، متن نهایی: {$message['FinalText']}\n";
}
// docs:end
