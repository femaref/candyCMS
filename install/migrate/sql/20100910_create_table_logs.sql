CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(32) NOT NULL DEFAULT 'NOT NULL',
  `action_name` varchar(16) NOT NULL,
  `action_id` smallint(6) DEFAULT NULL,
  `time_start` int(11) DEFAULT NULL,
  `time_end` int(11) DEFAULT NULL,
  `user_id` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;