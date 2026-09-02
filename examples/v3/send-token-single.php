<?php
/**
 * SendTokenSingle - ارسال قالب به یک شماره، با POST.
 *
 * مسیر معمول رمز یک‌بارمصرف. خط فرستنده را سامانه از روی خود قالب انتخاب
 * می‌کند، پس Sender ورودی ندارد.
 *
 * برای OTP همین واریانت POST را به کار ببرید، نه GET: نه کلید حساب و نه
 * خود رمز در نشانی و لاگ وب‌سرور ثبت نمی‌شوند.
 *
 * کلید قالب را با token-list.php پیدا کنید.
 *
 *   php examples/v3/send-token-single.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';

$config = pr_config();

// docs:start
$response = pr_post('SendTokenSingle', [
    'ApiKey'      => $config['api_key'],
    'TemplateKey' => 'YOUR-TEMPLATE-KEY',
    'Destination' => 9121112222,

    // جای‌گاه‌های {1}، {2}، … قالب. فقط به تعداد جای‌گاه‌ها بفرستید.
    // تا p10 پشتیبانی می‌شود.
    'p1' => '123456',
]);

if (empty($response['Success'])) {
    pr_show($response);
    exit(1);
}

// FinalText متن نهایی پس از جای‌گذاری پارامترها در قالب است.
foreach ($response['Result'] as $message) {
    echo "شناسه: {$message['Id']}\n";
    echo "خط فرستنده: {$message['Sender']}\n";
    echo "متن نهایی: {$message['FinalText']}\n";
}
// docs:end
