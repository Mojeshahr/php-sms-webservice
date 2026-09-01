<?php
/**
 * SendMultiple - متن متفاوت برای هر گیرنده.
 *
 * برخلاف SendBulk، اینجا Text و Sender در سطح هر گیرنده تعریف می‌شوند.
 * برای پیام‌های شخصی‌سازی‌شده که با یک قالب ثابت پوشش داده نمی‌شوند.
 *
 *   php examples/v3/send-multiple.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';

$config = pr_config();

$sender = (int) $config['sender'];

$response = pr_post('SendMultiple', [
    'ApiKey' => $config['api_key'],
    'Recipients' => [
        [
            'Sender'      => $sender,
            'Destination' => 9121112222,
            'Text'        => 'آقای محمدی، سفارش ۱۰۰۱ شما ارسال شد.',
            'UserTraceId' => 1001,
        ],
        [
            'Sender'      => $sender,
            'Destination' => 9121113333,
            'Text'        => 'خانم رضایی، سفارش ۱۰۰۲ شما آماده تحویل است.',
            'UserTraceId' => 1002,
        ],
    ],
]);

pr_show($response);
