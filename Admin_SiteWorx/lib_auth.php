<?php
// Simple auth helpers for Admin_SiteWorx (uses $pdo from connection)
function find_user_by_username($pdo, $username) {
    $stmt = $pdo->prepare('SELECT * FROM login WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => $username]);
    return $stmt->fetch();
}

function login_user($pdo, $username, $password) {
    $user = find_user_by_username($pdo, $username);
    if (!$user) return false;
    if (!empty($user['status']) && $user['status'] !== 'active') return false;

    // If password stored with PHP password_hash
    if (isset($user['password']) && strlen($user['password']) >= 60) {
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }

    // Fallback: support older md5 or sha1/sha256 seeds
    if (isset($user['password'])) {
        // md5
        if (strlen($user['password']) == 32 && md5($password) === $user['password']) {
            // upgrade hash
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE login SET password = :p WHERE id = :id');
            $stmt->execute([':p' => $newHash, ':id' => $user['id']]);
            $user['password'] = $newHash;
            return $user;
        }
        // sha256 (seeded by migration example)
        if (strlen($user['password']) == 64 && hash('sha256', $password) === $user['password']) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE login SET password = :p WHERE id = :id');
            $stmt->execute([':p' => $newHash, ':id' => $user['id']]);
            $user['password'] = $newHash;
            return $user;
        }
    }

    return false;
}

function register_user($pdo, $name, $email, $username, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO login (name, email, username, password) VALUES (:name, :email, :username, :password)');
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':username' => $username,
        ':password' => $hash
    ]);
    return $pdo->lastInsertId();
}

function update_user_password($pdo, $userId, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('UPDATE login SET password = :password WHERE id = :id');
    $stmt->execute([':password' => $hash, ':id' => $userId]);
}

function require_login() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['valid'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user($pdo) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['id'])) return null;
    $stmt = $pdo->prepare('SELECT * FROM login WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $_SESSION['id']]);
    return $stmt->fetch();
}

function get_user_role($pdo, $user) {
    // Accept $user as id or array row
    if (is_array($user)) {
        $row = $user;
    } else {
        $stmt = $pdo->prepare('SELECT * FROM login WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $user]);
        $row = $stmt->fetch();
    }
    if (!$row) return null;

    // If login table has 'role_id' and roles table exists, return role name
    try {
        $col = $pdo->query("SHOW COLUMNS FROM login LIKE 'role_id'")->fetch();
        if ($col) {
            if (!empty($row['role_id'])) {
                $stmt = $pdo->prepare('SELECT name FROM roles WHERE id = :id LIMIT 1');
                $stmt->execute([':id' => $row['role_id']]);
                $r = $stmt->fetch();
                if ($r) return $r['name'];
            }
        }
    } catch (Exception $e) {
        // ignore
    }

    // If login table has 'role' column, use it
    try {
        $col2 = $pdo->query("SHOW COLUMNS FROM login LIKE 'role'")->fetch();
        if ($col2) {
            return $row['role'] ?? null;
        }
    } catch (Exception $e) {
        // ignore
    }

    // Fallback: treat username 'admin' as admin
    if (!empty($row['username']) && strtolower($row['username']) === 'admin') return 'admin';
    return 'client';
}

function require_role($pdo, $allowed = []) {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['id'])) {
        header('Location: login.php'); exit;
    }
    $user = current_user($pdo);
    $role = get_user_role($pdo, $user);
    if (!in_array($role, (array)$allowed)) {
        http_response_code(403);
        echo 'Forbidden: insufficient privileges.';
        exit;
    }
}
