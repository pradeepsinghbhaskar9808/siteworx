-- VPS Hosting Plans (Windows USA)
-- Category: vps-hosting
INSERT INTO `hosting_plans` (`name`, `slug`, `category`, `description`, `specs`, `price_monthly`, `price_yearly`, `setup_fee`, `currency`, `status`, `created_at`) VALUES

('VPS Business Plan', 'vps-business', 'vps-hosting', 'Windows VPS hosting for businesses.',
 JSON_OBJECT(
   'cpu_cores', '2',
   'ram', '2 GB',
   'storage', '40 GB SSD',
   'bandwidth', '2 TB',
   'ip_addresses', '1',
   'control_panel', 'Plesk',
   'free_setup', 'FREE Setup',
   'uptime', '99.9% Uptime Guarantee'
 ), 5999.00, 59990.00, 0.00, 'INR', 'active', NOW()),

('VPS Traffic Plan', 'vps-traffic', 'vps-hosting', 'High-traffic VPS hosting solution.',
 JSON_OBJECT(
   'cpu_cores', '4',
   'ram', '4 GB',
   'storage', '80 GB SSD',
   'bandwidth', '4 TB',
   'ip_addresses', '2',
   'control_panel', 'Plesk',
   'free_setup', 'FREE Setup',
   'uptime', '99.9% Uptime Guarantee'
 ), 8999.00, 89990.00, 0.00, 'INR', 'active', NOW()),

('VPS Professional Plan', 'vps-professional', 'vps-hosting', 'Professional VPS with enhanced performance.',
 JSON_OBJECT(
   'cpu_cores', '6',
   'ram', '6 GB',
   'storage', '120 GB SSD',
   'bandwidth', '6 TB',
   'ip_addresses', '3',
   'control_panel', 'Plesk',
   'free_setup', 'FREE Setup',
   'uptime', '99.9% Uptime Guarantee'
 ), 11999.00, 119990.00, 0.00, 'INR', 'active', NOW()),

('VPS Enterprise Plan', 'vps-enterprise', 'vps-hosting', 'Enterprise VPS hosting solution.',
 JSON_OBJECT(
   'cpu_cores', '8',
   'ram', '8 GB',
   'storage', '160 GB SSD',
   'bandwidth', '8 TB',
   'ip_addresses', '4',
   'control_panel', 'Plesk',
   'free_setup', 'FREE Setup',
   'uptime', '99.9% Uptime Guarantee'
 ), 14999.00, 149990.00, 0.00, 'INR', 'active', NOW());
