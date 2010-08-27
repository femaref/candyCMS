DROP TABLE IF EXISTS `blog`;

CREATE TABLE `blog` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL DEFAULT '1',
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tags` (`tags`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf-8;


DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `parent_id` int(9) NOT NULL,
  `parent_category` char(1) NOT NULL DEFAULT 'b',
  `author_id` smallint(5) NOT NULL,
  `author_name` varchar(32) DEFAULT '',
  `author_email` varchar(64) DEFAULT '',
  `author_ip` varchar(15) DEFAULT '',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `parent_category` (`parent_category`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf-8;


DROP TABLE IF EXISTS `content`;

CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL DEFAULT '1',
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf-8;


DROP TABLE IF EXISTS `gallery_album`;

CREATE TABLE `gallery_album` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `gallery_file`;

CREATE TABLE `gallery_file` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `album_id` smallint(5) NOT NULL,
  `author_id` smallint(5) NOT NULL,
  `date` int(11) NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extension` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'jpg',
  `file` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `newsletter`;

CREATE TABLE `newsletter` (
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `receive_newsletter` tinyint(1) NOT NULL DEFAULT '1',
  `use_gravatar` tinyint(1) DEFAULT '0',
  `session` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_right` tinyint(1) NOT NULL DEFAULT '1',
  `date` int(11) DEFAULT NULL,
  `verification_code` varchar(12) COLLATE latin1_general_ci DEFAULT NULL,
  `last_login` int(11) DEFAULT NULL,
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `name` (`name`),
  KEY `user_right` (`user_right`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;