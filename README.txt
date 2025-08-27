Library Management System (XAMPP-ready)
--------------------------------------
How to run:
1. Copy the extracted folder into your XAMPP htdocs directory (e.g., c:\xampp\htdocs\library or /opt/lampp/htdocs/library).
2. Import the SQL file `library.sql` into phpMyAdmin (create a database named `library` first or edit config.php to match your DB name).
3. Open in browser: http://localhost/library_system/

Defaults:
- DB host: localhost
- DB name: library
- DB user: root
- DB pass: (empty)

Admin login (hardcoded in config.php):
  username: admin
  password: admin123

VIP activation code (hardcoded):
  VIP2025

Notes:
- This is a demo/simple starter project. Do NOT use as-is in production.
- If you change DB credentials, update config.php accordingly.