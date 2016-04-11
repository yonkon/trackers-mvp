CREATE TABLE IF NOT EXISTS `time_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - new/stopped, 1 - started, -1 - deleted',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `position` int(10) unsigned DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `cost` int(11) DEFAULT NULL,
  `cost_type` int(11) NOT NULL DEFAULT '0' COMMENT '0 - per project, 1 - per hour, 2 - per day, 3 - per week, 4 -per month',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
