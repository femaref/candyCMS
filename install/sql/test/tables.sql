DROP TABLE IF EXISTS `blogs`;

CREATE TABLE `blogs` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teaser` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(160) DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(2) DEFAULT 'en',
  `date` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tags` (`tags`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `blogs` WRITE;

INSERT INTO `blogs` (`id`, `author_id`, `title`, `teaser`, `tags`, `keywords`, `content`, `language`, `date`, `date_modified`, `published`)
VALUES
	(1,1,'b3cf6b2dd0',NULL,NULL,NULL,'','en',NULL,NULL,1),
	(2,1,'e12b3a84b2',NULL,NULL,NULL,'','en',NULL,NULL,0);

UNLOCK TABLES;


DROP TABLE IF EXISTS `calendars`;

CREATE TABLE `calendars` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL DEFAULT '',
  `content` text,
  `date` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `calendars` WRITE;

INSERT INTO `calendars` (`id`, `author_id`, `title`, `content`, `date`, `start_date`, `end_date`)
VALUES
	(1,1,'7c015444a5','',0,'2000-01-01','0000-00-00'),
	(2,1,'8f9e4a9962','',0,'2020-01-01','2020-12-31');

UNLOCK TABLES;


DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `parent_id` int(9) NOT NULL,
  `author_id` smallint(5) NOT NULL,
  `author_facebook_id` int(15) DEFAULT NULL,
  `author_name` varchar(32) NOT NULL,
  `author_email` varchar(64) DEFAULT '',
  `author_ip` varchar(15) DEFAULT '',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parentID` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `comments` WRITE;

INSERT INTO `comments` (`id`, `parent_id`, `author_id`, `author_facebook_id`, `author_name`, `author_email`, `author_ip`, `content`, `date`)
VALUES
	(1,1,1,NULL,'','','','7c883dc7d2',0),
	(2,1,0,NULL,'Test Commenter','test@example.com','','2e8e0b2d93',0);

UNLOCK TABLES;


DROP TABLE IF EXISTS `contents`;

CREATE TABLE `contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `teaser` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` varchar(160) COLLATE latin1_general_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

LOCK TABLES `contents` WRITE;

INSERT INTO `contents` (`id`, `author_id`, `title`, `teaser`, `keywords`, `content`, `date`, `published`)
VALUES
	(1,1,'18855f87f2',NULL,NULL,'',0,1),
	(2,1,'8f7fb844b0',NULL,NULL,'',0,0);

UNLOCK TABLES;


DROP TABLE IF EXISTS `downloads`;

CREATE TABLE `downloads` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `category` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `file` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `extension` varchar(4) NOT NULL DEFAULT '',
  `downloads` int(11) DEFAULT '0',
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `downloads` WRITE;

INSERT INTO `downloads` (`id`, `author_id`, `title`, `content`, `category`, `file`, `extension`, `downloads`, `date`)
VALUES
	(1,1,'098dec456d',NULL,NULL,'none','ext',0,0);

UNLOCK TABLES;


DROP TABLE IF EXISTS `gallery_albums`;

CREATE TABLE `gallery_albums` (
  `id` smallint(5) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `gallery_albums` WRITE;

INSERT INTO `gallery_albums` (`id`, `author_id`, `title`, `content`, `date`)
VALUES
	(1,1,'','6dffc4c552',0);

UNLOCK TABLES;


DROP TABLE IF EXISTS `gallery_files`;

CREATE TABLE `gallery_files` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `author_id` smallint(5) NOT NULL,
  `album_id` smallint(5) NOT NULL,
  `date` int(11) NOT NULL,
  `file` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `extension` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'jpg',
  `content` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `gallery_files` WRITE;

INSERT INTO `gallery_files` (`id`, `author_id`, `album_id`, `date`, `file`, `extension`, `content`)
VALUES
	(1,1,1,0,'982e960e18','jpg',NULL);

UNLOCK TABLES;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `logs` WRITE;

INSERT INTO `logs` (`id`, `section_name`, `action_name`, `action_id`, `time_start`, `time_end`, `user_id`)
VALUES
	(1,'blog','create',1,2020,NULL,1);

UNLOCK TABLES;


DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(100) DEFAULT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session` varchar(32) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT '1',
  `date` int(11) DEFAULT NULL,
  `verification_code` varchar(12) COLLATE latin1_general_ci DEFAULT NULL,
  `last_login` int(11) DEFAULT NULL,
  `api_token` varchar(32) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `content` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `use_gravatar` tinyint(1) DEFAULT '0',
  `receive_newsletter` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `userright` (`role`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `surname`, `password`, `email`, `role`, `date`, `verification_code`, `last_login`, `api_token`, `content`, `use_gravatar`, `receive_newsletter`)
VALUES
	(1,'Administrator','c2f9619961','098f6bcd4621d373cade4e832627b4f6','admin@example.com',4,NULL,NULL,NULL,'',NULL,0,1),
	(2,'Moderator','c3f32cb996','098f6bcd4621d373cade4e832627b4f6','moderator@example.com',3,NULL,NULL,NULL,'',NULL,0,1),
	(3,'Facebook-User','4ef590ffb5','098f6bcd4621d373cade4e832627b4f6','facebook@example.com',2,NULL,NULL,NULL,'',NULL,0,1),
	(4,'Member','6b6ff4a437','098f6bcd4621d373cade4e832627b4f6','member@example.com',1,NULL,NULL,NULL,'',NULL,0,1),
	(5,'Unverified','6ccfcbb125','098f6bcd4621d373cade4e832627b4f6','unverified@example.com',1,NULL,'6ccfcbb125',NULL,'',NULL,0,1);

UNLOCK TABLES;