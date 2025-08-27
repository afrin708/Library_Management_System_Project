<?php
    // config.php - database and app config
    session_start();
    date_default_timezone_set('Asia/Dhaka');

    // Database
    define('DB_HOST','localhost');
    define('DB_NAME','library');
    define('DB_USER','root');
    define('DB_PASS','');

    try {
      $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
      die('DB Connection failed: '.$e->getMessage());
    }

    // Hardcoded admin credentials
    define('ADMIN_USER','admin');
    define('ADMIN_PASS','admin123'); // change if you want

    // VIP code
    define('VIP_CODE','VIP2025');
    ?>