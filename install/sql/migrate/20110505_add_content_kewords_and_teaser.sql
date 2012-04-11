ALTER TABLE `%SQL_PREFIX%contents` ADD `teaser` varchar(140) NULL DEFAULT NULL  AFTER `title`;
ALTER TABLE `%SQL_PREFIX%contents` ADD `keywords` varchar(160) NULL DEFAULT NULL  AFTER `teaser`;