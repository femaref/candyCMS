CREATE TABLE `%SQL_PREFIX%downloads` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `category` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `file` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `extension` varchar(4) NOT NULL DEFAULT '',
  `downloads` int(11) DEFAULT '0',
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;