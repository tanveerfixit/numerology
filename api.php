<?php
// abjad/api.php

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

startSession();

$action = $_GET['action'] ?? '';

// Logout action
if ($action === 'logout') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true) ?? [];

    // Signup action
    if ($action === 'signup') {
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $fullName = trim($data['full_name'] ?? '');
        $contact = trim($data['contact'] ?? '');

        if (empty($username) || empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields (username, email, password) are required']);
            exit;
        }

        // Check unique username or email
        $check = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Username or Email is already taken']);
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, contact, role, status) VALUES (?, ?, ?, ?, ?, 'public', 'pending')");
        try {
            $stmt->execute([$username, $email, $hash, $fullName, $contact]);
            echo json_encode(['success' => true, 'message' => 'Account created! Pending admin approval.']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Signup failed: ' . $e->getMessage()]);
        }
        exit;
    }

    // Login action
    if ($action === 'login') {
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            exit;
        }

        $_SESSION['user_id'] = $user['id'];
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'] ?? '',
                'contact' => $user['contact'] ?? '',
                'role' => $user['role'],
                'status' => $user['status'],
                'circumstance' => $user['circumstance']
            ]
        ]);
        exit;
    }

    // Require authentication for remaining POST actions
    $currentUser = getCurrentUser($db);
    if (!$currentUser || $currentUser['status'] !== 'approved') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized: Account must be logged in and approved by admin.']);
        exit;
    }

    // Update Profile (Name, Contact)
    if ($action === 'update_profile') {
        $fullName = trim($data['full_name'] ?? '');
        $contact = trim($data['contact'] ?? '');

        try {
            $stmt = $db->prepare("UPDATE users SET full_name = ?, contact = ? WHERE id = ?");
            $stmt->execute([$fullName, $contact, $currentUser['id']]);
            echo json_encode(['success' => true, 'message' => 'Profile details updated successfully!']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update profile: ' . $e->getMessage()]);
        }
        exit;
    }

    // Send User Chat Question / Circumstance Consultation Request
    if ($action === 'send_user_chat' || $action === 'circumstance_request') {
        $nameLookup = trim($data['name_lookup'] ?? $data['nameLookup'] ?? '');
        $relationship = trim($data['relationship'] ?? '');
        $name = trim($data['name'] ?? $data['fullName'] ?? '');
        $contact = trim($data['contact'] ?? $data['contactNumber'] ?? '');
        $question = trim($data['question'] ?? '');

        if (empty($question)) {
            http_response_code(400);
            echo json_encode(['error' => 'Question message cannot be empty.']);
            exit;
        }

        try {
            $stmt = $db->prepare("INSERT INTO user_chats (user_id, sender, name_lookup, relationship, name, message) VALUES (?, 'user', ?, ?, ?, ?)");
            $stmt->execute([$currentUser['id'], $nameLookup, $relationship, $name, $question]);

            // Update user status and full_name/contact if provided
            $updateSql = "UPDATE users SET req_status = 'pending', req_name_lookup = ?, req_relationship = ?, req_name = ?, req_question = ?, req_submitted_at = CURRENT_TIMESTAMP";
            $params = [$nameLookup, $relationship, $name, $question];
            if (!empty($name)) {
                $updateSql .= ", full_name = ?";
                $params[] = $name;
            }
            if (!empty($contact)) {
                $updateSql .= ", contact = ?";
                $params[] = $contact;
            }
            $updateSql .= " WHERE id = ?";
            $params[] = $currentUser['id'];

            $db->prepare($updateSql)->execute($params);

            echo json_encode(['success' => true, 'message' => 'Consultation inquiry submitted to admin successfully!']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send message: ' . $e->getMessage()]);
        }
        exit;
    }

    // Admin user management & chat endpoints
    if (in_array($action, ['approve_user', 'reject_user', 'update_role', 'update_circumstance', 'send_admin_reply', 'delete_chat_message', 'clear_user_chat_history', 'delete_user'])) {
        if ($currentUser['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Admin privileges required']);
            exit;
        }

        if ($action === 'delete_chat_message') {
            $chatId = $data['chat_id'] ?? null;
            if (!$chatId) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing chat_id']);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM user_chats WHERE id = ?");
            $stmt->execute([$chatId]);
            echo json_encode(['success' => true]);
            exit;
        }

        $targetId = $data['id'] ?? $data['user_id'] ?? null;
        if (!$targetId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing target user ID']);
            exit;
        }

        if ($action === 'send_admin_reply') {
            $reply = trim($data['reply'] ?? '');
            if (empty($reply)) {
                http_response_code(400);
                echo json_encode(['error' => 'Reply text cannot be empty']);
                exit;
            }

            try {
                $stmt = $db->prepare("INSERT INTO user_chats (user_id, sender, message) VALUES (?, 'admin', ?)");
                $stmt->execute([$targetId, $reply]);

                $db->prepare("UPDATE users SET req_admin_reply = ?, req_status = 'replied', req_replied_at = CURRENT_TIMESTAMP, circumstance = ? WHERE id = ?")
                   ->execute([$reply, $reply, $targetId]);

                echo json_encode(['success' => true, 'message' => 'Reply sent to user successfully!']);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to send reply: ' . $e->getMessage()]);
            }
            exit;
        }

        if ($action === 'clear_user_chat_history') {
            $stmt = $db->prepare("DELETE FROM user_chats WHERE user_id = ?");
            $stmt->execute([$targetId]);
            $db->prepare("UPDATE users SET req_status = 'none' WHERE id = ?")->execute([$targetId]);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action === 'approve_user') {
            $stmt = $db->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
            $stmt->execute([$targetId]);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action === 'reject_user') {
            $stmt = $db->prepare("UPDATE users SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$targetId]);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action === 'update_role') {
            $newRole = $data['role'] ?? 'public';
            if (!in_array($newRole, ['public', 'staff', 'admin'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid role']);
                exit;
            }
            $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$newRole, $targetId]);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action === 'update_circumstance') {
            $circumstance = $data['circumstance'] ?? '';
            $stmt = $db->prepare("UPDATE users SET circumstance = ? WHERE id = ?");
            $stmt->execute([$circumstance, $targetId]);
            echo json_encode(['success' => true]);
            exit;
        }

        if ($action === 'delete_user') {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$targetId]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    // Calculation endpoints - REQUIRE LOGGED IN AND APPROVED USER
    if (in_array($action, ['save', 'edit', 'delete', 'update_name_notes'])) {
        if (!$currentUser || $currentUser['status'] !== 'approved') {
            http_response_code(403);
            echo json_encode(['error' => 'Account must be logged in and approved to access saved names history.']);
            exit;
        }
    }

    if ($action === 'update_name_notes') {
        $id = $data['id'] ?? null;
        $notes = $data['notes'] ?? '';
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing record ID']);
            exit;
        }
        try {
            $stmt = $db->prepare("UPDATE calculations SET notes = ? WHERE id = ?");
            $stmt->execute([$notes, $id]);
            echo json_encode(['success' => true, 'message' => 'Specific notes saved successfully!']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'save') {
        $name = $data['name'] ?? null;
        $total = $data['total'] ?? null;
        $single = $data['single'] ?? null;
        $origin = $data['origin'] ?? '';
        $meanings = $data['meanings'] ?? '';
        $notes = $data['notes'] ?? '';

        if (empty($name) || $total === null || $single === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        try {
            $stmt = $db->prepare("INSERT INTO calculations (name, total, single, origin, meanings, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $total, $single, $origin, $meanings, $notes]);
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'edit') {
        $id = $data['id'] ?? null;
        $name = $data['name'] ?? null;
        $total = $data['total'] ?? null;
        $single = $data['single'] ?? null;
        $origin = $data['origin'] ?? '';
        $meanings = $data['meanings'] ?? '';
        $notes = $data['notes'] ?? null;

        if (!$id || empty($name) || $total === null || $single === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        try {
            if ($notes !== null) {
                $stmt = $db->prepare("UPDATE calculations SET name = ?, total = ?, single = ?, origin = ?, meanings = ?, notes = ? WHERE id = ?");
                $stmt->execute([$name, $total, $single, $origin, $meanings, $notes, $id]);
            } else {
                $stmt = $db->prepare("UPDATE calculations SET name = ?, total = ?, single = ?, origin = ?, meanings = ? WHERE id = ?");
                $stmt->execute([$name, $total, $single, $origin, $meanings, $id]);
            }
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'delete') {
        $id = $data['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required field id']);
            exit;
        }

        try {
            $stmt = $db->prepare("DELETE FROM calculations WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $currentUser = getCurrentUser($db);

    if ($action === 'get_name_detail') {
        if (!$currentUser || $currentUser['status'] !== 'approved') {
            http_response_code(403);
            echo json_encode(['error' => 'Account must be logged in and approved to view record details.']);
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing record id']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM calculations WHERE id = ?");
        $stmt->execute([$id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$record) {
            http_response_code(404);
            echo json_encode(['error' => 'Record not found']);
            exit;
        }

        echo json_encode($record, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'get_user_chats') {
        if (!$currentUser) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $targetUserId = $currentUser['id'];
        if ($currentUser['role'] === 'admin' && isset($_GET['user_id'])) {
            $targetUserId = (int)$_GET['user_id'];
        }

        $stmt = $db->prepare("SELECT id, user_id, sender, name_lookup, relationship, name, message, created_at FROM user_chats WHERE user_id = ? ORDER BY id ASC");
        $stmt->execute([$targetUserId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($action === 'list_users') {
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Admin privileges required']);
            exit;
        }
        $stmt = $db->query("SELECT id, username, email, full_name, contact, role, status, circumstance, req_name_lookup, req_relationship, req_name, req_question, req_submitted_at, req_admin_reply, req_status, req_replied_at, created_at FROM users ORDER BY id DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    if ($action === 'get_pending_requests_count') {
        if (!$currentUser || $currentUser['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Admin privileges required']);
            exit;
        }
        $stmt = $db->query("SELECT COUNT(*) as cnt FROM users WHERE req_status = 'pending'");
        $row = $stmt->fetch();
        echo json_encode(['pending_count' => (int)$row['cnt']]);
        exit;
    }

    if ($action === 'history') {
        if (!$currentUser || $currentUser['status'] !== 'approved') {
            http_response_code(403);
            echo json_encode(['error' => 'Account must be logged in and approved to view saved names history.']);
            exit;
        }

        try {
            $stmt = $db->query("SELECT id, name, total, single, origin, meanings, notes, created_at FROM calculations ORDER BY id DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($rows, JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}

// 404 fallback
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
