<?php
// Seeder: import JSON seeds into hosting_plans, products, service_catalog, and servers.
// CLI usage: php seed_import.php
// Browser usage: login as admin, then open seed_import.php?run=1

$isCli = php_sapi_name() === 'cli';
if (!$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
    if (($_GET['run'] ?? '') !== '1') {
        echo "Open seed_import.php?run=1 to import seed data after admin login.\n";
        exit(0);
    }
}

require_once __DIR__ . '/connection.php';
if (!$isCli) {
    require_once __DIR__ . '/lib_auth.php';
    require_role($pdo, ['admin']);
}

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
        $insert = $pdo->prepare('INSERT INTO hosting_plans (name,slug,category,description,specs,price_monthly,price_yearly,setup_fee,currency,status,created_at) VALUES (:name,:slug,:category,:description,:specs,:price_monthly,:price_yearly,:setup_fee,:currency,:status,NOW())');
        $update = $pdo->prepare('UPDATE hosting_plans SET name=:name, category=:category, description=:description, specs=:specs, price_monthly=:price_monthly, price_yearly=:price_yearly, setup_fee=:setup_fee, currency=:currency, status=:status WHERE slug=:slug');
        $check = $pdo->prepare('SELECT id FROM hosting_plans WHERE slug = :slug LIMIT 1');
        foreach ($data as $item) {
            $slug = $item['slug'] ?? null; if (!$slug) continue;
            $params = [
                ':name'=>$item['name'] ?? $slug,
                ':slug'=>$slug,
                ':category'=>$item['category'] ?? 'misc',
                ':description'=>$item['description'] ?? null,
                ':specs'=>json_encode($item['specs'] ?? []),
                ':price_monthly'=>($item['price_monthly'] ?? 0),
                ':price_yearly'=>($item['price_yearly'] ?? null),
                ':setup_fee'=>($item['setup_fee'] ?? 0),
                ':currency'=>$item['currency'] ?? 'INR',
                ':status'=>$item['status'] ?? 'active'
            ];
            $check->execute([':slug'=>$slug]);
            if ($check->fetch()) {
                $update->execute($params);
                echo "  Updated: $slug\n";
                continue;
            }
            $specs = json_encode($item['specs'] ?? []);
            $params[':specs'] = $specs;
            $insert->execute($params);
            echo "  Inserted: $slug\n"; $totalInserted++;
        }
        continue;
    }

    if (stripos($base, 'products') !== false) {
        $insert = $pdo->prepare('INSERT INTO products (category,name,sku,description,meta,price,status,created_at) VALUES (:category,:name,:sku,:description,:meta,:price,:status,NOW())');
        $update = $pdo->prepare('UPDATE products SET category=:category,name=:name,description=:description,meta=:meta,price=:price,status=:status WHERE sku=:sku');
        $check = $pdo->prepare('SELECT id FROM products WHERE sku = :sku LIMIT 1');
        foreach ($data as $item) {
            $sku = $item['sku'] ?? null; if (!$sku) continue;
            $meta = json_encode($item['meta'] ?? new stdClass());
            $params = [
                ':category'=>$item['category'] ?? null, ':name'=>$item['name'] ?? $sku, ':sku'=>$sku, ':description'=>$item['description'] ?? null,
                ':meta'=>$meta, ':price'=>($item['price'] ?? 0), ':status'=>$item['status'] ?? 'active'
            ];
            $check->execute([':sku'=>$sku]);
            if ($check->fetch()) {
                $update->execute($params);
                echo "  Updated product: $sku\n";
                continue;
            }
            $insert->execute($params);
            echo "  Inserted product: $sku\n"; $totalInserted++;
        }
        continue;
    }

    if (stripos($base, 'service_catalog') !== false || stripos($base, 'services') !== false) {
        $insert = $pdo->prepare('INSERT INTO service_catalog (code,name,type,meta,price,status) VALUES (:code,:name,:type,:meta,:price,:status)');
        $update = $pdo->prepare('UPDATE service_catalog SET name=:name,type=:type,meta=:meta,price=:price,status=:status WHERE code=:code');
        $check = $pdo->prepare('SELECT id FROM service_catalog WHERE code = :code LIMIT 1');
        foreach ($data as $item) {
            $code = $item['code'] ?? null; if (!$code) continue;
            $meta = json_encode($item['meta'] ?? new stdClass());
            $params = [':code'=>$code,':name'=>$item['name'] ?? $code,':type'=>$item['type'] ?? null,':meta'=>$meta,':price'=>($item['price'] ?? 0),':status'=>$item['status'] ?? 'active'];
            $check->execute([':code'=>$code]);
            if ($check->fetch()) {
                $update->execute($params);
                echo "  Updated service: $code\n";
                continue;
            }
            $insert->execute($params);
            echo "  Inserted service: $code\n"; $totalInserted++;
        }
        continue;
    }

    if (stripos($base, 'servers') !== false) {
        $insert = $pdo->prepare('INSERT INTO servers (hostname,ip_address,provider,region,specs,status,created_at) VALUES (:hostname,:ip,:provider,:region,:specs,:status,NOW())');
        $update = $pdo->prepare('UPDATE servers SET ip_address=:ip,provider=:provider,region=:region,specs=:specs,status=:status WHERE hostname=:hostname');
        $check = $pdo->prepare('SELECT id FROM servers WHERE hostname = :h LIMIT 1');
        foreach ($data as $item) {
            $h = $item['hostname'] ?? null; if (!$h) continue;
            $specs = json_encode($item['specs'] ?? new stdClass());
            $params = [':hostname'=>$h,':ip'=>$item['ip_address'] ?? null,':provider'=>$item['provider'] ?? null,':region'=>$item['region'] ?? null,':specs'=>$specs,':status'=>$item['status'] ?? 'active'];
            $check->execute([':h'=>$h]);
            if ($check->fetch()) {
                $update->execute($params);
                echo "  Updated server: $h\n";
                continue;
            }
            $insert->execute($params);
            echo "  Inserted server: $h\n"; $totalInserted++;
        }
        continue;
    }

    echo "  Unknown seed file: $base (skipped)\n";
}

echo "Seed complete. Total inserted: $totalInserted\n";
