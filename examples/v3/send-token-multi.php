<?php
/**
 * SendTokenMulti - یک قالب، چند گیرنده، مقادیر متفاوت.
 *
 * پارامترها اینجا آرایه‌اند، نه p1 تا p10، و ترتیب آرایه همان ترتیب
 * جای‌گاه‌های {1}، {2}، … است.
 *
 *   php examples/v3/send-token-multi.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../../utils/client.php';

$config = pr_config();

$response = pr_post('SendTokenMulti', [
    'ApiKey'      => $config['api_key'],
    'TemplateKey' => 'YOUR-TEMPLATE-KEY',
    'Recipients'  => [
        [
            'Destination' => 9121112222,
            'UserTraceId' => 1001,
            'Parameters'  => ['محمدی', '123456'],
        ],
        [
            'Destination' => 9121113333,
            'UserTraceId' => 1002,
            'Parameters'  => ['رضایی', '654321'],
        ],
    ],
]);

pr_show($response);
