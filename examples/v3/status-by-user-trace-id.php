<?php
/**
 * StatusByUserTraceId - وضعیت پیامک با شناسه پی‌گیری خودتان.
 *
 * همان StatusById، اما با شناسه‌هایی که خودتان هنگام ارسال تعیین کرده‌اید.
 * اگر UserTraceId را کلید رکورد پایگاه داده خودتان بگذارید، دیگر لازم
 * نیست Id سامانه را ذخیره کنید.
 *
 * این متد کاربرد دومی هم دارد که مهم‌تر است: اگر درخواست ارسال timeout
 * خورد یا خطای ۱۰۰ گرفت، نمی‌دانید پیامک ثبت شده یا نه. کورکورانه دوباره
 * نفرستید؛ با همان UserTraceId اینجا استعلام بگیرید. تنها راه امن همین
 * است، و دلیل اصلی اینکه چرا باید همیشه UserTraceId یکتا بفرستید.
 *
 *   php examples/v3/status-by-user-trace-id.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';
require_once __DIR__ . '/../../utils/codes.php';

$config = pr_config();

$response = pr_post('StatusByUserTraceId', [
    'ApiKey'       => $config['api_key'],
    'UserTraceIds' => [1001, 1002],
]);

if (empty($response['Success'])) {
    pr_show($response);
    exit(1);
}

foreach ($response['Result'] as $item) {
    $code = (int) $item['StatusCode'];

    echo "پی‌گیری {$item['UserTraceId']}: " . pr_status_label($code);

    // فقط وضعیت pending ارزش استعلام دوباره دارد.
    echo pr_status_is_pending($code) ? "  ← دوباره استعلام بگیرید\n" : "\n";
}
