CREATE TABLE `partners` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
	`name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`shop_name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`type` TINYINT NOT NULL DEFAULT '1',
	`phone` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`emergency_contact` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`email` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`address` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`district_id` BIGINT UNSIGNED NULL DEFAULT NULL,
	`postcode` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`image` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`nid_file` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`trade_license_file` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`trade_license` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`status` TINYINT(1) NOT NULL DEFAULT '1',
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `partners_phone_unique` (`phone`) USING BTREE,
	INDEX `partners_user_id_foreign` (`user_id`) USING BTREE,
	INDEX `partners_district_id_foreign` (`district_id`) USING BTREE,
	CONSTRAINT `partners_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON UPDATE NO ACTION ON DELETE SET NULL,
	CONSTRAINT `partners_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION ON DELETE SET NULL
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2
;
INSERT INTO `auto_charts_accounts` (`id`, `auto_master_account_id`, `title`, `code`, `created_at`, `updated_at`) VALUES (10, 1, 'Accounts Receivable', '103', '2026-05-18 15:50:21', '2026-05-18 15:50:22');

INSERT INTO `charts_accounts` (`id`, `title`, `code`, `auto_charts_accounts_id`, `master_account_id`, `created_at`, `updated_at`) VALUES (10, 'Accounts Receivable', '103', 10, 1, '2026-05-18 15:52:44', '2026-05-18 15:52:45');

INSERT INTO `auto_charts_accounts` (`id`, `auto_master_account_id`, `title`, `code`, `created_at`, `updated_at`) VALUES (11, 2, 'Wallet Liability Account', '203', '2026-05-18 16:18:09', '2026-05-18 16:18:10');

INSERT INTO `charts_accounts` (`id`, `title`, `code`, `auto_charts_accounts_id`, `master_account_id`, `created_at`, `updated_at`) VALUES (11, 'Wallet Liability Account', '203', 11, 2, '2026-05-18 16:18:09', '2026-05-18 16:18:10');

customer table full update korte hobe
partner table full update korte hobe
payment_accounts table full update korte hobe
add_money_requests table full update korte hobe
order table full update korte hobe

purchase table full update korte hobe
purchase_items table full update korte hobe
product_stocks table full update korte hobe
stock_movements	table full update korte hobe
stock_snapshots table full update korte hobe
product_prices table full update korte hobe

#############done#############

sales table full update korte hobe
sale_items table full update korte hobe
designations table full update korte hobe with data 

designations ar data add 

INSERT INTO designations
    (name, code, description, status, created_by, parent_id, created_at, updated_at)
VALUES
    ('Chairman', 'CHAIRMAN', 'Chairman', 1, 1, NULL, NOW(), NOW()),
    ('Managing Director', 'MD', 'Managing Director', 1, 1, NULL, NOW(), NOW()),
    ('Deputy Managing Director', 'DMD', 'Deputy Managing Director', 1, 1, NULL, NOW(), NOW()),
    ('General Manager', 'GM', 'General Manager', 1, 1, NULL, NOW(), NOW()),
    ('Assistant General Manager', 'AGM', 'Assistant General Manager', 1, 1, NULL, NOW(), NOW()),
    ('Area Manager', 'AM', 'Area Manager', 1, 1, NULL, NOW(), NOW()),
    ('Assistant Manager', 'ASM', 'Assistant Manager', 1, 1, NULL, NOW(), NOW()),
    ('Manager', 'MGR', 'Manager', 1, 1, NULL, NOW(), NOW()),
    ('Senior Sales Executive Manager', 'SSEM', 'Senior Sales Executive Manager', 1, 1, NULL, NOW(), NOW()),
    ('Sales Executive', 'SE', 'Sales Executive', 1, 1, NULL, NOW(), NOW()),
    ('Delivery Man', 'DM', 'Delivery Man', 1, 1, NULL, NOW(), NOW());

	UPDATE designations d
	JOIN designations p ON p.name = 'Chairman'
	SET d.parent_id = p.id
	WHERE d.name = 'Managing Director';

	UPDATE designations d
	JOIN designations p ON p.name = 'Managing Director'
	SET d.parent_id = p.id
	WHERE d.name = 'Deputy Managing Director';

	UPDATE designations d
	JOIN designations p ON p.name = 'Deputy Managing Director'
	SET d.parent_id = p.id
	WHERE d.name = 'General Manager';

	UPDATE designations d
	JOIN designations p ON p.name = 'General Manager'
	SET d.parent_id = p.id
	WHERE d.name = 'Assistant General Manager';

	UPDATE designations d
	JOIN designations p ON p.name = 'Assistant General Manager'
	SET d.parent_id = p.id
	WHERE d.name = 'Area Manager';

	UPDATE designations d
	JOIN designations p ON p.name = 'Area Manager'
	SET d.parent_id = p.id
	WHERE d.name = 'Assistant Manager';

	UPDATE designations d
	JOIN designations p ON p.name = 'Assistant Manager'
	SET d.parent_id = p.id
	WHERE d.name = 'Manager';

	UPDATE designations d
	JOIN designations p ON p.name = 'Manager'
	SET d.parent_id = p.id
	WHERE d.name = 'Senior Sales Executive Manager';

	UPDATE designations d
	JOIN designations p ON p.name = 'Senior Sales Executive Manager'
	SET d.parent_id = p.id
	WHERE d.name = 'Sales Executive';

	UPDATE designations d
	JOIN designations p ON p.name = 'Sales Executive'
	SET d.parent_id = p.id
	WHERE d.name = 'Delivery Man';

	employee_relations table full update korte hobe
	marketing_member_targets table full update korte hobe
	marketing_member_commissions table full update korte hobe
	withdraw_requests table full update korte hobe
	marketing_area_assignments table full update korte hobe
	marketing_area_scopes table full update korte hobe
	employees table full update korte hobe
	partner table full update korte hobe
	settings table full update korte hobe with data
	order_transfers table full update korte hobe
	order_transfer_items table full update korte hobe

