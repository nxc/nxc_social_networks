DROP TABLE IF EXISTS `nxc_social_network_tokens`;
CREATE TABLE `nxc_social_network_tokens` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `type` tinyint(3) unsigned NOT NULL,
  `token` TEXT NOT NULL,
  `secret` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `nxc_social_network_publish_handlers`;
CREATE TABLE `nxc_social_network_publish_handlers` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `workflow_event_id` int(11) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `classattribute_ids_serialized` TEXT NULL,
  `options_serialized` TEXT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;