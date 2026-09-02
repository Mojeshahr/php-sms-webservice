<?php
/**
 * TokenList - فهرست قالب‌های تعریف‌شده.
 *
 * برای پیدا کردن TemplateKey که متدهای ارسال قالب لازم دارند.
 *
 *   php examples/v3/token-list.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';
require_once __DIR__ . '/../../utils/codes.php';

$config = pr_config();

// docs:start
$response = pr_post('TokenList', [
    'ApiKey' => $config['api_key'],
]);

if (empty($response['Success'])) {
    pr_show($response);
    exit(1);
}

foreach ($response['Result'] as $template) {
    // فقط وضعیت ۱ قابل ارسال است.
    $status = pr_template_status_label((int) $template['Status']);
    echo "کلید: {$template['Key']}\n";
    echo "وضعیت: $status\n";
    echo "متن: {$template['TextTemplate']}\n";
    echo str_repeat('-', 40) . "\n";
}
// docs:end
