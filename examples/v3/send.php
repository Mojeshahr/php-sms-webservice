<?php
/**
 * Send - ارسال یک متن به یک یا چند شماره با GET.
 *
 * ساده‌ترین متد ارسال. برای محیط عملیاتی send-bulk.php ترجیح دارد، چون
 * اینجا کلید API داخل نشانی می‌نشیند و در لاگ وب‌سرور، تاریخچه مرورگر و
 * هدر Referer ثبت می‌شود.
 *
 *   php examples/v3/send.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';

$config = pr_config();

$response = pr_get('Send', [
    'ApiKey' => $config['api_key'],
    'Sender' => $config['sender'],

    // متن را خام بدهید. http_build_query داخل pr_get دقیقاً یک بار
    // url-encode می‌کند. اگر اینجا هم urlencode() بزنید، گیرنده
    // «%D8%A7%D8%B3%D8%AA» می‌بیند نه متن فارسی.
    'Text' => 'کد تأیید شما ۱۲۳۴۵۶ است',

    // رشته جداشده با کاما، نه آرایه. بدون صفر ابتدایی. حداکثر ۹۹ شماره.
    'Recipients' => '9121112222,9121113333',
]);

pr_show($response);
