ALTER TABLE `#__jopensimpaypal_payoutrequests` ADD COLUMN `transferred`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `status`;
