#---------------------------------------------------------------#
# Server OS: Darwin mr-mm.fritz.box 11.3.0 Darwin Kernel Version 11.3.0: Thu Jan 12 18:47:41 PST 2012; root:xnu-1699.24.23~1/RELEASE_X86_64 x86_64
#
# MySQL-Version: 5.1.42
#
# PHP-Version: 5.3.8
#
# Database: cms_new
#
# Time of backup: 2012-04-02 15:49
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Backup includes following tables:
#---------------------------------------------------------------#
# blogs
# calendars
# comments
# contents
# downloads
# gallery_albums
# gallery_files
# logs
# migrations
# newsletters
# sessions
# test_blogs
# test_calendars
# test_comments
# test_contents
# test_downloads
# test_gallery_albums
# test_gallery_files
# test_logs
# test_migrations
# test_projects
# test_sessions
# test_users
# users


#---------------------------------------------------------------#
# Table: blogs, Columns: 11
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs` (
`id` smallint(5) NOT NULL,
`author_id` smallint(5) NOT NULL default '1',
`title` varchar(128) NOT NULL,
`teaser` varchar(256) default NULL,
`tags` varchar(128) default NULL,
`keywords` varchar(160) default NULL,
`content` text NOT NULL,
`language` varchar(2) NOT NULL default 'en',
`date` int(11) default NULL,
`date_modified` int(11) default NULL,
`published` tinyint(1) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE tags (`tags`)
) AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: blogs, Rows: 2
#---------------------------------------------------------------#
INSERT INTO `blogs` VALUES ('1','2','b3cf6b2dd0','','','','','en','1','','1');
INSERT INTO `blogs` VALUES ('2','2','e12b3a84b2','','','','','en','2','','1');

#---------------------------------------------------------------#
# Table: calendars, Columns: 7
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `calendars`;
CREATE TABLE `calendars` (
`id` int(11) unsigned NOT NULL,
`author_id` int(11) NOT NULL,
`title` varchar(128) NOT NULL,
`content` text default NULL,
`date` int(11) default NULL,
`start_date` date NOT NULL,
`end_date` date default NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: calendars, Rows: 2
#---------------------------------------------------------------#
INSERT INTO `calendars` VALUES ('1','2','7c015444a5','','0','2000-01-01','0000-00-00');
INSERT INTO `calendars` VALUES ('2','2','8f9e4a9962','','0','2020-01-01','2020-12-31');

#---------------------------------------------------------------#
# Table: comments, Columns: 9
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
`id` int(9) NOT NULL,
`parent_id` int(9) NOT NULL,
`author_id` smallint(5) NOT NULL,
`author_facebook_id` int(15) default NULL,
`author_name` varchar(32) default NULL,
`author_email` varchar(64) default NULL,
`author_ip` varchar(15) default NULL,
`content` text NOT NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE parentID (`parent_id`)
) AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: comments, Rows: 2
#---------------------------------------------------------------#
INSERT INTO `comments` VALUES ('1','1','2','','','','','7c883dc7d2','0');
INSERT INTO `comments` VALUES ('2','1','0','','Test Commenter','test@example.com','','2e8e0b2d93','0');

#---------------------------------------------------------------#
# Table: contents, Columns: 8
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
`id` int(11) NOT NULL,
`author_id` smallint(5) NOT NULL default '1',
`title` varchar(128) NOT NULL,
`teaser` varchar(256) default NULL,
`content` text NOT NULL,
`keywords` varchar(160) default NULL,
`date` int(11) NOT NULL,
`published` tinyint(1) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: contents, Rows: 2
#---------------------------------------------------------------#
INSERT INTO `contents` VALUES ('1','2','18855f87f2','','','','0','1');
INSERT INTO `contents` VALUES ('2','2','8f7fb844b0','','','','0','0');

#---------------------------------------------------------------#
# Table: downloads, Columns: 9
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `downloads`;
CREATE TABLE `downloads` (
`id` int(11) unsigned NOT NULL,
`author_id` smallint(5) NOT NULL,
`title` varchar(128) NOT NULL,
`content` text default NULL,
`category` varchar(128) default NULL,
`file` varchar(64) NOT NULL,
`extension` varchar(4) NOT NULL,
`downloads` int(11) default NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: downloads, Rows: 1
#---------------------------------------------------------------#
INSERT INTO `downloads` VALUES ('1','2','098dec456d','','','none','ext','0','0');

#---------------------------------------------------------------#
# Table: gallery_albums, Columns: 5
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `gallery_albums`;
CREATE TABLE `gallery_albums` (
`id` smallint(5) NOT NULL,
`author_id` smallint(5) NOT NULL,
`title` varchar(50) NOT NULL,
`content` varchar(160) default NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: gallery_albums, Rows: 1
#---------------------------------------------------------------#
INSERT INTO `gallery_albums` VALUES ('1','2','','6dffc4c552','0');

#---------------------------------------------------------------#
# Table: gallery_files, Columns: 7
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `gallery_files`;
CREATE TABLE `gallery_files` (
`id` int(9) NOT NULL,
`album_id` smallint(5) NOT NULL,
`author_id` smallint(5) NOT NULL,
`content` varchar(160) default NULL,
`file` varchar(64) NOT NULL,
`extension` varchar(4) NOT NULL default 'jpg',
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: gallery_files, Rows: 1
#---------------------------------------------------------------#
INSERT INTO `gallery_files` VALUES ('1','1','2','','982e960e18','jpg','0');

#---------------------------------------------------------------#
# Table: logs, Columns: 7
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
`id` int(11) NOT NULL,
`controller_name` varchar(32) NOT NULL default 'NOT NULL',
`action_name` varchar(16) NOT NULL,
`action_id` smallint(6) default NULL,
`time_start` int(11) default NULL,
`time_end` int(11) default NULL,
`user_id` smallint(6) default NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1552 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: logs, Rows: 98
#---------------------------------------------------------------#
INSERT INTO `logs` VALUES ('1','blog','create','1','2020','','1');
INSERT INTO `logs` VALUES ('1455','cronjob','execute','0','1333098092','1333098092','0');
INSERT INTO `logs` VALUES ('1456','cronjob','execute','0','1333098095','1333098095','0');
INSERT INTO `logs` VALUES ('1457','cronjob','execute','0','1333098100','1333098100','0');
INSERT INTO `logs` VALUES ('1458','cronjob','execute','0','1333098101','1333098101','0');
INSERT INTO `logs` VALUES ('1459','cronjob','execute','0','1333098102','1333098103','0');
INSERT INTO `logs` VALUES ('1460','cronjob','execute','0','1333098105','1333098105','0');
INSERT INTO `logs` VALUES ('1461','cronjob','execute','0','1333098106','1333098106','0');
INSERT INTO `logs` VALUES ('1462','cronjob','execute','0','1333098107','1333098107','0');
INSERT INTO `logs` VALUES ('1463','cronjob','execute','0','1333098109','1333098109','0');
INSERT INTO `logs` VALUES ('1464','cronjob','execute','0','1333098110','1333098110','0');
INSERT INTO `logs` VALUES ('1465','cronjob','execute','0','1333098112','1333098113','0');
INSERT INTO `logs` VALUES ('1466','cronjob','execute','0','1333098115','1333098116','0');
INSERT INTO `logs` VALUES ('1467','cronjob','execute','0','1333098118','1333098118','0');
INSERT INTO `logs` VALUES ('1468','cronjob','execute','0','1333098119','1333098119','0');
INSERT INTO `logs` VALUES ('1469','cronjob','execute','0','1333098121','1333098121','0');
INSERT INTO `logs` VALUES ('1470','cronjob','execute','0','1333098122','1333098122','0');
INSERT INTO `logs` VALUES ('1471','cronjob','execute','0','1333098124','1333098124','0');
INSERT INTO `logs` VALUES ('1472','cronjob','execute','0','1333098127','1333098127','0');
INSERT INTO `logs` VALUES ('1473','cronjob','execute','0','1333098130','1333098130','0');
INSERT INTO `logs` VALUES ('1474','cronjob','execute','0','1333098132','1333098132','0');
INSERT INTO `logs` VALUES ('1475','cronjob','execute','0','1333098133','1333098133','0');
INSERT INTO `logs` VALUES ('1476','cronjob','execute','0','1333098135','1333098135','0');
INSERT INTO `logs` VALUES ('1477','cronjob','execute','0','1333098137','1333098137','0');
INSERT INTO `logs` VALUES ('1478','cronjob','execute','0','1333098140','1333098140','0');
INSERT INTO `logs` VALUES ('1479','cronjob','execute','0','1333098156','1333098156','0');
INSERT INTO `logs` VALUES ('1480','cronjob','execute','0','1333098164','1333098164','0');
INSERT INTO `logs` VALUES ('1481','cronjob','execute','0','1333098166','1333098166','0');
INSERT INTO `logs` VALUES ('1482','cronjob','execute','0','1333098169','1333098169','0');
INSERT INTO `logs` VALUES ('1483','cronjob','execute','0','1333098308','1333098308','0');
INSERT INTO `logs` VALUES ('1484','cronjob','execute','0','1333098382','1333098383','0');
INSERT INTO `logs` VALUES ('1485','cronjob','execute','0','1333098391','1333098391','0');
INSERT INTO `logs` VALUES ('1486','cronjob','execute','0','1333098392','1333098392','0');
INSERT INTO `logs` VALUES ('1487','cronjob','execute','0','1333098393','1333098393','0');
INSERT INTO `logs` VALUES ('1488','cronjob','execute','0','1333098394','1333098395','0');
INSERT INTO `logs` VALUES ('1489','cronjob','execute','0','1333098396','1333098396','0');
INSERT INTO `logs` VALUES ('1490','cronjob','execute','0','1333098397','1333098397','0');
INSERT INTO `logs` VALUES ('1491','cronjob','execute','0','1333098399','1333098399','0');
INSERT INTO `logs` VALUES ('1492','cronjob','execute','0','1333098400','1333098400','0');
INSERT INTO `logs` VALUES ('1493','cronjob','execute','0','1333098401','1333098401','0');
INSERT INTO `logs` VALUES ('1494','cronjob','execute','0','1333098402','1333098402','0');
INSERT INTO `logs` VALUES ('1495','cronjob','execute','0','1333098403','1333098403','0');
INSERT INTO `logs` VALUES ('1496','cronjob','execute','0','1333098404','1333098404','0');
INSERT INTO `logs` VALUES ('1497','cronjob','execute','0','1333098405','1333098405','0');
INSERT INTO `logs` VALUES ('1498','cronjob','execute','0','1333098407','1333098407','0');
INSERT INTO `logs` VALUES ('1499','cronjob','execute','0','1333098430','1333098432','0');
INSERT INTO `logs` VALUES ('1500','cronjob','execute','0','1333098460','1333098460','0');
INSERT INTO `logs` VALUES ('1501','cronjob','execute','0','1333098462','1333098462','0');
INSERT INTO `logs` VALUES ('1502','cronjob','execute','0','1333098463','1333098463','0');
INSERT INTO `logs` VALUES ('1503','cronjob','execute','0','1333098473','1333098473','0');
INSERT INTO `logs` VALUES ('1504','cronjob','execute','0','1333098475','1333098475','0');
INSERT INTO `logs` VALUES ('1505','cronjob','execute','0','1333098476','1333098476','0');
INSERT INTO `logs` VALUES ('1506','cronjob','execute','0','1333098839','1333098839','0');
INSERT INTO `logs` VALUES ('1507','cronjob','execute','0','1333098840','1333098840','0');
INSERT INTO `logs` VALUES ('1508','cronjob','execute','0','1333098841','1333098841','0');
INSERT INTO `logs` VALUES ('1509','cronjob','execute','0','1333098842','1333098842','0');
INSERT INTO `logs` VALUES ('1510','cronjob','execute','0','1333098843','1333098843','0');
INSERT INTO `logs` VALUES ('1511','cronjob','execute','0','1333098844','1333098844','0');
INSERT INTO `logs` VALUES ('1512','cronjob','execute','0','1333098854','1333098855','0');
INSERT INTO `logs` VALUES ('1513','cronjob','execute','0','1333098856','1333098857','0');
INSERT INTO `logs` VALUES ('1514','cronjob','execute','0','1333098859','1333098859','0');
INSERT INTO `logs` VALUES ('1515','cronjob','execute','0','1333098871','1333098871','0');
INSERT INTO `logs` VALUES ('1516','cronjob','execute','0','1333098877','1333098877','0');
INSERT INTO `logs` VALUES ('1517','cronjob','execute','0','1333098880','1333098880','0');
INSERT INTO `logs` VALUES ('1518','cronjob','execute','0','1333098884','1333098884','0');
INSERT INTO `logs` VALUES ('1519','cronjob','execute','0','1333098887','1333098887','0');
INSERT INTO `logs` VALUES ('1520','cronjob','execute','0','1333098890','1333098890','0');
INSERT INTO `logs` VALUES ('1521','cronjob','execute','0','1333098893','1333098893','0');
INSERT INTO `logs` VALUES ('1522','cronjob','execute','0','1333098895','1333098895','0');
INSERT INTO `logs` VALUES ('1523','cronjob','execute','0','1333098897','1333098897','0');
INSERT INTO `logs` VALUES ('1524','cronjob','execute','0','1333098899','1333098899','0');
INSERT INTO `logs` VALUES ('1525','cronjob','execute','0','1333098901','1333098901','0');
INSERT INTO `logs` VALUES ('1526','cronjob','execute','0','1333098907','1333098907','0');
INSERT INTO `logs` VALUES ('1527','cronjob','execute','0','1333098908','1333098908','0');
INSERT INTO `logs` VALUES ('1528','cronjob','execute','0','1333098910','1333098910','0');
INSERT INTO `logs` VALUES ('1529','cronjob','execute','0','1333098911','1333098911','0');
INSERT INTO `logs` VALUES ('1530','cronjob','execute','0','1333098932','1333098933','0');
INSERT INTO `logs` VALUES ('1531','cronjob','execute','0','1333098935','1333098935','0');
INSERT INTO `logs` VALUES ('1532','cronjob','execute','0','1333098936','1333098936','0');
INSERT INTO `logs` VALUES ('1533','cronjob','execute','0','1333098937','1333098937','0');
INSERT INTO `logs` VALUES ('1534','cronjob','execute','0','1333098938','1333098939','0');
INSERT INTO `logs` VALUES ('1535','cronjob','execute','0','1333099036','1333099036','0');
INSERT INTO `logs` VALUES ('1536','cronjob','execute','0','1333099038','1333099038','0');
INSERT INTO `logs` VALUES ('1537','cronjob','execute','0','1333099039','1333099039','0');
INSERT INTO `logs` VALUES ('1538','cronjob','execute','0','1333099040','1333099040','0');
INSERT INTO `logs` VALUES ('1539','cronjob','execute','0','1333099043','1333099044','0');
INSERT INTO `logs` VALUES ('1540','cronjob','execute','0','1333099045','1333099045','0');
INSERT INTO `logs` VALUES ('1541','cronjob','execute','0','1333099046','1333099046','0');
INSERT INTO `logs` VALUES ('1542','cronjob','execute','0','1333099047','1333099047','0');
INSERT INTO `logs` VALUES ('1543','cronjob','execute','0','1333099048','1333099048','0');
INSERT INTO `logs` VALUES ('1544','cronjob','execute','0','1333107343','1333107343','0');
INSERT INTO `logs` VALUES ('1545','cronjob','execute','0','1333113216','1333113216','0');
INSERT INTO `logs` VALUES ('1546','cronjob','execute','0','1333113218','1333113218','0');
INSERT INTO `logs` VALUES ('1547','cronjob','execute','0','1333113220','1333113220','0');
INSERT INTO `logs` VALUES ('1548','cronjob','execute','0','1333113262','1333113262','0');
INSERT INTO `logs` VALUES ('1549','cronjob','execute','0','1333113299','1333113299','0');
INSERT INTO `logs` VALUES ('1550','cronjob','execute','0','1333113301','1333113302','0');
INSERT INTO `logs` VALUES ('1551','cronjob','execute','0','1333374597','1333374597','0');

#---------------------------------------------------------------#
# Table: migrations, Columns: 3
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
`id` int(11) NOT NULL,
`file` varchar(100) default NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: migrations, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: newsletters, Columns: 2
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `newsletters`;
CREATE TABLE `newsletters` (
`id` smallint(5) NOT NULL,
`email` varchar(64) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE UNIQUE (`email`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: newsletters, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: sessions, Columns: 5
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
`id` int(11) unsigned NOT NULL,
`user_id` int(11) NOT NULL,
`session` varchar(32) default NULL,
`ip` varchar(15) default NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=89 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: sessions, Rows: 2
#---------------------------------------------------------------#
INSERT INTO `sessions` VALUES ('87','2','NULL','127.0.0.1','1331631292');
INSERT INTO `sessions` VALUES ('88','2','NULL','127.0.0.1','1331638927');

#---------------------------------------------------------------#
# Table: test_blogs, Columns: 11
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_blogs`;
CREATE TABLE `test_blogs` (
`id` smallint(5) NOT NULL,
`author_id` smallint(5) NOT NULL default '1',
`title` varchar(128) NOT NULL,
`teaser` varchar(140) default NULL,
`tags` varchar(128) default NULL,
`keywords` varchar(160) default NULL,
`content` text NOT NULL,
`language` varchar(2) NOT NULL default 'en',
`date` int(11) default NULL,
`date_modified` int(11) default NULL,
`published` tinyint(1) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_blogs, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_calendars, Columns: 7
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_calendars`;
CREATE TABLE `test_calendars` (
`id` int(11) unsigned NOT NULL,
`author_id` int(11) NOT NULL,
`title` varchar(128) NOT NULL,
`content` text default NULL,
`date` int(11) default NULL,
`start_date` date NOT NULL,
`end_date` date default NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_calendars, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_comments, Columns: 10
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_comments`;
CREATE TABLE `test_comments` (
`id` int(9) NOT NULL,
`parent_id` int(9) NOT NULL,
`parent_category` char(1) NOT NULL default 'b',
`author_id` smallint(5) NOT NULL,
`author_facebook_id` int(15) default NULL,
`author_name` varchar(32) default NULL,
`author_email` varchar(64) default NULL,
`author_ip` varchar(15) default NULL,
`content` text NOT NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE parentID (`parent_id`),
 UNIQUE parentCat (`parent_category`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_comments, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_contents, Columns: 8
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_contents`;
CREATE TABLE `test_contents` (
`id` int(11) NOT NULL,
`author_id` smallint(5) NOT NULL default '1',
`title` varchar(128) NOT NULL,
`teaser` varchar(140) default NULL,
`keywords` varchar(160) default NULL,
`content` text NOT NULL,
`date` int(11) NOT NULL,
`published` tinyint(1) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_contents, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_downloads, Columns: 9
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_downloads`;
CREATE TABLE `test_downloads` (
`id` int(11) unsigned NOT NULL,
`author_id` smallint(5) NOT NULL,
`title` varchar(128) NOT NULL,
`content` text default NULL,
`category` varchar(128) default NULL,
`file` varchar(64) NOT NULL,
`extension` varchar(4) NOT NULL,
`downloads` int(11) default NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_downloads, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_gallery_albums, Columns: 5
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_gallery_albums`;
CREATE TABLE `test_gallery_albums` (
`id` smallint(5) NOT NULL,
`author_id` smallint(5) NOT NULL,
`title` varchar(50) NOT NULL,
`content` varchar(100) default NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_gallery_albums, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_gallery_files, Columns: 7
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_gallery_files`;
CREATE TABLE `test_gallery_files` (
`id` int(9) NOT NULL,
`author_id` smallint(5) NOT NULL,
`album_id` smallint(5) NOT NULL,
`date` int(11) NOT NULL,
`file` varchar(64) NOT NULL,
`extension` varchar(4) NOT NULL default 'jpg',
`content` varchar(100) default NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_gallery_files, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_logs, Columns: 7
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_logs`;
CREATE TABLE `test_logs` (
`id` int(11) NOT NULL,
`section_name` varchar(32) NOT NULL default 'NOT NULL',
`action_name` varchar(16) NOT NULL,
`action_id` smallint(6) default NULL,
`time_start` int(11) default NULL,
`time_end` int(11) default NULL,
`user_id` smallint(6) default NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_logs, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_migrations, Columns: 3
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_migrations`;
CREATE TABLE `test_migrations` (
`id` int(11) NOT NULL,
`file` varchar(100) default NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_migrations, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_projects, Columns: 13
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_projects`;
CREATE TABLE `test_projects` (
`id` int(11) NOT NULL,
`title` varchar(128) NOT NULL,
`teaser` varchar(140) default NULL,
`content` text NOT NULL,
`date` int(11) default NULL,
`client` varchar(128) default NULL,
`designer` varchar(128) default NULL,
`tasks` varchar(128) default NULL,
`tools` varchar(128) default NULL,
`url` varchar(128) default NULL,
`project_type` varchar(12) NOT NULL,
`published` tinyint(1) NOT NULL,
`start_page` tinyint(1) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_projects, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_sessions, Columns: 5
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_sessions`;
CREATE TABLE `test_sessions` (
`id` int(11) unsigned NOT NULL,
`user_id` int(11) NOT NULL,
`session` varchar(32) default NULL,
`ip` varchar(15) default NULL,
`date` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_sessions, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: test_users, Columns: 12
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `test_users`;
CREATE TABLE `test_users` (
`id` int(9) NOT NULL,
`name` varchar(32) NOT NULL,
`surname` varchar(32) default NULL,
`password` varchar(32) NOT NULL,
`email` varchar(64) NOT NULL,
`role` tinyint(1) NOT NULL default '1',
`date` int(11) default NULL,
`verification_code` varchar(12) default NULL,
`receive_newsletter` tinyint(1) default NULL,
`content` varchar(1000) default NULL,
`use_gravatar` tinyint(1) default NULL,
`api_token` varchar(32) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE email (`email`),
 UNIQUE name (`name`),
 UNIQUE userright (`role`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: test_users, Rows: 0
#---------------------------------------------------------------#

#---------------------------------------------------------------#
# Table: users, Columns: 12
#---------------------------------------------------------------#
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
`id` int(11) NOT NULL,
`name` varchar(32) NOT NULL,
`surname` varchar(32) default NULL,
`password` varchar(32) NOT NULL,
`email` varchar(64) NOT NULL,
`content` varchar(1000) default NULL,
`receive_newsletter` tinyint(1) NOT NULL default '1',
`use_gravatar` tinyint(1) default NULL,
`role` tinyint(1) NOT NULL default '1',
`date` int(11) default NULL,
`verification_code` varchar(12) default NULL,
`api_token` varchar(32) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE email (`email`),
 UNIQUE api_token (`api_token`)
) AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#---------------------------------------------------------------#
# Data: users, Rows: 1
#---------------------------------------------------------------#
INSERT INTO `users` VALUES ('2','Administrator','c2f9619961','098f6bcd4621d373cade4e832627b4f6','admin@example.com','','1','0','4','','','');

