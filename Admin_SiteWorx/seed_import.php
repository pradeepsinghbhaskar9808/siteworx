<?php
// CLI-safe seeder: import JSON plan seeds into hosting_plans table
// Usage: php seed_import.php

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from CLI.\n"; exit(1);
}

require_once __DIR__ . '/connection.php';

$seedDir = __DIR__ . '/../database/seeds';
if (!is_dir($seedDir)) { echo "Seed dir missing: $seedDir\n"; exit(1); }

$files = glob($seedDir . '/*.json');
if (empty($files)) { echo "No seed files found in $seedDir\n"; exit(0); }

$totalInserted = 0;
foreach ($files as $jsonFile) {
    $base = basename($jsonFile);
    echo "Importing: $base\n";
    $data = json_decode(file_get_contents($jsonFile), true);
    if (!is_array($data)) { echo "  Invalid JSON in $base, skipping\n"; continue; }

    if (stripos($base, 'plans') !== false) {
        $insert = $pdo->prepare('INSERT INTO hosting_plans (name,slug,category,description,specs,price_monthly,price_yearly,setup_fee,status,created_at) VALUES (:name,:slug,:category,:description,:specs,:price_monthly,:price_yearly,:setup_fee,:status,NOW())');
        $check = $pdo->prepare('SELECT id FROM hosting_plans WHERE slug = :slug LIMIT 1');
        foreach ($data as $item) {
            $slug = $item['slug'] ?? null; if (!$slug) continue;
            $check->execute([':slug'=>$slug]); if ($check->fetch()) { echo "  Skipping existing: $slug\n"; continue; }
            $specs = json_encode($item['specs'] ?? []);
            $insert->execute([
                ':name'=>$item['name'] ?? $slug, ':slug'=>$slug, ':category'=>$item['category'] ?? 'misc', ':description'=>$item['description'] ?? null,
                ':specs'=>$specs, ':price_monthly'=>($item['price_monthly'] ?? 0), ':price_yearly'=>($item['price_yearly'] ?? null), ':setup_fee'=>($item['setup_fee'] ?? 0), ':status'=>$item['status'] ?? 'active'
            ]);
            echo "  Inserted: $slug\n"; $totalInserted++;
        }
        continue;
    }

    if (stripos($base, 'products') !== false) {
        $insert = $pdo->prepare('INSERT INTO products (category,name,sku,description,meta,price,status,created_at) VALUES (:category,:name,:sku,:description,:meta,:price,:status,NOW())');
        $check = $pdo->prepare('SELECT id FROM products WHERE sku = :sku LIMIT 1');
        foreach ($data as $item) {
            $sku = $item['sku'] ?? null; if (!$sku) continue;
            $check->execute([':sku'=>$sku]); if ($check->fetch()) { echo "  Skipping existing product: $sku\n"; continue; }
            $meta = json_encode($item['meta'] ?? new stdClass());
            $insert->execute([
                ':category'=>$item['category'] ?? null, ':name'=>$item['name'] ?? $sku, ':sku'=>$sku, ':description'=>$item['description'] ?? null,
                ':meta'=>$meta, ':price'=>($item['price'] ?? 0), ':status'=>$item['status'] ?? 'active'
            ]);
            echo "  Inserted product: $sku\n"; $totalInserted++;
        }
        continue;
    }

    if (stripos($base, 'service_catalog') !== false || stripos($base, 'services') !== false) {
        $insert = $pdo->prepare('INSERT INTO service_catalog (code,name,type,meta,price,status) VALUES (:code,:name,:type,:meta,:price,:status)');
        $check = $pdo->prepare('SELECT id FROM service_catalog WHERE code = :code LIMIT 1');
        foreach ($data as $item) {
            $code = $item['code'] ?? null; if (!$code) continue;
            $check->execute([':code'=>$code]); if ($check->fetch()) { echo "  Skipping existing service: $code\n"; continue; }
            $meta = json_encode($item['meta'] ?? new stdClass());
            $insert->execute([':code'=>$code,':name'=>$item['name'] ?? $code,':type'=>$item['type'] ?? null,':meta'=>$meta,':price'=>($item['price'] ?? 0),':status'=>$item['status'] ?? 'active']);
            echo "  Inserted service: $code\n"; $totalInserted++;
        }
        continue;
    }

    if (stripos($base, 'servers') !== false) {
        $insert = $pdo->prepare('INSERT INTO servers (hostname,ip_address,provider,region,specs,status,created_at) VALUES (:hostname,:ip,:provider,:region,:specs,:status,NOW())');
        $check = $pdo->prepare('SELECT id FROM servers WHERE hostname = :h LIMIT 1');
        foreach ($data as $item) {
            $h = $item['hostname'] ?? null; if (!$h) continue;
            $check->execute([':h'=>$h]); if ($check->fetch()) { echo "  Skipping existing server: $h\n"; continue; }
            $specs = json_encode($item['specs'] ?? new stdClass());
            $insert->execute([':hostname'=>$h,':ip'=>$item['ip_address'] ?? null,':provider'=>$item['provider'] ?? null,':region'=>$item['region'] ?? null,':specs'=>$specs,':status'=>$item['status'] ?? 'active']);
            echo "  Inserted server: $h\n"; $totalInserted++;
        }
        continue;
    }

    echo "  Unknown seed file: $base (skipped)\n";
}

echo "Seed complete. Total inserted: $totalInserted\n";
