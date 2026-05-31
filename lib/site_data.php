<?php
// Helpers to fetch site data (plans, services) using $pdo from Admin_SiteWorx/connection.php
if (!function_exists('sw_get_plans')) {
    function sw_get_plans($pdo, $category = null, $limit = 100) {
        $sql = 'SELECT id,name,slug,category,description, specs, price_monthly, price_yearly FROM hosting_plans WHERE status = "active"';
        $params = [];
        if ($category) {
            $sql .= ' AND category = :cat';
            $params[':cat'] = $category;
        }
        $sql .= ' ORDER BY category, price_monthly LIMIT ' . (int)$limit;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        // decode specs JSON
        foreach ($rows as &$r) {
            if (!empty($r['specs'])) {
                $r['specs'] = json_decode($r['specs'], true);
            } else {
                $r['specs'] = [];
            }
        }
        return $rows;
    }

    function sw_get_plan_by_slug($pdo, $slug) {
        $stmt = $pdo->prepare('SELECT * FROM hosting_plans WHERE slug = :s LIMIT 1');
        $stmt->execute([':s' => $slug]);
        $r = $stmt->fetch();
        if ($r && !empty($r['specs'])) $r['specs'] = json_decode($r['specs'], true);
        return $r;
    }

    function sw_get_services($pdo, $type = null, $limit = 100) {
        $sql = 'SELECT * FROM service_catalog WHERE status = "active"';
        $params = [];
        if ($type) { $sql .= ' AND type = :t'; $params[':t'] = $type; }
        $sql .= ' ORDER BY name LIMIT ' . (int)$limit;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
