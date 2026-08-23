<?php
/**
 * SendTokenSingle — همان متد با GET.
 *
 * وقتی به‌درد می‌خورد که فقط می‌توانید یک نشانی صدا بزنید: یک وب‌هوک،
 * یک ابزار قدیمی، یا تست سریع با مرورگر.
 *
 * برای محیط عملیاتی send-token-single.php را به کار ببرید. اینجا هم کلید
 * حساب و هم رمز یک‌بارمصرف داخل نشانی می‌روند و در لاگ وب‌سرور می‌مانند.
 *
 *   php examples/v3/send-token-single-get.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';

$config = pr_config();

$response = pr_get('SendTokenSingle', [
    'ApiKey'      => $config['api_key'],
    'TemplateKey' => 'YOUR-TEMPLATE-KEY',
    'Destination' => 9121112222,
    'p1'          => '123456',
]);

pr_show($response);
