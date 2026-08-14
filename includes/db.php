<?php
// includes/db.php

$dbPath = dirname(__DIR__) . '/abjad.db';

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create calculations table if not exists
    $db->exec("
        CREATE TABLE IF NOT EXISTS calculations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            total INTEGER NOT NULL,
            single INTEGER NOT NULL,
            origin TEXT DEFAULT NULL,
            meanings TEXT DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Migration check for calculations table columns
    $calcCols = array_column($db->query("PRAGMA table_info(calculations)")->fetchAll(), 'name');
    if (!in_array('notes', $calcCols)) {
        $db->exec("ALTER TABLE calculations ADD COLUMN notes TEXT DEFAULT NULL");
    }
    if (!in_array('created_at', $calcCols)) {
        $db->exec("ALTER TABLE calculations ADD COLUMN created_at TEXT DEFAULT NULL");
    }

    // Create users table if not exists
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            full_name TEXT DEFAULT NULL,
            contact TEXT DEFAULT NULL,
            role TEXT CHECK(role IN ('public','staff','admin')) DEFAULT 'public',
            status TEXT CHECK(status IN ('pending','approved','rejected')) DEFAULT 'pending',
            circumstance TEXT DEFAULT NULL,
            req_name_lookup TEXT DEFAULT NULL,
            req_relationship TEXT DEFAULT NULL,
            req_name TEXT DEFAULT NULL,
            req_question TEXT DEFAULT NULL,
            req_submitted_at TIMESTAMP DEFAULT NULL,
            req_admin_reply TEXT DEFAULT NULL,
            req_status TEXT CHECK(req_status IN ('none','pending','replied')) DEFAULT 'none',
            req_replied_at TIMESTAMP DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Create user_chats table for single chat thread history
    $db->exec("
        CREATE TABLE IF NOT EXISTS user_chats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            sender TEXT CHECK(sender IN ('user','admin')) NOT NULL,
            name_lookup TEXT DEFAULT NULL,
            relationship TEXT DEFAULT NULL,
            name TEXT DEFAULT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ");

    // Create site_settings table for dynamic configuration (such as element colors)
    $db->exec("
        CREATE TABLE IF NOT EXISTS site_settings (
            key TEXT PRIMARY KEY,
            value TEXT NOT NULL
        );
    ");

    // Seed default element colors (Fire=yellow, Air=red, Water=blue, Earth=black)
    $defaultElemColors = [
        'elem_color_fire' => '#eab308', // Yellow
        'elem_color_air' => '#dc2626',  // Red
        'elem_color_water' => '#2563eb', // Blue
        'elem_color_earth' => '#0f172a'  // Black
    ];

    $checkSetting = $db->prepare("SELECT COUNT(*) FROM site_settings WHERE key = ?");
    $insertSetting = $db->prepare("INSERT INTO site_settings (key, value) VALUES (?, ?)");

    foreach ($defaultElemColors as $sKey => $sVal) {
        $checkSetting->execute([$sKey]);
        if ($checkSetting->fetchColumn() == 0) {
            $insertSetting->execute([$sKey, $sVal]);
        }
    }

    // Migration check for existing databases
    $columns = $db->query("PRAGMA table_info(users)")->fetchAll();
    $existingCols = array_column($columns, 'name');

    $colsToAdd = [
        'full_name' => 'TEXT DEFAULT NULL',
        'contact' => 'TEXT DEFAULT NULL',
        'circumstance' => 'TEXT DEFAULT NULL',
        'req_name_lookup' => 'TEXT DEFAULT NULL',
        'req_relationship' => 'TEXT DEFAULT NULL',
        'req_name' => 'TEXT DEFAULT NULL',
        'req_question' => 'TEXT DEFAULT NULL',
        'req_submitted_at' => 'TIMESTAMP DEFAULT NULL',
        'req_admin_reply' => 'TEXT DEFAULT NULL',
        'req_status' => "TEXT DEFAULT 'none'",
        'req_replied_at' => 'TIMESTAMP DEFAULT NULL'
    ];

    foreach ($colsToAdd as $colName => $colDef) {
        if (!in_array($colName, $existingCols)) {
            $db->exec("ALTER TABLE users ADD COLUMN {$colName} {$colDef}");
        }
    }

    // Seed default admin user if no admin exists
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'admin'");
    $row = $stmt->fetch();
    if ($row['cnt'] == 0) {
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $seed = $db->prepare("INSERT INTO users (username, email, password, full_name, contact, role, status) VALUES (?, ?, ?, 'Administrator', '+1 000-000-0000', 'admin', 'approved')");
        $seed->execute(['admin', 'admin@example.com', $adminPass]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
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
        $stmt = $db->query("SELECT key, value FROM site_settings WHERE key LIKE 'elem_color_%'");
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
