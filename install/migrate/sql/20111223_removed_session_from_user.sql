ALTER TABLE `users` CHANGE `user_right` `right` TINYINT(1)  NOT NULL  DEFAULT '1';
ALTER TABLE `users` DROP `ip`;
