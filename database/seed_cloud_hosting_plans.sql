-- Cloud Hosting Plans
-- Categories: cloud-hosting
INSERT INTO `hosting_plans` (`name`, `slug`, `category`, `description`, `specs`, `price_monthly`, `price_yearly`, `setup_fee`, `currency`, `status`, `created_at`) VALUES

('Cloud Business Plan', 'cloud-business', 'cloud-hosting', 'Cloud hosting for growing businesses.',
 JSON_OBJECT(
   'cpu_cores', '2',
   'ram', '2 GB',
   'storage', '40 GB SSD',
   'bandwidth', '2 TB',
   'ip_addresses', '1',
   'control_panel', 'CPanel',
   'free_setup', 'FREE Setup',
   'uptime', '99.9% Uptime Guarantee'
 ), 4999.00, 49990.00, 0.00, 'INR', 'active', NOW()),

('Cloud Professional Plan', 'cloud-professional', 'cloud-hosting', 'Professional cloud hosting with more resources.',
 JSON_OBJECT(
   'cpu_cores', '4',
   'ram', '4 GB',
   'storage', '80 GB SSD',
   'bandwidth', '4 TB',
   'ip_addresses', '2',
   'control_panel', 'CPanel',
   'free_setup', 'FREE Setup',
   'uptime', '99.9% Uptime Guarantee'
 ), 7999.00, 79990.00, 0.00, 'INR', 'active', NOW()),

('Cloud Enterprise Plan', 'cloud-enterprise', 'cloud-hosting', 'Enterprise-grade cloud hosting.',
 JSON_OBJECT(
   'cpu_cores', '8',
   'ram', '8 GB',
   'storage', '160 GB SSD',
   'bandwidth', '8 TB',
   'ip_addresses', '4',
   'control_panel', 'CPanel',
   'free_setup', 'FREE Setup',
   'uptime', '99.9% Uptime Guarantee'
 ), 12999.00, 129990.00, 0.00, 'INR', 'active', NOW());
