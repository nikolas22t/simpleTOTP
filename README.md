# Simple TOTP Manager

A lightweight, self-hosted **TOTP (time-based one-time password) manager** that runs entirely in your browser. Secrets are kept in the browser's `localStorage` and never sent to the server.

## Features

- 6-digit TOTP codes (SHA-1, 30 s), with the current **and next** code shown for every account
- Full-width grid of compact account cards (7 per row on wide screens, responsive down to mobile)
- One global countdown ring plus a per-card progress bar (green → amber → red)
- Double-click a code to copy it (copied without spaces)
- Search, with **saved search tabs** (e.g. a permanent "work" or "gmail" tab), stored in a cookie
- **Service logos**: names like `Binance`, `Gmail` or `GitHub` automatically show the service logo, looked up from a server-side logo list
- Add, **edit** (✎ on hover), delete, sort A→Z, clear all
- **Drag and drop** cards to reorder them
- Export / import all accounts as JSON

## Files

| File | Purpose |
|------|---------|
| `2fa.php` | The app (HTML/CSS/JS; TOTP via Web Crypto) |
| `logo.php` | Logo endpoint: `logo.php?n=<service>` → PNG, or 404 if unknown |
| `logos.json` | Logo list: `"keyword": "domain"` (e.g. `"binance": "binance.com"`) |
| `logos/` | Cached logo images (must be writable by PHP) |

## Installation

```bash
git clone https://github.com/nikolas22t/simpleTOTP.git
cd simpleTOTP
php -S localhost:8000
```

Then open `http://localhost:8000/2fa.php` (or rename it to `index.php`). PHP 7.4+ is only needed for the logo endpoint; the TOTP part is pure client-side JavaScript. Serve it over HTTPS in production (Web Crypto requires a secure context).

## Logos

The first word of an account name is the lookup key (case-insensitive; `Kraken-Master` → `kraken`, `myheritage.com/` → `myheritage`). If the key is in `logos.json`, `logo.php` fetches the domain's icon once from Google's favicon service, caches it in `logos/`, and serves it from then on. Unknown names keep a coloured letter avatar.

- Add a service: add `"name": "domain.com"` to `logos.json`
- Use your own image: drop a PNG named after the domain into `logos/` (e.g. `logos/example.com.png`)

Only the domain name is sent to Google — never account names or secrets.

## Usage

1. **＋ Add** → enter a name (e.g. `GitHub user@example.com` — the first word is the service, the rest is shown as the account) and the Base32 secret.
2. Type in the search box to filter; click **+ Tab** to save that search as a permanent tab.
3. **Import / Export** to back up or move your accounts between browsers.

## Security notes

- Secrets live in this browser's `localStorage` in plain text. Anyone with access to the browser profile can read them — use it on trusted devices only and keep an exported backup somewhere safe.
- Clearing site data deletes your accounts; export first.

## License

MIT
