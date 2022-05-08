-- Initial, GitHub
UPDATE `app_modules` SET `module_name` = 'Settings', `module_url` = 'Settings', `module_html` = 'Settings', `module_description` = 'Settings' WHERE `app_modules`.`module_id` = 1;
UPDATE `app_methods` SET `method_name` = 'Entity', `method_url` = 'Entity', `method_description` = 'Allows you to configure the general information of the business/company' WHERE `app_methods`.`method_id` = 1;
UPDATE `app_methods` SET `method_name` = 'Users', `method_url` = 'Users', `method_description` = 'Allows you to manage users and permissions for each user' WHERE `app_methods`.`method_id` = 2;
UPDATE `app_methods` SET `method_name` = 'Preferences', `method_url` = 'Preferences', `method_description` = 'Allows to set and modify optional system parameters in the company' WHERE `app_methods`.`method_id` = 3;
UPDATE `app_methods` SET `method_name` = 'About BlackPHP', `method_url` = 'About', `method_description` = 'Shows system information: Version, contact and technical support' WHERE `app_methods`.`method_id` = 4;
-- Nahutech Local Test
-- 2022-05-08
UPDATE `app_themes` SET `theme_name` = 'Blue - Lateral menu' WHERE `app_themes`.`theme_id` = 1;
UPDATE `app_themes` SET `theme_name` = 'Black - Lateral menu' WHERE `app_themes`.`theme_id` = 2;
UPDATE `app_themes` SET `theme_name` = 'Green - Lateral menu' WHERE `app_themes`.`theme_id` = 3;
UPDATE `app_themes` SET `theme_name` = 'Blue - Top menu' WHERE `app_themes`.`theme_id` = 4;
-- Teleinf Local test
