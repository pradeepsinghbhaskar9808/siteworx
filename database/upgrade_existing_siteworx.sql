-- SiteWorx upgrade for an existing siteworx_db database.
-- Run this if your database already exists and you do not want to DROP/CREATE tables.

USE `siteworx_db`;

ALTER TABLE `hosting_plans`
  ADD COLUMN IF NOT EXISTS `currency` VARCHAR(8) NOT NULL DEFAULT 'INR' AFTER `setup_fee`;

ALTER TABLE `login`
  ADD COLUMN IF NOT EXISTS `manager_id` INT UNSIGNED DEFAULT NULL AFTER `role_id`,
  ADD COLUMN IF NOT EXISTS `status` ENUM('active','suspended','deleted') NOT NULL DEFAULT 'active' AFTER `manager_id`,
  ADD COLUMN IF NOT EXISTS `reset_token` VARCHAR(128) DEFAULT NULL AFTER `status`,
  ADD COLUMN IF NOT EXISTS `reset_expiry` DATETIME DEFAULT NULL AFTER `reset_token`,
  ADD COLUMN IF NOT EXISTS `meta` JSON DEFAULT NULL AFTER `reset_expiry`,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `meta`,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

ALTER TABLE `subscriptions`
  ADD COLUMN IF NOT EXISTS `service_id` INT UNSIGNED DEFAULT NULL AFTER `plan_id`,
  ADD COLUMN IF NOT EXISTS `server_id` INT UNSIGNED DEFAULT NULL AFTER `service_id`;

UPDATE `hosting_plans`
SET `currency` = 'INR'
WHERE `currency` IS NULL OR `currency` = '';
