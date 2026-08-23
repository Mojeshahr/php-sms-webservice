<?php
/**
 * لایهٔ ارتباطی مشترک همهٔ نمونه‌ها.
 *
 * این یک کتابخانه نیست و قرار نیست باشد؛ فقط همان چند خط cURL است که
 * وگرنه باید در هر نمونه تکرار می‌شد. اگر در پروژهٔ خودتان کلاینت HTTP
 * دارید (Guzzle، Symfony HttpClient، …) همان را به کار ببرید و از اینجا
 * فقط شکل درخواست و پاسخ را بردارید.
 *
 * برای استفاده، سه فایل لازم است: این، config/config.php و .env
 */

declare(strict_types=1);

require_once __DIR__ . '/codes.php';

/**
 * .env را می‌خواند و در محیط می‌گذارد، سپس پیکربندی را برمی‌گرداند.
 */
function pr_config(): array
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $envFile = __DIR__ . '/../.env';
    if (is_readable($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (getenv($key) === false) {
                putenv("$key=$value");
            }
        }
    }

    $config = require __DIR__ . '/../config/config.php';

    if ($config['api_key'] === '') {
        fwrite(STDERR, "PAYAM_RESAN_API_KEY تنظیم نشده است. .env.example را به .env کپی کنید.\n");
        exit(1);
    }

    return $config;
}

/**
 * فراخوانی یک متد GET.
 *
 * پارامترها را خام بدهید. http_build_query دقیقاً یک بار url-encode
 * می‌کند؛ اگر خودتان هم پیش از این urlencode کنید، متن پیامک با
 * نویسه‌های %D8 روی گوشی گیرنده ظاهر می‌شود.
 */
function pr_get(string $method, array $query): array
{
    $config = pr_config();
    $url = $config['base_url'] . '/' . $method . '?' . http_build_query($query);

    return pr_request($method, $url, null);
}

/**
 * فراخوانی یک متد POST با بدنهٔ JSON.
 */
function pr_post(string $method, array $body): array
{
    $config = pr_config();
    $url = $config['base_url'] . '/' . $method;
    $json = json_encode($body, JSON_UNESCAPED_UNICODE);

    if ($json === false) {
        throw new RuntimeException('ساخت JSON درخواست شکست خورد: ' . json_last_error_msg());
    }

    return pr_request($method, $url, $json);
}

/**
 * درخواست را می‌فرستد و پاکت پاسخ را برمی‌گرداند.
 *
 * فقط وقتی استثنا می‌دهد که پاسخ اصلاً قابل خواندن نباشد: خطای شبکه،
 * کد HTTP غیر ۲۰۰، یا JSON خراب. شکست منطقی سرویس استثنا نیست و در
 * فیلد Success پاسخ می‌آید، چون همان چیزی است که فراخواننده باید
 * بررسی‌اش کند.
 */
function pr_request(string $method, string $url, ?string $jsonBody): array
{
    $curl = curl_init();

    $options = [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ];

    if ($jsonBody !== null) {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $jsonBody;
        $options[CURLOPT_HTTPHEADER] = [
            'Content-Type: application/json; charset=utf-8',
            'Accept: application/json',
        ];
    }

    curl_setopt_array($curl, $options);

    $body = curl_exec($curl);
    $errno = curl_errno($curl);
    $error = curl_error($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($errno !== 0) {
        throw new RuntimeException("خطای شبکه در فراخوانی $method: $error");
    }

    if ($status !== 200) {
        throw new RuntimeException("پاسخ غیرمنتظره در فراخوانی $method: HTTP $status");
    }

    $decoded = json_decode((string) $body, true);
    if (!is_array($decoded)) {
        throw new RuntimeException("پاسخ $method یک JSON معتبر نبود: " . substr((string) $body, 0, 200));
    }

    return $decoded;
}

/**
 * پاسخ را خوانا چاپ می‌کند. فقط برای همین نمونه‌ها.
 *
 * نکتهٔ مهمی که نشان می‌دهد: موفقیت را باید از فیلد Success خواند. سرویس
 * حتی وقتی درخواست ناموفق است هم کد HTTP 200 برمی‌گرداند.
 */
function pr_show(array $response): void
{
    if (empty($response['Success'])) {
        $code = (int) ($response['ErrorCode'] ?? 0);
        $message = $response['Error'] ?? 'بدون شرح';

        echo "ناموفق. کد خطا $code — $message\n";

        // چیزی که سرویس نمی‌گوید و برنامه لازم دارد: تکرار همین درخواست
        // می‌تواند جواب بدهد یا نه.
        $retry = pr_error_retry($code);
        if ($retry === 'backoff') {
            echo "  موقتی است. با تأخیر فزاینده دوباره تلاش کنید.\n";
        } elseif ($retry === 'limit-reset') {
            echo "  سهمیه پر است. تا آزاد شدنش صبر کنید.\n";
        } else {
            echo "  تا درخواست را اصلاح نکنید، تکرارش بی‌فایده است.\n";
        }

        return;
    }

    echo "موفق.\n";
    echo json_encode($response['Result'] ?? null, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
}
