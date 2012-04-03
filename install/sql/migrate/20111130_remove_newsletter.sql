DROP TABLE `%SQL_PREFIX%newsletters`;
ALTER TABLE `%SQL_PREFIX%users` CHANGE `receive_newsletter` `receive_newsletter` TINYINT(1)  NULL  DEFAULT '0';