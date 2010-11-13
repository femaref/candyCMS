ALTER TABLE `newsletters` ADD `id` smallint(5) NULL DEFAULT NULL AFTER `email`;
ALTER TABLE `newsletters` DROP INDEX `email`;
ALTER TABLE `newsletters` CHANGE `id` `id` smallint(5) NOT NULL  auto_increment PRIMARY KEY;
ALTER TABLE `newsletters` ADD UNIQUE `UNIQUE` (`email`);