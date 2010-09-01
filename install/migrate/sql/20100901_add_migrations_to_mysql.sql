CREATE TABLE `migration` (id INT) DEFAULT CHARACTER SET `utf8`;
ALTER TABLE `migration` ADD `file` varchar(100) NULL DEFAULT NULL  AFTER `id`;
ALTER TABLE `migration` ADD `date` int(11) NULL DEFAULT NULL  AFTER `file`;
ALTER TABLE `migration` CHANGE `date` `date` int(11) NOT NULL;
ALTER TABLE `migration` CHANGE `id` `id` int(11) NULL  auto_increment PRIMARY KEY;