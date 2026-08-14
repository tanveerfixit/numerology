<?php
// includes/db.php

/**
 * Load environment variables from .env file if available
 */
function loadEnv($filePath) {
    if (!file_exists($filePath)) return [];
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $env[trim($key)] = trim($val, " \t\n\r\0\x0B\"'");
        }
    }
    return $env;
}

$env = loadEnv(dirname(__DIR__) . '/.env');

$dbHost = $env['DB_HOST'] ?? 'srv2113.hstgr.io';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_NAME'] ?? 'u583652021_numerology';
$dbUser = $env['DB_USER'] ?? 'u583652021_number';
$dbPass = $env['DB_PASS'] ?? 'Tani@8877';

$db = null;
$hostsToTry = array_unique([$dbHost, '77.37.35.183', '127.0.0.1', 'localhost']);
$lastException = null;

foreach ($hostsToTry as $h) {
    try {
        $dsn = "mysql:host={$h};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $db = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            PDO::ATTR_TIMEOUT => 5
        ]);
        break; // Successfully connected!
    } catch (Exception $ex) {
        $lastException = $ex;
    }
}

if (!$db) {
    http_response_code(500);
    die(json_encode(['error' => 'MySQL Database connection failed: ' . ($lastException ? $lastException->getMessage() : 'Unable to connect to MySQL server.')]));
}

// Initialize MySQL Tables and Schemas
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS calculations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            total INT NOT NULL,
            single INT NOT NULL,
            origin VARCHAR(255) DEFAULT NULL,
            meanings TEXT DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(191) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255) DEFAULT NULL,
            contact VARCHAR(100) DEFAULT NULL,
            role VARCHAR(20) DEFAULT 'public',
            status VARCHAR(20) DEFAULT 'pending',
            circumstance TEXT DEFAULT NULL,
            req_name_lookup VARCHAR(255) DEFAULT NULL,
            req_relationship VARCHAR(255) DEFAULT NULL,
            req_name VARCHAR(255) DEFAULT NULL,
            req_question TEXT DEFAULT NULL,
            req_submitted_at TIMESTAMP NULL DEFAULT NULL,
            req_admin_reply TEXT DEFAULT NULL,
            req_status VARCHAR(20) DEFAULT 'none',
            req_replied_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $db->exec("
        CREATE TABLE IF NOT EXISTS user_chats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            sender VARCHAR(20) NOT NULL,
            name_lookup VARCHAR(255) DEFAULT NULL,
            relationship VARCHAR(255) DEFAULT NULL,
            name VARCHAR(255) DEFAULT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $db->exec("
        CREATE TABLE IF NOT EXISTS site_settings (
            setting_key VARCHAR(100) PRIMARY KEY,
            setting_value TEXT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Seed default elemental theme colors if not exists
    $defaultElemColors = [
        'elem_color_fire' => '#eab308',  // Yellow
        'elem_color_air' => '#dc2626',   // Red
        'elem_color_water' => '#2563eb', // Blue
        'elem_color_earth' => '#0f172a'  // Black
    ];

    $checkSetting = $db->prepare("SELECT COUNT(*) FROM site_settings WHERE setting_key = ?");
    $insertSetting = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

    foreach ($defaultElemColors as $sKey => $sVal) {
        $checkSetting->execute([$sKey]);
        if ($checkSetting->fetchColumn() == 0) {
            $insertSetting->execute([$sKey, $sVal]);
        }
    }

    // Seed default accounts if users table is empty
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM users");
    $row = $stmt->fetch();
    if ($row['cnt'] == 0) {
        $defaultPass = password_hash('Admin123', PASSWORD_DEFAULT);
        $seed = $db->prepare("INSERT INTO users (username, email, password, full_name, contact, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $seed->execute(['admin', 'admin@numerology.pk', $defaultPass, 'Administrator', '+92 300 0000001', 'admin', 'approved']);
        $seed->execute(['staff', 'staff@numerology.pk', $defaultPass, 'Staff Member', '+92 300 0000002', 'staff', 'approved']);
        $seed->execute(['user', 'user@numerology.pk', $defaultPass, 'Standard User', '+92 300 0000003', 'public', 'approved']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database initialization error: ' . $e->getMessage()]));
}

/**
 * Retrieve current elemental colors (Fire, Air, Water, Earth)
 */
function getElementColors($db) {
    $defaults = [
        'fire' => '#eab308', // Yellow
        'air' => '#dc2626',  // Red
        'water' => '#2563eb', // Blue
        'earth' => '#0f172a'  // Black
    ];

    try {
        $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key LIKE 'elem_color_%'");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return [
            'fire' => $rows['elem_color_fire'] ?? $defaults['fire'],
            'air' => $rows['elem_color_air'] ?? $defaults['air'],
            'water' => $rows['elem_color_water'] ?? $defaults['water'],
            'earth' => $rows['elem_color_earth'] ?? $defaults['earth']
        ];
    } catch (Exception $e) {
        return $defaults;
    }
}

/**
 * Upsert site setting value in MySQL
 */
function setSiteSetting($db, $key, $value) {
    $stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    return $stmt->execute([$key, $value]);
}
