# Working with this repository

You are looking at runnable PHP examples for the **Payam Resan** SMS web service
(`api.sms-webservice.com`, API V3), an Iranian SMS provider. Someone is probably
asking you to add SMS to their project.

Copy the example that matches the method, adapt it, and keep the rules below.
They are not style preferences — each one is a bug that this service produces if
you ignore it.

## Start here

```bash
export PAYAM_RESAN_API_KEY='123456-XXXXXXXXXXXXXXX'
export PAYAM_RESAN_SENDER='30004040'

php examples/v3/account-info.php
```

`account-info.php` sends nothing, spends no credit, and answers even on a zero
balance, so run it first to prove the key and the connection work.

PHP 7.4 or later with the cURL extension. Nothing else — there is no
`composer.json` and no shared client class; each file is written out in full so
it can be copied straight into a project.

**The examples do not read `.env`.** They call `getenv()` only. Copying
`.env.example` to `.env` is not enough; the values have to be in the
environment:

```bash
set -a; . ./.env; set +a
```

If the user follows the README quick start literally and gets a blank key, this
is why.

## Rule 1: `Success`, never the HTTP status

The service answers `200` to everything, including a wrong key, an empty account
and a malformed body. The examples never read `CURLINFO_RESPONSE_CODE`, on
purpose.

```php
$response = json_decode($raw, true);

if (empty($response['Success'])) {
    exit("ناموفق. کد {$response['ErrorCode']}: {$response['Error']}\n");
}

foreach ($response['Result'] as $message) {
    echo "{$message['UserTraceId']} => شناسه {$message['Id']}\n";
}
```

`ErrorCode` is only meaningful when `Success` is false.

In production code, do better than `empty()` on its own: it also swallows the
case where `json_decode` returned `null` because the body was not JSON at all.
Check `json_last_error() === JSON_ERROR_NONE` first, then the flag, so a proxy
error page does not get reported as an SMS failure with an empty code.

## Rule 2: decode to arrays, with `true`

Every example uses `json_decode($raw, true)` and reads `$response['Result']`,
never `->Result`. Drop the `true` and every line below it breaks.

`Result` is a list of rows for the send, status, list and inbox methods, but an
object for `AccountInfo`:

```php
echo "اعتبار: {$response['Result']['Credit']}\n";
```

## Rule 3: watch the width of message ids

Message ids are large integers. On 64-bit PHP `json_decode` handles them
losslessly and the examples are fine as written. On a 32-bit build they overflow
to float and interpolate as `9.9032E+15`, which then matches nothing when you
query the delivery report.

If the user's platform is uncertain, decode with `JSON_BIGINT_AS_STRING` and
keep ids as strings end to end. Do not cast an id to `int` anywhere.

## Rule 4: `JSON_UNESCAPED_UNICODE` and the charset header

Persian text needs both, and the examples set both:

```php
CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'],
```

## Rule 5: encode the query exactly once

`http_build_query` url-encodes exactly once. Encoding the text yourself
beforehand makes the SMS arrive full of `%D8` sequences. That bug was real in an
earlier version of this repository and had been copied into six languages before
it was caught.

## Rule 6: the key never leaves the server

`getenv('PAYAM_RESAN_API_KEY')`. Never write it into a file, and never let it
reach the browser. If the user's code is a JavaScript front end, this repository
is the right half of the design and the front end must call **their** PHP
endpoint, which calls this.

Use `https` always. In the `GET` methods the key travels inside the URL, so
plain `http` puts it on the wire in the clear — and even over TLS it lands in
the web server access log and the `Referer` header. Prefer `send-bulk.php` over
`send.php`, and the POST token variant over `send-token-single-get.php`.

## Rule 7: pick the right method

| The user wants | Use | File |
|---|---|---|
| one text to many people | `SendBulk` | `send-bulk.php` |
| a different text per person | `SendMultiple` | `send-multiple.php` |
| a one-time password or code | `SendTokenSingle` | `send-token-single.php` |
| a template to many people | `SendTokenMulti` | `send-token-multi.php` |
| delivery status | `StatusByUserTraceId` | `status-by-user-trace-id.php` |
| balance and sender lines | `AccountInfo` | `account-info.php` |

**A one-time password goes through a template**, not free text — that is the
usual route for OTP, and the template fixes the sender line, which is why
`SendTokenSingle` takes no `Sender`. `token-list.php` lists the account's
templates; `Status === 2` means approved and sendable, `1` awaiting review, `3`
rejected.

If the user is on Laravel, send them to
[laravel-sms-webservice](https://github.com/Mojeshahr/laravel-sms-webservice)
instead — same service, examples written for that framework.

## Rule 8: phone numbers have no leading zero

The service wants `9121112222` or `989121112222`. Users type `09121112222` or
`+989121112222`. Normalise before sending, or you get error `13`.

Ninety-nine recipients per request is the ceiling for `SendBulk`,
`SendMultiple` and `SendTokenMulti`.

## Rule 9: always send a `UserTraceId`

Use the user's own database id. After a timeout or error `100`, resending blind
may send twice — `StatusByUserTraceId` is the only safe way to learn whether the
message was registered. `$message['StatusCode'] === 8` there means the id is not
in the account, so it is safe to send again.

`SendTokenSingle` is the exception: it has no such input, so its `UserTraceId`
comes back null. If a trace id is needed for an OTP, use `SendTokenMulti` with a
single recipient.

## Rule 10: know which errors are worth retrying

These never succeed on retry — fix the cause; retrying only burns the rate limit
until the account hits error `20`:

`1`, `2`, `3`, `6`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `19`

`19` is an empty balance; `10` means the caller's IP is not on the account's
allowlist — a common one right after a server move. Treat any unknown code the
way you treat `100`: unclear outcome, check with `StatusByUserTraceId` before
resending.

## Rule 11: delivery status is a poll, not a callback

Status codes `0`, `1`, `2`, `3` and `10` mean still in flight — query again
later, and not more often than every few minutes or you will hit error `20`.
Everything else is final. Branch on `StatusCode`, never on the `Status` text,
which is Persian prose meant for humans and can change.

The examples compare with `===` against integers. That is right for this
service today, but if you write defensive code, compare loosely or cast, so a
field arriving as a JSON string does not silently fall through.

## Rule 12: `GetInbox` consumes what it returns

The service hands over each incoming message **once**. Never call it from a web
request: every page load consumes unread messages permanently. It belongs in a
cron job that writes straight to the database.

The sender field is called `Form`, not `From`. That is the service's spelling,
not a typo in the example.

## Using an HTTP client the project already has

The cURL block is written out in each file so the file stands alone. If the
project already has Guzzle or similar, keep the payload and the response check
and replace only the transport:

```php
$http = new GuzzleHttp\Client(['base_uri' => 'https://api.sms-webservice.com/api/V3/']);
$body = json_decode((string) $http->post('SendBulk', ['json' => $payload])->getBody(), true);
```

Leave TLS verification alone. The examples set no `CURLOPT_SSL_VERIFYPEER`
because the default is correct; turning it off to "fix" a certificate error
hands the key to anyone on the path.

## Testing without spending credit

Replace `V3` with `V3SandBox` in the URL. No message is sent and no credit is
spent. `TokenList` is not implemented there.

The sandbox is a simulator, not a mirror of the account: credit is always
`1234567`, sender lines are invented, and **it accepts any key**. Success there
proves nothing about the user's real key.

## Where the authoritative answers are

- Method reference and error tables: <https://docs.payam-resan.com>
- Machine-readable OpenAPI: <https://github.com/Mojeshahr/sms-webservice-spec>

If the spec and these examples ever disagree, the spec wins — report it as a bug
rather than guessing.
