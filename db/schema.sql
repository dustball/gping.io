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
  `password` CHAR(126) NOT NULL UNIQUE KEY -- bcrypt hashed!
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_devices` (
  `device_id` CHAR(12) PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL REFERENCES `login_data`(`id`) MATCH SIMPLE --,
  --`device_type_id` INT NOT NULL REFERENCES `device_type`(`device_type_id`) MATCH SIMPLE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- do we care about this?
CREATE TABLE `device_type` (
  `device_type_id` INT PRIMARY KEY,
  `name` VARCHAR(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `device_type`(`device_type_id`, `name`) VALUES
  (1, 'vehicle');

-- contains reported location data for a user
CREATE TABLE `location` (
  `user_id` BIGINT UNSIGNED KEY NOT NULL REFERENCES `login_data`(`id`), -- who owns this data point
  `device_id` CHAR(12) KEY NOT NULL REFERENCES `user_devices`(`device_id`), -- which device submitted it
  `location_type_id` INT NOT NULL REFERENCES ` -- what kind of data is this
  `time` datetime NOT NULL, -- when was it submitted
  `lat` decimal(11,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `accuracy` float DEFAULT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- how was the data point determined?
CREATE TABLE `location_type` (
  `location_type_id` INT NOT NULL PRIMARY KEY,
  `name` VARCHAR(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `location_type` VALUES
  (1, 'gps'), -- uses gps device
  (2, 'cell'), -- rough data from cell tower
  (3, 'network'); -- inferred from seen AP data

CREATE TABLE `vehicle_data` (
  `user_id` BIGINT UNSIGNED KEY NOT NULL REFERENCES `login_data`(`id`), -- who owns this vehicle data
  `device_id` CHAR(12) KEY NOT NULL REFERENCES `user_devices`(`device_id`), -- which device submitted it
)

CREATE TABLE `vehicle_data` (
  `device_id` char(12) DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
