<?php
/**
 * SendBulk - یک متن به چند گیرنده، هر کدام با شناسه پی‌گیری خودتان.
 *
 * روش پیشنهادی برای ارسال عملیاتی: کلید را از نشانی بیرون می‌برد و
 * UserTraceId می‌پذیرد، پس بعداً با status-by-user-trace-id.php می‌توانید
 * وضعیت را بگیرید بدون اینکه Id سامانه را ذخیره کرده باشید.
 *
 *   php examples/v3/send-bulk.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';

$config = pr_config();

// docs:start
$response = pr_post('SendBulk', [
    'ApiKey' => $config['api_key'],
    'Sender' => (int) $config['sender'],
    'Text'   => 'سفارش شما ثبت شد.',

    // حداکثر ۹۹ گیرنده. UserTraceId را معمولاً کلید رکورد پایگاه داده
    // خودتان می‌گذارند تا گزارش بعدی به همان رکورد وصل شود.
    'Recipients' => [
        ['Destination' => 9121112222, 'UserTraceId' => 1001],
        ['Destination' => 9121113333, 'UserTraceId' => 1002],
    ],
]);

pr_show($response);
// docs:end
