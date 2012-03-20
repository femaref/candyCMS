DROP TABLE `newsletters`;
ALTER TABLE `users` CHANGE `receive_newsletter` `receive_newsletter` TINYINT(1)  NULL  DEFAULT '0';