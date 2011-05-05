ALTER TABLE `contents` ADD `teaser` varchar(140) NULL DEFAULT NULL  AFTER `title`;
ALTER TABLE `contents` ADD `keywords` varchar(160) NULL DEFAULT NULL  AFTER `teaser`;