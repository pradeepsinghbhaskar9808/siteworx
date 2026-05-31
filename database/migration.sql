-- SiteWorx database migration
-- Run on a MySQL/MariaDB server as a user with CREATE privileges.
SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS `siteworx_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `siteworx_db`;

-- Roles table
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users / login table
DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(80) NOT NULL,
  `name` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(191) DEFAULT NULL,
  `password` VARCHAR(255) DEFAULT NULL,
  `role_id` INT UNSIGNED NOT NULL DEFAULT 3,
  `status` ENUM('active','suspended','deleted') NOT NULL DEFAULT 'active',
  `meta` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_login_username` (`username`),
  UNIQUE KEY `uq_login_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hosting plans
DROP TABLE IF EXISTS `hosting_plans`;
CREATE TABLE `hosting_plans` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(160) NOT NULL,
  `category` VARCHAR(80) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `specs` JSON DEFAULT NULL,
  `price_monthly` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `price_yearly` DECIMAL(10,2) DEFAULT NULL,
  `setup_fee` DECIMAL(10,2) DEFAULT 0.00,
  `status` ENUM('active','draft','archived') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_plans_slug` (`slug`),
  KEY `idx_plans_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Generic products/catalog
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(80) DEFAULT NULL,
  `name` VARCHAR(150) NOT NULL,
  `sku` VARCHAR(80) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `meta` JSON DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Service catalog for misc services (emails, gsuite, dev, etc)
DROP TABLE IF EXISTS `service_catalog`;
CREATE TABLE `service_catalog` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(80) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `type` VARCHAR(80) DEFAULT NULL,
  `meta` JSON DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders and items
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(8) NOT NULL DEFAULT 'USD',
  `status` VARCHAR(40) NOT NULL DEFAULT 'pending',
  `meta` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_orders_user` (`user_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `plan_id` INT UNSIGNED DEFAULT NULL,
  `product_id` INT UNSIGNED DEFAULT NULL,
  `service_id` INT UNSIGNED DEFAULT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `period_months` INT UNSIGNED DEFAULT 1,
  `meta` JSON DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_order` (`order_id`),
  CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_items_plan` FOREIGN KEY (`plan_id`) REFERENCES `hosting_plans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_items_service` FOREIGN KEY (`service_id`) REFERENCES `service_catalog` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subscriptions table
DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `plan_id` INT UNSIGNED NOT NULL,
  `order_item_id` BIGINT UNSIGNED DEFAULT NULL,
  `started_at` DATETIME NOT NULL,
  `expires_at` DATETIME DEFAULT NULL,
  `status` ENUM('active','trialing','cancelled','expired') NOT NULL DEFAULT 'active',
  `meta` JSON DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_subs_user` (`user_id`),
  CONSTRAINT `fk_subs_user` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_subs_plan` FOREIGN KEY (`plan_id`) REFERENCES `hosting_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Invoices and payments
DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status` VARCHAR(40) NOT NULL DEFAULT 'unpaid',
  `issued_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_invoices_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `method` VARCHAR(80) DEFAULT NULL,
  `status` VARCHAR(40) DEFAULT 'completed',
  `paid_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_payments_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Servers table (for dedicated/cloud servers)
DROP TABLE IF EXISTS `servers`;
CREATE TABLE `servers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hostname` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `provider` VARCHAR(100) DEFAULT NULL,
  `region` VARCHAR(100) DEFAULT NULL,
  `specs` JSON DEFAULT NULL,
  `status` ENUM('active','maintenance','retired') DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Support tickets
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('open','pending','resolved','closed') DEFAULT 'open',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit logs
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `operation` VARCHAR(120) NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `data` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `k` VARCHAR(128) NOT NULL,
  `v` TEXT DEFAULT NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed roles
INSERT INTO `roles` (`id`,`name`,`description`) VALUES
  (1,'admin','Full administrator'),
  (2,'manager','Manager with limited admin rights'),
  (3,'client','End user / client');

-- Example admin user (no password set) - set via register.php or run a secure password hash update
INSERT INTO `login` (`username`,`name`,`email`,`password`,`role_id`,`status`,`created_at`) VALUES
  ('admin','Administrator','admin@example.com','',1,'active',NOW());

SET FOREIGN_KEY_CHECKS=1;

-- Notes:
-- 1) The `password` field expects a PHP bcrypt hash (generated by password_hash()).
-- 2) Use `register.php` to create real users or replace the admin row with a hashed password.
-- 3) You can extend the schema (coupons, taxes, gateways) as needed.
