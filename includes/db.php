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
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Create users table if not exists
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
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

    // Migration check for existing databases
    $columns = $db->query("PRAGMA table_info(users)")->fetchAll();
    $existingCols = array_column($columns, 'name');

    $colsToAdd = [
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
        $seed = $db->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, 'admin', 'approved')");
        $seed->execute(['admin', 'admin@example.com', $adminPass]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}
