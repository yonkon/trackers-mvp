CREATE TABLE IF NOT EXISTS `time_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_project_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end` timestamp NULL DEFAULT NULL,
  `seconds` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `time_project_id` (`time_project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
