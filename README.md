<div align="center">

<a href="https://payam-resan.com">
  <img src=".github/assets/logo.svg" width="64" height="64" alt="پیام رسان">
</a>

<h1>نمونه‌کدهای PHP وب‌سرویس پیام رسان</h1>

اتصال به وب‌سرویس <a href="https://payam-resan.com"><b>پنل پیامکی پیام رسان</b></a> با PHP<br>
یک فایل قابل اجرا به‌ازای هر متد سرویس، بدون هیچ وابستگی

[![API](https://img.shields.io/badge/API-V3-0a7cbd)](https://payam-resan.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)](https://php.net)
[![Dependencies](https://img.shields.io/badge/dependencies-none-2ea44f)](#شروع-سریع)
[![License](https://img.shields.io/badge/license-MIT-6e7781)](LICENSE)

<b>فارسی</b> · <a href="README.en.md">English</a>

</div>

<sub>دنبال زبان دیگری هستید؟ همین نمونه‌ها برای زبان‌های دیگر هم در
[github.com/Mojeshahr](https://github.com/Mojeshahr) هست.</sub>

---

## شروع سریع

```bash
git clone https://github.com/Mojeshahr/php-sms-webservice.git
cd php-sms-webservice
cp .env.example .env
```

فایل `.env` را باز کنید و کلید و خط فرستنده‌تان را از پنل کاربری بگذارید:

```ini
PAYAM_RESAN_API_KEY=123456-XXXXXXXXXXXXXXX
PAYAM_RESAN_SENDER=30004040
```

اول اتصال را بیازمایید. این متد چیزی ارسال نمی‌کند و اعتباری مصرف نمی‌کند:

```bash
php examples/v3/account-info.php
```

اگر اعتبار و خطوط فعالتان را دید، همه چیز درست است:

```bash
php examples/v3/send.php
```

**نیازمندی:** PHP نسخه ۷.۴ یا بالاتر با افزونه cURL. هیچ وابستگی دیگری ندارد.

---

## کدام متد را انتخاب کنم

<div dir="rtl">

| اگر… | این متد |
|---|---|
| فقط می‌خواهید سریع یک پیامک بفرستید | `Send` |
| متن یکسان به چند نفر، با پی‌گیری وضعیت | `SendBulk` |
| متن متفاوت برای هر گیرنده | `SendMultiple` |
| رمز یک‌بارمصرف یا هر قالب تأییدشده | `SendTokenSingle` |
| یک قالب برای چند نفر با مقادیر متفاوت | `SendTokenMulti` |

</div>

برای ارسال عملیاتی `SendBulk` بهترین انتخاب است: کلید را از نشانی بیرون
می‌برد و برای هر گیرنده شناسه پی‌گیری اختصاصی می‌پذیرد.

---

## متدها

<div dir="rtl">

| نمونه | متد سرویس | کار |
|---|---|---|
| [account-info.php](examples/v3/account-info.php) | `AccountInfo` | اعتبار و خطوط فعال |
| [send.php](examples/v3/send.php) | `Send` | یک متن به یک یا چند شماره، با GET |
| [send-bulk.php](examples/v3/send-bulk.php) | `SendBulk` | یک متن به چند گیرنده، با شناسه پی‌گیری |
| [send-multiple.php](examples/v3/send-multiple.php) | `SendMultiple` | متن متفاوت برای هر گیرنده |
| [send-token-single.php](examples/v3/send-token-single.php) | `SendTokenSingle` | ارسال قالب به یک شماره |
| [send-token-single-get.php](examples/v3/send-token-single-get.php) | `SendTokenSingle` | همان، با GET |
| [send-token-multi.php](examples/v3/send-token-multi.php) | `SendTokenMulti` | یک قالب، چند گیرنده |
| [token-list.php](examples/v3/token-list.php) | `TokenList` | فهرست قالب‌ها و وضعیت تأییدشان |
| [status-by-id.php](examples/v3/status-by-id.php) | `StatusById` | وضعیت با شناسه سامانه |
| [status-by-user-trace-id.php](examples/v3/status-by-user-trace-id.php) | `StatusByUserTraceId` | وضعیت با شناسه خودتان |
| [get-inbox.php](examples/v3/get-inbox.php) | `GetInbox` | پیامک‌های رسیده به خطوط شما |

</div>

### دو نسخه از هر نمونه

فایل‌های بالا کوتاه‌اند چون لایه cURL مشترک را از `utils/` صدا می‌زنند. برای
خواندن و برای کلون‌کردن این مخزن خوب‌اند، ولی بیرون از اینجا کپی‌کردنی نیستند:
`pr_post` در پروژه شما وجود ندارد.

برای همین کنارشان [`examples/v3/standalone/`](examples/v3/standalone/) هست. همان
یازده نمونه، ولی هر کدام کامل و مستقل، فقط با افزونه cURL خود PHP. اینها همان
چیزی هستند که در [مستندات](https://docs.payam-resan.com) دیده می‌شوند: کپی کنید
و در پروژه خودتان اجرا کنید.

---

## استفاده در پروژه خودتان

این مخزن یک بسته composer نیست و قرار نیست باشد. سه راه دارید.

### ۱. دو پوشه را بردارید

ساده‌ترین راه برای پروژه‌های PHP خالص:

```bash
cp -r config utils /path/to/your-project/lib/payam-resan/
```

بعد هر جا لازم شد:

```php
require __DIR__ . '/lib/payam-resan/utils/client.php';

$config = pr_config();

$response = pr_post('SendBulk', [
    'ApiKey' => $config['api_key'],
    'Sender' => (int) $config['sender'],
    'Text'   => 'کد تأیید شما ۱۲۳۴۵۶ است',
    'Recipients' => [
        ['Destination' => 9121112222, 'UserTraceId' => $order->id],
    ],
]);

if (! $response['Success']) {
    // کد خطا و راهکارش را در مستندات ببینید
    throw new RuntimeException($response['Error']);
}
```

تابع `pr_config()` خودش `.env` را می‌خواند، پس فقط باید `.env` کنار پوشه کپی‌شده
باشد یا متغیرها را در محیط سرور تعریف کنید.

### ۲. فقط شکل درخواست را بردارید

اگر در پروژه‌تان کلاینت HTTP دارید، `utils/` را نبرید. از هر نمونه فقط بدنه
درخواست و بررسی پاسخ را بردارید:

```php
use GuzzleHttp\Client;

$http = new Client(['base_uri' => 'https://api.sms-webservice.com/api/V3/']);

$response = $http->post('SendBulk', [
    'json' => [
        'ApiKey' => getenv('PAYAM_RESAN_API_KEY'),
        'Sender' => 30004040,
        'Text'   => 'کد تأیید شما ۱۲۳۴۵۶ است',
        'Recipients' => [
            ['Destination' => 9121112222, 'UserTraceId' => 1001],
        ],
    ],
]);

$body = json_decode((string) $response->getBody(), true);

if (! $body['Success']) {
    throw new RuntimeException("پیامک ارسال نشد: {$body['Error']} (کد {$body['ErrorCode']})");
}
```

### ۳. صبر کنید

یک بسته نصب‌شدنی با composer در برنامه هست و در مخزن جداگانه‌ای منتشر
می‌شود. تا آن موقع، دو راه بالا کار را راه می‌اندازند.

---

## چهار نکته که وقت‌تان را می‌خرد

> [!IMPORTANT]
> موفقیت را از فیلد `Success` بخوانید، نه از کد HTTP. سرویس همیشه `200`
> برمی‌گرداند، حتی وقتی کلید نامعتبر است. علت شکست در `ErrorCode` و `Error`
> می‌آید.

> [!WARNING]
> متن پیامک را خودتان url-encode نکنید. `http_build_query` این کار را یک بار
> انجام می‌دهد؛ اگر پیش از آن هم `urlencode()` بزنید، گیرنده به‌جای متن فارسی
> `%D8%A7%D8%B3%D8%AA` می‌بیند.

**شماره گیرنده بدون صفر ابتدایی.** `9121112222` یا با کد کشور
`989121112222`. حداکثر ۹۹ گیرنده در هر درخواست.

**وضعیت پیامک بلافاصله نهایی نیست.** از `0` (در صف سامانه) شروع می‌شود و تا
`4` (تحویل به گوشی) چند مرحله جلو می‌رود. بین ارسال و استعلام چند دقیقه فاصله
بدهید.

---

## امنیت کلید

> [!CAUTION]
> کلید API یک راز است، مثل رمز عبور. هر کسی که داشته باشد می‌تواند از اعتبار
> شما پیامک بفرستد.

- در کد ننویسیدش. در `.env` بگذارید که در `.gitignore` هست.
- در جاوااسکریپت سمت مرورگر یا در اپلیکیشن موبایل کامپایل‌شده نگذاریدش.
- برای ارسال، متدهای `POST` را ترجیح بدهید. در `GET` کلید داخل نشانی می‌رود و
  در لاگ وب‌سرور، تاریخچه مرورگر و هدر `Referer` ثبت می‌شود.
- اگر جایی لو رفت، از پنل باطلش کنید و کلید تازه بگیرید.

---

## ساختار

<div dir="rtl">

| مسیر | چیست |
|---|---|
| `examples/v3/` | یک نمونه به‌ازای هر عملیات سرویس |
| `examples/v3/standalone/` | همان نمونه‌ها، بدون هیچ وابستگی به این مخزن |
| `utils/` | لایه cURL و جدول کدها، تا در هر نمونه تکرار نشوند |
| `config/` | خواندن پیکربندی از محیط |
| `.env.example` | نمونه متغیرهای محیطی |

</div>

بخش `v3` در مسیر عمدی است تا نسخه‌های بعدی سرویس کنار همین بنشینند.

---

## مستندات و پشتیبانی

- [مستندات پیام رسان](https://docs.payam-resan.com)
- [مرجع Swagger وب‌سرویس](https://api.sms-webservice.com/swagger/ui/index)
- سؤال یا خطا؟ [issue باز کنید](https://github.com/Mojeshahr/php-sms-webservice/issues)
  یا با [پشتیبانی](https://payam-resan.com) تماس بگیرید.

## مجوز

[MIT](LICENSE)

<br>
<div align="center">
  <sub>
    <img src=".github/assets/logo.svg" width="16" height="16" alt="" align="top">
    &nbsp;<b>پنل پیامکی پیام رسان - موج شهر</b>&nbsp;
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset=".github/assets/mojeshahr-dark.svg">
      <img src=".github/assets/mojeshahr-light.svg" width="16" height="16" alt="" align="top">
    </picture>
  </sub>
  <br>
  <sub>
    <a href="https://payam-resan.com">payam-resan.com</a>
    &nbsp;·&nbsp;
    <a href="https://mojeshahr.ir">mojeshahr.ir</a>
  </sub>
</div>
