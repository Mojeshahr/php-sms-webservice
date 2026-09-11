<div align="center">

<a href="https://payam-resan.com">
  <img src=".github/assets/logo.svg" width="64" height="64" alt="Payam Resan">
</a>

<h1>PHP examples for the Payam Resan SMS web service</h1>

Connect to the <a href="https://payam-resan.com"><b>Payam Resan SMS panel</b></a> from PHP<br>
one runnable file per API method, no dependencies

[![API](https://img.shields.io/badge/API-V3-0a7cbd)](https://payam-resan.com)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)](https://php.net)
[![Dependencies](https://img.shields.io/badge/dependencies-none-2ea44f)](#quick-start)
[![License](https://img.shields.io/badge/license-MIT-6e7781)](LICENSE)

<a href="README.md">فارسی</a> · <b>English</b>

</div>

<sub>Looking for another language? The same examples exist for the other
languages at [github.com/Mojeshahr](https://github.com/Mojeshahr).</sub>

---

## Quick start

```bash
git clone https://github.com/Mojeshahr/php-sms-webservice.git
cd php-sms-webservice
cp .env.example .env
```

Open `.env` and put in the API key and sender line from your account panel:

```ini
PAYAM_RESAN_API_KEY=123456-XXXXXXXXXXXXXXX
PAYAM_RESAN_SENDER=30004040
```

Test the connection first. This method sends nothing and costs no credit:

```bash
php examples/v3/account-info.php
```

If it prints your balance and active sender lines, everything is wired up:

```bash
php examples/v3/send.php
```

**Requires** PHP 7.4 or later with the cURL extension. Nothing else.

---

## Which method do I want

| If you want to… | Use |
|---|---|
| just send one message, quickly | `Send` |
| send the same text to several people and track each one | `SendBulk` |
| send a different text to each recipient | `SendMultiple` |
| send a one-time password or any approved template | `SendTokenSingle` |
| send one template to several people with different values | `SendTokenMulti` |

For production sending, `SendBulk` is the best choice: it keeps the API key out
of the URL and accepts your own tracking id for every recipient.

---

## The methods

| Example | Service method | What it does |
|---|---|---|
| [account-info.php](examples/v3/account-info.php) | `AccountInfo` | Balance and active sender lines |
| [send.php](examples/v3/send.php) | `Send` | One text to one or more numbers, over GET |
| [send-bulk.php](examples/v3/send-bulk.php) | `SendBulk` | One text to many recipients, with tracking ids |
| [send-multiple.php](examples/v3/send-multiple.php) | `SendMultiple` | A different text per recipient |
| [send-token-single.php](examples/v3/send-token-single.php) | `SendTokenSingle` | Send a template to one number |
| [send-token-single-get.php](examples/v3/send-token-single-get.php) | `SendTokenSingle` | The same, over GET |
| [send-token-multi.php](examples/v3/send-token-multi.php) | `SendTokenMulti` | One template, many recipients |
| [token-list.php](examples/v3/token-list.php) | `TokenList` | Your templates and their approval status |
| [status-by-id.php](examples/v3/status-by-id.php) | `StatusById` | Delivery status by the service's id |
| [status-by-user-trace-id.php](examples/v3/status-by-user-trace-id.php) | `StatusByUserTraceId` | Delivery status by your own id |
| [get-inbox.php](examples/v3/get-inbox.php) | `GetInbox` | Messages people sent to your lines |

---

## Using this in your own project

This is not a Composer package. You have three options.

### 1. Copy the example

Every example is deliberately free of any dependency on this repository, so
copying the file into your project is enough. Define two environment variables
and it runs:

```bash
export PAYAM_RESAN_API_KEY='123456-XXXXXXXXXXXXXXX'
export PAYAM_RESAN_SENDER='30004040'
```

### 2. Take only the request shape

If your project already has an HTTP client, leave the example's cURL layer
behind and copy just the request body and the response check:

```php
use GuzzleHttp\Client;

$http = new Client(['base_uri' => 'https://api.sms-webservice.com/api/V3/']);

$response = $http->post('SendBulk', [
    'json' => [
        'ApiKey' => getenv('PAYAM_RESAN_API_KEY'),
        'Sender' => 30004040,
        'Text'   => 'Your verification code is 123456',
        'Recipients' => [
            ['Destination' => 9121112222, 'UserTraceId' => 1001],
        ],
    ],
]);

$body = json_decode((string) $response->getBody(), true);

if (! $body['Success']) {
    throw new RuntimeException("SMS not sent: {$body['Error']} (code {$body['ErrorCode']})");
}
```

### 3. Wait

An installable Composer package is planned and will be published in its own
repository. Until then, the two routes above will get you running.

---

## Four things that will save you time

> [!IMPORTANT]
> Read success from the `Success` field, not from the HTTP status. The service
> always answers `200`, even when the key is invalid. The reason for a failure
> arrives in `ErrorCode` and `Error`.

> [!WARNING]
> Do not url-encode the message text yourself. `http_build_query` already does
> it once; encode it beforehand as well and the recipient sees
> `%D8%A7%D8%B3%D8%AA` instead of readable text.

**Recipient numbers carry no leading zero.** Use `9121112222`, or
`989121112222` with the country code. Up to 99 recipients per request.

**Delivery status is not final immediately.** It starts at `0` (queued in the
system) and moves through several steps to `4` (delivered to the handset).
Leave a few minutes between sending and asking.

---

## Keeping the key safe

> [!CAUTION]
> The API key is a secret, like a password. Anyone who has it can spend your
> credit.

- Never write it in code. Put it in `.env`, which is listed in `.gitignore`.
- Never ship it in browser JavaScript or in a compiled mobile app.
- Prefer the `POST` methods for sending. With `GET`, the key travels inside the
  URL and is recorded in web server logs, browser history and the `Referer`
  header.
- If it leaks, revoke it in the panel and issue a new one.

---

## Layout

| Path | What it is |
|---|---|
| `examples/v3/` | One self-contained example per service operation |
| `.env.example` | A template for the environment variables |

The `v3` in the path is deliberate, so later versions of the service can sit
beside this one.

---

## Documentation and support

The full guide to the web service is at
[docs.payam-resan.com](https://docs.payam-resan.com), and the machine-readable
OpenAPI description is in
[sms-webservice-spec](https://github.com/Mojeshahr/sms-webservice-spec).

Question or bug? [Open an issue](https://github.com/Mojeshahr/php-sms-webservice/issues)
or contact [support](https://payam-resan.com).

## License

Released under the MIT license. Full text in [`LICENSE`](LICENSE).

<br>
<div align="center">
  <sub>
    <img src=".github/assets/logo.svg" width="16" height="16" alt="" align="top">
    &nbsp;<b>Payam Resan SMS Panel - Moje Shahr</b>&nbsp;
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
