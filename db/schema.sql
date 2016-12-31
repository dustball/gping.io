-- v0

CREATE TABLE `gping` (
  `id` char(12) DEFAULT NULL,
  `t` datetime DEFAULT NULL,
  `voltage` float DEFAULT NULL,
  `ver` int(11) DEFAULT NULL,
  `bat_status` char(1) DEFAULT NULL,
  `bat_charge` char(1) DEFAULT NULL,
  `bat_percent` float DEFAULT NULL,
  `odbs` text,
  `uptime_phone` int(11) DEFAULT NULL,
  `uptime_app` int(11) DEFAULT NULL,
  `fleetid` varchar(12) DEFAULT NULL,
  `account` varchar(128) DEFAULT NULL,
  `aid` varchar(20) DEFAULT NULL,
  `locked` char(1) DEFAULT NULL,
  KEY `gpi` (`id`,`t`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `gping_nloc` (
  `id` char(12) DEFAULT NULL,
  `t` datetime DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `lat` decimal(11,8) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `accuracy` float DEFAULT NULL,
  KEY `gpi` (`id`,`t`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `gping_gloc` (
  `id` char(12) DEFAULT NULL,
  `t` datetime DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `lat` decimal(11,8) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `accuracy` float DEFAULT NULL,
  KEY `gpi` (`id`,`t`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- v1

CREATE TABLE `login_data` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` CHAR(128) NOT NULL UNIQUE KEY,
  `password` CHAR(126) NOT NULL UNIQUE KEY
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_devices` (
  `device_id` CHAR(12) PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL REFERENCES `login_data`(`id`) MATCH SIMPLE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
