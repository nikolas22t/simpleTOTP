# Simple PHP TOTP Manager

A lightweight **PHP TOTP (Time-Based One-Time Password) manager** with **per-user session storage**. Each user only sees their own entries in the current session. Supports adding, deleting, moving, sorting, and importing/exporting TOTP entries in JSON format.

---

## Features

- Generate TOTP codes (6 digits) for any account using a Base32 secret.
- Per-user session storage (no database required).
- Add, delete, move up/down, and sort entries.
- Export and import entries as JSON files.
- Automatic code refresh every 30 seconds.
- Minimal, responsive HTML/CSS UI.

---

## Installation

1. Clone the repository:

```bash
git clone https://github.com/nikolas22t/simpleTOTP.git
cd simpleTOTP
```

2. Make sure you have **PHP 7.0+** installed.
3. Start a local PHP server:

```bash
php -S localhost:8000
```

4. Open your browser and visit `http://localhost:8000/2fa.php`.

---

## Usage

1. Add a new entry by entering the **Name** (e.g., GitHub) and **Secret (Base32)**.  
2. Your TOTP codes will refresh automatically every 30 seconds.  
3. Use **Export JSON** to save your entries, or **Import JSON** to load entries from another session.  
4. Sort entries alphabetically or move them up/down for organization.

---

## Notes

- All data is stored in PHP sessions; closing the browser may clear entries. Use JSON export to save your data.  
- The project does **not require a database**.  
- Minimal dependencies; pure PHP and standard HTML/CSS.

---

## License

This project is open source under the **MIT License**.
