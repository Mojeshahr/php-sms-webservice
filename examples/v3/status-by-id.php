<?php
/**
 * StatusById — وضعیت پیامک با شناسهٔ سامانه.
 *
 * Idها را متدهای ارسال برمی‌گردانند.
 *
 * وضعیت بلافاصله نهایی نیست: پیامک از ۰ (در صف سامانه) شروع می‌شود و تا
 * ۴ (تحویل به گوشی) چند مرحله جلو می‌رود. بلافاصله بعد از ارسال استعلام
 * نگیرید؛ چند دقیقه فاصله بدهید، وگرنه به خطای ۲۰ می‌خورید.
 *
 * هر دو متد گزارش آرایه می‌گیرند، پس استعلام را دسته‌ای بفرستید نه تک‌تک.
 *
 *   php examples/v3/status-by-id.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';
require_once __DIR__ . '/../../utils/codes.php';

// تکلیف هر وضعیت با حلقهٔ استعلام.
const MEANING = [
    'pending'   => 'هنوز در راه است، بعداً دوباره استعلام کنید',
    'final'     => 'نهایی است، استعلام را متوقف کنید',
    'not-sent'  => 'ارسال نشد، مانع را رفع و دوباره بفرستید',
    'not-found' => 'این شناسه در حساب شما نیست',
];

$config = pr_config();

$response = pr_post('StatusById', [
    'ApiKey' => $config['api_key'],
    'Ids'    => [123456789, 123456790],
]);

if (empty($response['Success'])) {
    pr_show($response);
    exit(1);
}

foreach ($response['Result'] as $item) {
    // منطق برنامه را روی StatusCode بنویسید. فیلد Status فقط برای نمایش
    // است و متنش ممکن است عوض شود.
    $code = (int) $item['StatusCode'];

    echo "شناسه {$item['Id']}: " . pr_status_label($code) . "\n";
    echo '  ' . MEANING[pr_status_disposition($code)] . "\n";
}
