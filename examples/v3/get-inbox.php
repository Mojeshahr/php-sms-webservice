<?php
/**
 * GetInbox — پیامک‌های رسیده به خطوط حساب.
 *
 * این یک استعلام است، نه webhook: سامانه چیزی به سرور شما نمی‌فرستد و
 * باید خودتان دوره‌ای فراخوانی کنید.
 *
 * ⚠ نام فیلد فرستنده در پاسخ Form است، نه From. این غلط املایی در خود
 * سرویس است؛ دنبال From نگردید.
 *
 *   php examples/v3/get-inbox.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';

$config = pr_config();

$response = pr_post('GetInbox', [
    'ApiKey' => $config['api_key'],
]);

if (empty($response['Success'])) {
    pr_show($response);
    exit(1);
}

if ($response['Result'] === []) {
    echo "پیامک تازه‌ای نرسیده است.\n";
    exit(0);
}

foreach ($response['Result'] as $message) {
    echo "از {$message['Form']} به {$message['To']} در {$message['Time']}\n";
    echo "  {$message['Text']}\n";
}
