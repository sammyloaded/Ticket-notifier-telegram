# Telegram Ticket Notification Bot

A lightweight PHP script that watches your support tickets database and sends “new ticket” alerts into a Telegram group mainly for Smartpanel SMM Script Users who wish to get notified of new support ticket via telegram.

---

## Features

* Detects **new** tickets by tracking the highest ticket ID in a simple pointer file.
* Sends ticket details (ID, Subject, Status) with an inline “View Ticket” button.
* Stores your last-processed ticket ID in `last_ticket_id.txt`—no database writes required.
* Comes with built-in browser debug output; can be run via cron for fully automated alerts.

---

## Repository Contents

```
.
├── ticketbot.php         # Main notification script
└── last_ticket_id.txt    # Pointer file (auto-created on first run)
```

---

## ⚙️ Configuration

1. **Copy** or **rename** `ticketbot.php` into your project directory.

2. **Edit** the top section of `ticketbot.php` and replace the placeholders:

   ```php
   // ── CONFIG ─────────────────────────────────────────────────────────────────
   $dbHost     = 'YOUR_DB_HOST';
   $dbName     = 'YOUR_DB_NAME';
   $dbUser     = 'YOUR_DB_USER';
   $dbPass     = 'YOUR_DB_PASSWORD';

   $botToken   = 'YOUR_TELEGRAM_BOT_TOKEN';
   $chatId     = 'YOUR_TELEGRAM_CHAT_ID';

   // Optionally adjust the path to your last-ID pointer:
   $lastIdFile = __DIR__ . '/last_ticket_id.txt';
   ```

3. **Ensure** the web-server (or PHP-CLI user) has read/write permissions on `last_ticket_id.txt`.
   On Unix/Linux:

   ```bash
   touch last_ticket_id.txt
   chmod 0666 last_ticket_id.txt
   ```

---

## 🚀 Usage

### 1) Manual Run

From the command line:

```bash
php /path/to/ticketbot.php
```

Or point your browser to:

```
https://your-domain.example.com/ticketbot.php
```

You’ll see a debug log of what the script found, what it sent to Telegram, and where it updated the pointer.

### 2) Automate with Cron

To check for new tickets every minute, add to your cron (edit with `crontab -e`):

```cron
* * * * * curl -s https://yourdomainname/ticketbot.php >/dev/null 2>&1
```

---

## 🐞 Debugging

* The script outputs errors and Telegram responses directly when run in browser or CLI.
* If you see `cURL error` or `{"ok":false,...}`, double-check:

  * **Bot Token** is exactly as given by BotFather (no extra spaces or BOM).
  * **Chat ID** is correct (for groups it starts with `-100…`).
  * **Bot is added** into your Telegram group with “Send Messages” permission.

---

## 🙋‍♂️ Customization

* **Change the SQL** in `ticketbot.php` if your tickets table or column names differ.
* **Modify the message template** (Markdown) to include additional fields (e.g. `created_at`, `priority`, etc.).
* **Swap file-based tracking** for a dedicated “notified” flag in your DB—just add an `UPDATE` inside the loop.

---

## 📜 License

This project is provided “as-is” under the MIT License. Feel free to adapt, extend, and redistribute.

```text
MIT License

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Smartpanel Ticket Telegram Notifier"), to deal in the Software without restriction...
```

---

Happy automating! 🚀
