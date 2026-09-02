<?php
/**
 * SendTokenMulti - یک قالب، چند گیرنده، مقادیر متفاوت.
 *
 * پارامترها اینجا آرایه‌اند، نه p1 تا p10. درایه اول به {1} می‌نشیند،
 * دومی به {2} و همین‌طور تا آخر: ترتیب از شماره جای‌گاه می‌آید، نه از
 * جایی که در متن قالب دیده می‌شود.
 *
 * جز افزونه cURL که در هر نصب PHP هست، به چیزی وابسته نیست. کپی کنید و
 * در پروژه خودتان اجرا کنید.
 *
 *   SMS_API_KEY=... php examples/v3/standalone/send-token-multi.php
 */

declare(strict_types=1);

// docs:start
// قالب نمونه: «مرسوله شما از {2} تحویل پست شد. بارکد مرسوله پستی: {1}»
$payload = [
    'ApiKey'      => getenv('SMS_API_KEY'),
    'TemplateKey' => 'postcode',
    'Recipients'  => [
        [
            'Destination' => 9121112222,
            'UserTraceId' => 1001,
            'Parameters'  => ['BARCODE-AAA', 'شیراز'],
        ],
        [
            'Destination' => 9121113333,
            'UserTraceId' => 1002,
            'Parameters'  => ['BARCODE-BBB', 'تبریز'],
        ],
    ],
];

$curl = curl_init('https://api.sms-webservice.com/api/V3/SendTokenMulti');
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
    echo "{$message['UserTraceId']} => {$message['FinalText']}\n";
}
// docs:end
