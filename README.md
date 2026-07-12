# Police Crime Report Portal — Web Security Lab

A deliberately vulnerable PHP + MariaDB web app that demonstrates four web
vulnerabilities, each sitting inside a real feature of a crime-report portal.
Full working CRUD (create / read / update / delete) backed by the database.

| # | Vulnerability        | Where it lives                     |
|---|----------------------|------------------------------------|
| 1 | SQL Injection (POST) | `login.php` — auth bypass          |
| 2 | SQL Injection (GET)  | `track.php?code=` — sqlmap target  |
| 3 | Stored XSS           | report details → officer case view |
| 4 | Reflected XSS        | `search.php?q=`                    |
| 5 | CSRF                 | officer "update status" action     |

> Runs on localhost only. Passwords are stored in plaintext **on purpose** so
> the SQL-injection dump is meaningful — never do this in real software.

## Features (CRUD, all persisted to the DB)
- **Citizens** register, file reports, and view / **edit** / **delete** their own reports.
- **Officers** see every case with live status counts, open a case, **update status**,
  **edit** or **delete** any report, and **add / delete** case notes.
- **Public** case tracking by code (no sign-in).

## Setup on Kali Linux (Apache + MariaDB)

```bash
# 1. Install the stack (if needed)
sudo apt update
sudo apt install -y apache2 mariadb-server php libapache2-mod-php php-mysqli

# 2. Start services
sudo systemctl start apache2 mariadb

# 3. Deploy into Apache's webroot
sudo cp -r police-portal /var/www/html/
sudo chown -R www-data:www-data /var/www/html/police-portal

# 4. Create the database (tables + realistic seed data + app user)
sudo mariadb < /var/www/html/police-portal/sql/schema.sql
```

Open **http://localhost/police-portal/**

Tip for the VM: open claude.ai inside the Kali browser and download the zip
there, so it lands directly in the VM. If you rename the folder, update the one
`define('BASE', ...)` line in `includes/functions.php`.

## Seed logins
| Role    | Username  | Password    |
|---------|-----------|-------------|
| Officer | `officer` | `police123` |
| Officer | `admin`   | `admin@123` |
| Citizen | `rahim`   | `rahim123`  |
| Citizen | `fatema`  | `fatema123` |
| Citizen | `karim`   | `karim123`  |
| Citizen | `shila`   | `shila123`  |

## Demo order
1. **SQLi (POST)** — login as `officer' -- ` / anything.
2. **SQLi (GET) + sqlmap** — probe `track.php?code=CR-1001'`, then run the sqlmap commands.
3. **Stored XSS** — file a report as a citizen with a `<script>` payload; open it as an officer.
4. **Reflected XSS** — `search.php?q=<script>alert(document.cookie)</script>`.
5. **CSRF** — while signed in as officer, open `exploit/csrf_attack_get.html`.

All payloads and sqlmap commands: **`exploit/payloads.md`**.
