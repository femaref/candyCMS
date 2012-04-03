DROP TABLE IF EXISTS `%SQL_PREFIX%blogs`;

CREATE TABLE `%SQL_PREFIX%blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teaser` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(160) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `language` VARCHAR(2)  NULL  DEFAULT 'en',
  `date` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tags` (`tags`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `%SQL_PREFIX%comments`;

CREATE TABLE `%SQL_PREFIX%comments` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `parent_id` int(9) NOT NULL,
  `author_id` int(11) NOT NULL,
  `author_facebook_id` int(15) DEFAULT NULL,
  `author_name` varchar(32) DEFAULT '',
  `author_email` varchar(64) DEFAULT '',
  `author_ip` varchar(15) DEFAULT '',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%SQL_PREFIX%contents`;

CREATE TABLE `%SQL_PREFIX%contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teaser` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(160) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%SQL_PREFIX%calendars`;

CREATE TABLE `%SQL_PREFIX%calendars` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL DEFAULT '',
  `content` text,
  `date` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%SQL_PREFIX%downloads`;

CREATE TABLE `%SQL_PREFIX%downloads` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `category` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `file` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `extension` varchar(4) NOT NULL DEFAULT '',
  `downloads` int(11) DEFAULT '0',
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%SQL_PREFIX%gallery_albums`;

CREATE TABLE `%SQL_PREFIX%gallery_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  `content` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `%SQL_PREFIX%gallery_files`;

CREATE TABLE `%SQL_PREFIX%gallery_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` int(11) NOT NULL,
  `author_id` int(115) NOT NULL,
  `file` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'jpg',
  `date` int(11) NOT NULL,
  `content` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%SQL_PREFIX%logs`;

CREATE TABLE `%SQL_PREFIX%logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller_name` varchar(32) NOT NULL DEFAULT 'NOT NULL',
  `action_name` varchar(16) NOT NULL,
  `action_id` int(11) DEFAULT NULL,
  `time_start` int(11) DEFAULT NULL,
  `time_end` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%SQL_PREFIX%migrations`;

CREATE TABLE `%SQL_PREFIX%migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(100) DEFAULT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%SQL_PREFIX%sessions`;

CREATE TABLE `%SQL_PREFIX%sessions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session` varchar(32) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `%SQL_PREFIX%users`;

CREATE TABLE `%SQL_PREFIX%users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `receive_newsletter` tinyint(1) NOT NULL DEFAULT '1',
  `use_gravatar` tinyint(1) DEFAULT '0',
  `role` tinyint(1) NOT NULL DEFAULT '1',
  `date` int(11) DEFAULT NULL,
  `verification_code` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_token` VARCHAR(32)  NOT NULL  DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `api_token` (`api_token`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;