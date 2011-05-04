DROP TABLE IF EXISTS `blogs`;

CREATE TABLE `blogs` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL DEFAULT '1',
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teaser` varchar(140) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(160) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tags` (`tags`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `parent_id` int(9) NOT NULL,
  `author_id` smallint(5) NOT NULL,
  `author_facebook_id` int(15) DEFAULT NULL,
  `author_name` varchar(32) DEFAULT '',
  `author_email` varchar(64) DEFAULT '',
  `author_ip` varchar(15) DEFAULT '',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `parent_category` (`parent_category`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `contents`;

CREATE TABLE `contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL DEFAULT '1',
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teaser` varchar(140) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(160) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `gallery_albums`;

CREATE TABLE `gallery_albums` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `gallery_files`;

CREATE TABLE `gallery_files` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `album_id` smallint(5) NOT NULL,
  `author_id` smallint(5) NOT NULL,
  `date` int(11) NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `extension` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'jpg',
  `file` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `logs`;

CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(32) NOT NULL DEFAULT 'NOT NULL',
  `action_name` varchar(16) NOT NULL,
  `action_id` smallint(6) DEFAULT NULL,
  `time_start` int(11) DEFAULT NULL,
  `time_end` int(11) DEFAULT NULL,
  `user_id` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(100) DEFAULT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `newsletters`;

CREATE TABLE `newsletters` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;