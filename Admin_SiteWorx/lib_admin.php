<?php

function sw_current_role($pdo) {
    $user = current_user($pdo);
    return [$user, get_user_role($pdo, $user)];
}

function sw_can_manage_user($pdo, $targetUserId) {
    [$current, $role] = sw_current_role($pdo);
    if (!$current) return false;
    if ($role === 'admin') return true;
    if ((int)$current['id'] === (int)$targetUserId) return true;
    if ($role !== 'manager') return false;

    $stmt = $pdo->prepare('SELECT manager_id FROM login WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $targetUserId]);
    $row = $stmt->fetch();
    return $row && (int)$row['manager_id'] === (int)$current['id'];
}

function sw_manager_user_filter_sql($role, $currentUserId, $alias = 'u') {
    if ($role === 'admin') return ['1=1', []];
    if ($role === 'manager') return ["({$alias}.manager_id = :current_manager OR {$alias}.id = :current_manager)", [':current_manager' => $currentUserId]];
    return ["{$alias}.id = :current_user", [':current_user' => $currentUserId]];
}

function sw_json_or_error($value, &$error) {
    $value = trim((string)$value);
    if ($value === '') return null;
    json_decode($value, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $error = 'Specs/meta must be valid JSON.';
        return false;
    }
    return $value;
}

function sw_format_money($amount, $currency = 'INR') {
    return htmlspecialchars($currency) . ' ' . number_format((float)$amount, 2);
}

