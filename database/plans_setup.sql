-- ============================================================
-- SOM Property Management — System Plans Setup
-- Run this against an EXISTING database to install/refresh the
-- five subscription tiers. Only admins manage plans in the app.
--
-- "less than or equal to N units" is enforced by the app's limit
-- check (units >= max_apartments), so max_apartments = N exactly
-- (an owner may hold up to and including N apartments).
--
-- Usage (XAMPP / MySQL):
--   mysql -u root som_property < database/plans_setup.sql
--   (or paste into phpMyAdmin > SQL for the som_property database)
--
-- Plan IDs 1-5 are preserved so existing owners stay linked.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM `plans`;
ALTER TABLE `plans` AUTO_INCREMENT = 1;

INSERT INTO `plans` (`id`, `name`, `slug`, `price_monthly`, `max_apartments`, `features`, `is_active`) VALUES
(1, 'Starter', 'starter',  20.00,  14,
 '["tenant_portal","rent_billing","email_notifications","complaint_tracking","advanced_reports","water_electric_billing","contract_management","pdf_exports"]', 1),
(2, 'Pro',     'pro',      30.00,  28,
 '["tenant_portal","rent_billing","email_notifications","complaint_tracking","advanced_reports","water_electric_billing","contract_management","pdf_exports","asset_register","technical_issues"]', 1),
(3, 'Premium', 'premium',  50.00,  50,
 '["tenant_portal","rent_billing","email_notifications","complaint_tracking","advanced_reports","water_electric_billing","contract_management","pdf_exports","asset_register","technical_issues","bulk_notifications","financial_analytics"]', 1),
(4, 'Maxi-1',  'maxi-1',  100.00, 100,
 '["tenant_portal","rent_billing","email_notifications","complaint_tracking","advanced_reports","water_electric_billing","contract_management","pdf_exports","asset_register","technical_issues","bulk_notifications","financial_analytics","multi_property","priority_support"]', 1),
(5, 'Maxi-2',  'maxi-2',  150.00, 200,
 '["tenant_portal","rent_billing","email_notifications","complaint_tracking","advanced_reports","water_electric_billing","contract_management","pdf_exports","asset_register","technical_issues","bulk_notifications","financial_analytics","multi_property","priority_support","api_access","custom_branding","dedicated_support"]', 1);

SET FOREIGN_KEY_CHECKS = 1;

-- Verify:
-- SELECT id, name, price_monthly, max_apartments,
--        CONCAT('<= ', max_apartments, ' units') AS capacity
-- FROM plans ORDER BY price_monthly;
