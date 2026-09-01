# Agent guide

This repository is **example code**, not a library. Its job is to get a PHP
developer talking to the Payam Resan SMS web service within a few minutes. Any
decision that makes that slower is the wrong decision, however good the
engineering behind it.

## Two needs this repository must meet

Both of them, not one. A repository that covers only one is unfinished.

1. **A guide to the web service and its methods, in this language.** The reader
   has to learn when each method is useful, what it takes, what it returns and
   where it goes wrong. That is the job of the README and of the doc block at
   the top of every example.

2. **Example code that is ready for real use.** Something the reader can drop
   into their own program, not a snippet that only works on a slide. That means
   error handling, timeouts, reading the key from the environment, and a
   section in the README about using this in your own project.

## Persian text

Follow the wording, spelling and punctuation already in the repository. When
you add a passage or rewrite one, match the surrounding text instead of
bringing your own conventions to it. This covers the README, the comments
inside the code, YAML values, and any string shown to a user.

**The brand name takes a real space, not a ZWNJ:** `پیام رسان`. The joined
form is the common noun for social messaging apps and collides with the brand.

Where the brand is first introduced, use the full phrase
**پنل پیامکی پیام رسان**. That removes the ambiguity.

Every README carries `payam-resan.com` in the footer, written out and linked,
not merely a link sitting on the brand name.

## Two languages, two files

Every repository carries two READMEs and both stay in step:

| File | Language | Direction |
|---|---|---|
| `README.md` | Persian | right to left |
| `README.en.md` | English | left to right |

The English file is named exactly `README.en.md`, in capitals. GitHub renders
only `README.md` on its own, so both files open with a language switch line so
that either one leads to the other:

```
<b>فارسی</b> · <a href="README.en.md">English</a>
```

**The English text is not a literal translation of the Persian.** Same
material, written for a reader who does not know Persian and most likely
arrived from outside Iran. The structure and the section order stay identical,
which is what makes keeping them in step cheap.

The English version needs no `<div dir="rtl">` wrapper.

## Text direction in the Persian README

GitHub applies `dir="auto"` to headings, paragraphs and lists, but **not to
tables**, and its `<article>` wrapper carries no direction. Three rules follow.

**1. Wrap every table in a right-to-left container,** otherwise the columns
stay left to right and the first column lands on the left:

```markdown
<div dir="rtl">

| ستون | ستون |
|---|---|

</div>
```

**2. A code block never goes inside a right-to-left container.** The code ends
up right aligned. Wrap the table only, never a whole section.

**3. A Persian paragraph must not open with a left-to-right character.**
`dir="auto"` takes its direction from the first strong character, so a sentence
that starts with `` `code` `` or an English link renders fully left aligned.
The fix is to rewrite the sentence, not to add markup:

```
✗ `v3` در مسیر عمدی است تا …
✓ بخش `v3` در مسیر عمدی است تا …
```

## Source of truth

Method signatures, field names and code values come from the
`sms-webservice-spec` repository. Never write them from memory or from older
documentation. Where the spec and this repository disagree, the spec is right.

## Rules that do not bend

**No API key is written into any file.** Not in an example, not in a test, not
masked. It belongs in `.env`, and `.env` is in `.gitignore`. The only place a
sample value appears is `.env.example`.

**`https` only.** In the GET methods the key travels inside the URL, so plain
`http` puts it on the wire in the clear.

**The message text is url-encoded once, not twice.** `http_build_query` already
does it. Calling `urlencode()` beforehand makes the recipient see `%D8%A7`.
That bug was in the previous version of this repository and had been copied
into six languages.

**Success is read from the `Success` field, never from the HTTP status.** The
service answers `200` even when the key is invalid.

**The service's own mistakes are not corrected.** The sender field in
`GetInbox` is spelled `Form`. Fix it and the code stops matching the real
response.

## Layout

| Path | What it holds |
|---|---|
| `examples/v3/` | one file per service operation |
| `utils/` | the cURL layer and the code tables, so no example repeats them |
| `config/` | reading configuration from the environment |

The `v3` in the path is deliberate. A new version means a new
`examples/v<n>/`, with the existing folder left alone.

## Style of the examples

- Every file opens with a doc block saying **when** the method is useful and
  what the alternative is, rather than listing parameters.
- Every file runs on its own: `php examples/v3/send.php`.
- Errors are checked. An example that ignores the response teaches the wrong
  thing.
- Comments in Persian, field names in English and spelled exactly as the
  service spells them.

## Before every commit

```bash
for f in utils/*.php examples/v3/*.php; do php -l "$f"; done
```

## Git

Semantic messages, `type(scope): subject`, with no explanatory body and no
mention of any tool or assistant. Do not commit without explicit approval.
