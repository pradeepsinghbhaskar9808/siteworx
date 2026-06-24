-- Dedicated Hosting Plans (Linux India)
-- Category: dedicated-hosting
INSERT INTO `hosting_plans` (`name`, `slug`, `category`, `description`, `specs`, `price_monthly`, `price_yearly`, `setup_fee`, `currency`, `status`, `created_at`) VALUES

('Dedicated Starter Plan', 'dedicated-starter', 'dedicated-hosting', 'Linux dedicated server for small to medium businesses.',
 JSON_OBJECT(
   'ram', '4 GB',
   'cpu_cores', '2 Core',
   'storage', '1 TB',
   'bandwidth', '10 TB',
   'uptime', '99.9% Uptime Guarantee',
   'setup', '$20',
   'os', 'Linux'
 ), 5900.00, 59000.00, 2000.00, 'INR', 'active', NOW()),

('Dedicated Advanced Plan', 'dedicated-advanced', 'dedicated-hosting', 'Advanced Linux dedicated server with more resources.',
 JSON_OBJECT(
   'ram', '4 GB',
   'cpu_cores', '4 Core',
   'storage', '2 TB',
   'bandwidth', '20 TB',
   'uptime', '99.9% Uptime Guarantee',
   'setup', '$20',
   'os', 'Linux'
 ), 7900.00, 79000.00, 2000.00, 'INR', 'active', NOW()),

('Dedicated Professional Plan', 'dedicated-professional', 'dedicated-hosting', 'Professional dedicated server with premium resources.',
 JSON_OBJECT(
   'ram', '6 GB',
   'cpu_cores', '4 Core',
   'storage', '4 TB',
   'bandwidth', '30 TB',
   'uptime', '99.9% Uptime Guarantee',
   'setup', '$15',
   'os', 'Linux'
 ), 9900.00, 99000.00, 1500.00, 'INR', 'active', NOW()),

('Dedicated Enterprise Plan', 'dedicated-enterprise', 'dedicated-hosting', 'Enterprise dedicated server with maximum performance.',
 JSON_OBJECT(
   'ram', '6 GB',
   'cpu_cores', '6 Core',
   'storage', '6 TB',
   'bandwidth', '40 TB',
   'uptime', '99.9% Uptime Guarantee',
   'setup', '$15',
   'os', 'Linux'
 ), 11900.00, 119000.00, 1500.00, 'INR', 'active', NOW()),

('Dedicated Ultimate Plan', 'dedicated-ultimate', 'dedicated-hosting', 'Ultimate dedicated server for maximum power.',
 JSON_OBJECT(
   'ram', '8 GB',
   'cpu_cores', '6 Core',
   'storage', '8 TB',
   'bandwidth', '60 TB',
   'uptime', '99.9% Uptime Guarantee',
   'setup', 'FREE',
   'os', 'Linux'
 ), 13900.00, 139000.00, 0.00, 'INR', 'active', NOW());
