<?php
/**
 * AccountInfo - اعتبار و خطوط فعال حساب.
 *
 * سبک‌ترین متد سرویس و بهترین جا برای شروع: چیزی ارسال نمی‌کند و اعتباری
 * مصرف نمی‌کند، پس برای آزمودن درستی کلید و اتصال ایده‌آل است.
 *
 *   php examples/v3/account-info.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';

$config = pr_config();

// docs:start
$response = pr_post('AccountInfo', [
    'ApiKey' => $config['api_key'],
]);

if (empty($response['Success'])) {
    pr_show($response);
    exit(1);
}

$account = $response['Result'];

echo "اعتبار: {$account['Credit']}\n";
echo "خطوط فعال:\n";
foreach ($account['AvailableSenders'] as $sender) {
    echo "  - $sender\n";
}
// docs:end
