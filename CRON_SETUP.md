# CRON Setup — Boleskine Fixture Fetcher

This document describes the Linux crontab configuration for the
standalone `fetch-shinty-fixtures.php` script.

---

## Schedule

| Day(s) | Time Window | Frequency | Cron Expression |
|--------|-------------|-----------|-----------------|
| Saturday | 15:00 – 23:59 BST | Every 20 minutes | `*/20 15-23 * * 6` |
| Sunday | 00:00 – 11:59 BST | Every 20 minutes | `*/20 0-11 * * 0` |
| Monday – Friday | 08:00 BST | Once daily | `0 8 * * 1-5` |

---

## Crontab Entry

Add the following lines to the crontab of the web server user
(edit with `crontab -e`):

```
# Boleskine Fixture Fetcher — Saturdays (15:00–23:59 every 20min)
*/20 15-23 * * 6 /usr/bin/php /path/to/fetch-shinty-fixtures.php

# Boleskine Fixture Fetcher — Sundays (00:00–11:59 every 20min)
*/20 0-11 * * 0 /usr/bin/php /path/to/fetch-shinty-fixtures.php

# Boleskine Fixture Fetcher — Weekdays (once at 08:00)
0 8 * * 1-5 /usr/bin/php /path/to/fetch-shinty-fixtures.php
```

> **Note:** Replace `/path/to/` with the absolute path to the directory
> containing `fetch-shinty-fixtures.php` on your server.

---

## Verification

1. Run the script manually to confirm it works:
   ```bash
   php /path/to/fetch-shinty-fixtures.php
   ```

2. Check the generated file:
   ```bash
   cat /path/to/fixtures.json
   ```

3. Once the crontab is installed, verify it is active:
   ```bash
   crontab -l
   ```

---

## Requirements

- PHP CLI (`/usr/bin/php`)
- PHP cURL extension (`php-curl`)
- Outbound HTTPS access to `matches.shinty.com`

---

## Notes

- The script is fully independent of WordPress — no WP bootstrap required.
- It writes `fixtures.js` to the same directory as the script as a JavaScript
  variable assignment (`var fixtureData = { ... };`), which the browser loads
  via a `<script>` tag — compatible with both `file://` and `http://` protocols.
- Error output is sent to stderr; cron will typically email it to the
  user if `MAILTO` is configured in crontab.
