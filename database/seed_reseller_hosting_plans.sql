-- Reseller Hosting Plans split by Linux and Windows categories.
-- These categories match hosting-reseller.php and Admin_SiteWorx/manage_plans.php.

INSERT INTO `hosting_plans` (`name`, `slug`, `category`, `description`, `specs`, `price_monthly`, `price_yearly`, `setup_fee`, `currency`, `status`, `created_at`) VALUES

('Linux Reseller Beginner Plan', 'linux-reseller-beginner', 'reseller-linux', 'Starter Linux reseller hosting plan.',
 JSON_OBJECT(
   'storage', '25 GB',
   'websites', 'Unlimited',
   'accounts', 'Unlimited WHM/cPanel',
   'bandwidth', 'Unlimited',
   'emails', 'Unlimited',
   'subdomains', 'Unlimited',
   'databases', 'Unlimited MySQL',
   'free_setup', 'FREE Set Up',
   'uptime', '99.9% Uptime Guarantee',
   'source_page', 'hosting-reseller.php'
 ), 799.00, 7990.00, 0.00, 'INR', 'active', NOW()),

('Windows Reseller Beginner Plan', 'windows-reseller-beginner', 'reseller-windows', 'Starter Windows reseller hosting plan.',
 JSON_OBJECT(
   'storage', '25 GB',
   'websites', 'Unlimited',
   'accounts', 'Unlimited Plesk',
   'bandwidth', 'Unlimited',
   'emails', 'Unlimited',
   'subdomains', 'Unlimited',
   'databases', 'Unlimited MSSQL',
   'free_setup', 'FREE Set Up',
   'uptime', '99.9% Uptime Guarantee',
   'source_page', 'hosting-reseller.php'
 ), 799.00, 7990.00, 0.00, 'INR', 'active', NOW()),

('Linux Reseller Business Plan', 'linux-reseller-business', 'reseller-linux', 'Business Linux reseller hosting plan.',
 JSON_OBJECT(
   'storage', '50 GB',
   'websites', 'Unlimited',
   'accounts', 'Unlimited WHM/cPanel',
   'bandwidth', 'Unlimited',
   'emails', 'Unlimited',
   'subdomains', 'Unlimited',
   'databases', 'Unlimited MySQL',
   'free_setup', 'FREE Set Up',
   'uptime', '99.9% Uptime Guarantee',
   'source_page', 'hosting-reseller.php'
 ), 1299.00, 12990.00, 0.00, 'INR', 'active', NOW()),

('Windows Reseller Business Plan', 'windows-reseller-business', 'reseller-windows', 'Business Windows reseller hosting plan.',
 JSON_OBJECT(
   'storage', '50 GB',
   'websites', 'Unlimited',
   'accounts', 'Unlimited Plesk',
   'bandwidth', 'Unlimited',
   'emails', 'Unlimited',
   'subdomains', 'Unlimited',
   'databases', 'Unlimited MSSQL',
   'free_setup', 'FREE Set Up',
   'uptime', '99.9% Uptime Guarantee',
   'source_page', 'hosting-reseller.php'
 ), 1299.00, 12990.00, 0.00, 'INR', 'active', NOW()),

('Linux Reseller Traffic Plan', 'linux-reseller-traffic', 'reseller-linux', 'High traffic Linux reseller hosting plan.',
 JSON_OBJECT(
   'storage', '100 GB',
   'websites', 'Unlimited',
   'accounts', 'Unlimited WHM/cPanel',
   'bandwidth', 'Unlimited',
   'emails', 'Unlimited',
   'subdomains', 'Unlimited',
   'databases', 'Unlimited MySQL',
   'free_setup', 'FREE Set Up',
   'uptime', '99.9% Uptime Guarantee',
   'source_page', 'hosting-reseller.php'
 ), 1599.00, 15990.00, 0.00, 'INR', 'active', NOW()),

('Windows Reseller Traffic Plan', 'windows-reseller-traffic', 'reseller-windows', 'High traffic Windows reseller hosting plan.',
 JSON_OBJECT(
   'storage', '100 GB',
   'websites', 'Unlimited',
   'accounts', 'Unlimited Plesk',
   'bandwidth', 'Unlimited',
   'emails', 'Unlimited',
   'subdomains', 'Unlimited',
   'databases', 'Unlimited MSSQL',
   'free_setup', 'FREE Set Up',
   'uptime', '99.9% Uptime Guarantee',
   'source_page', 'hosting-reseller.php'
 ), 1599.00, 15990.00, 0.00, 'INR', 'active', NOW()),

('Linux Reseller Standard Plan', 'linux-reseller-standard', 'reseller-linux', 'Standard Linux reseller hosting plan.',
 JSON_OBJECT(
   'storage', '200 GB',
   'websites', 'Unlimited',
   'accounts', 'Unlimited WHM/cPanel',
   'bandwidth', 'Unlimited',
   'emails', 'Unlimited',
   'subdomains', 'Unlimited',
   'databases', 'Unlimited MySQL',
   'free_setup', 'FREE Set Up',
   'uptime', '99.9% Uptime Guarantee',
   'source_page', 'hosting-reseller.php'
 ), 2199.00, 21990.00, 0.00, 'INR', 'active', NOW()),

('Windows Reseller Standard Plan', 'windows-reseller-standard', 'reseller-windows', 'Standard Windows reseller hosting plan.',
 JSON_OBJECT(
   'storage', '200 GB',
   'websites', 'Unlimited',
   'accounts', 'Unlimited Plesk',
   'bandwidth', 'Unlimited',
   'emails', 'Unlimited',
   'subdomains', 'Unlimited',
   'databases', 'Unlimited MSSQL',
   'free_setup', 'FREE Set Up',
   'uptime', '99.9% Uptime Guarantee',
   'source_page', 'hosting-reseller.php'
 ), 2199.00, 21990.00, 0.00, 'INR', 'active', NOW());
