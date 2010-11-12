ALTER TABLE `test_newsletters` ADD `id` smallint(5) NULL DEFAULT NULL AFTER `email`;
ALTER TABLE `test_newsletters` DROP INDEX `email`;
ALTER TABLE `test_newsletters` CHANGE `id` `id` smallint(5) NOT NULL  auto_increment PRIMARY KEY;
ALTER TABLE `test_newsletters` ADD UNIQUE `UNIQUE` (`email`);