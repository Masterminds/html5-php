# Security Policy

## Supported Versions

Security fixes are provided for the latest release of the 2.x series.

| Version | Supported |
| ------- | --------- |
| 2.x     | Yes       |
| < 2.0   | No        |

## Scope

This library parses and serializes HTML5, often from untrusted input.

In scope, for example:

- crashes, hangs or excessive CPU/memory use caused by crafted input
  (e.g. very deeply nested or very large documents),
- serializer output that can introduce script execution (XSS) or otherwise
  does not faithfully represent the parsed document,
- parsing behaviour that deviates from the HTML5 specification in a way that
  can be exploited (e.g. mutation-based bypasses of a downstream sanitizer).

Out of scope:

- the library is not an HTML sanitizer: unsanitized input producing
  unsanitized output is expected, and callers must sanitize untrusted content
  themselves,
- issues that require the caller to misuse the API or to already control the
  PHP environment.

If you are unsure whether something qualifies, report it privately anyway.

## Reporting a Vulnerability

Please do **not** report security vulnerabilities through public GitHub issues,
discussions or pull requests.

Report them privately using GitHub's
[private vulnerability reporting](https://github.com/Masterminds/html5-php/security/advisories/new)
("Security" tab → "Report a vulnerability").

Please include as much of the following as you can:

- the affected version(s) and PHP version,
- a description of the issue and its impact,
- a minimal HTML input or code sample that reproduces it.

We will acknowledge your report, keep you informed while we investigate, and
coordinate a fix and disclosure with you. Once a fix is released, the
vulnerability will be published as a GitHub security advisory.
