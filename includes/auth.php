<?php
// includes/auth.php

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return !empty($_SESSION['user_id']);
}

function getCurrentUser($db) {
    if (!isLoggedIn()) return null;
    $stmt = $db->prepare("SELECT id, username, email, role, status, circumstance, req_name_lookup, req_relationship, req_name, req_question, req_submitted_at, req_admin_reply, req_status, req_replied_at, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?auth=login');
        exit;
    }
}

function requireApproved($user) {
    if (!$user || $user['status'] !== 'approved') {
        header('Location: index.php?error=pending');
        exit;
    }
}

function isStaffOrAdmin($user) {
    return $user && in_array($user['role'], ['staff', 'admin']) && $user['status'] === 'approved';
}

function requireAdmin($user) {
    if (!$user || $user['role'] !== 'admin' || $user['status'] !== 'approved') {
        header('Location: index.php?error=unauthorized');
        exit;
    }
}
