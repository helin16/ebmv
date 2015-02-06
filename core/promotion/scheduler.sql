DROP TABLE IF EXISTS `process`;
CREATE TABLE `process` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`taskId` int(10) unsigned NOT NULL DEFAULT 0,
	`processId` INT(10) unsigned NOT NULL DEFAULT 0,
	`error` INT(10) unsigned NOT NULL DEFAULT 0,
	`start` DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00',
	`end` DATETIME NOT NULL DEFAULT '0001-01-01 00:00:00',
	`lifespan` INT(255) unsigned NOT NULL DEFAULT 0,
	`comments` VARCHAR(255) NOT NULL DEFAULT '',
	`type` VARCHAR(50) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`taskId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`processId`)
	,INDEX (`error`)
	,INDEX (`start`)
	,INDEX (`lifespan`)
) ENGINE=innodb DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL DEFAULT '',
	`path` VARCHAR(255) NOT NULL DEFAULT '',
	`done` bool NOT NULL DEFAULT 0,
	`retry` int(10) unsigned NOT NULL DEFAULT 0,
	`comments` VARCHAR(255) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`path`)
	,INDEX (`done`)
	,INDEX (`retry`)
) ENGINE=innodb DEFAULT CHARSET=utf8;