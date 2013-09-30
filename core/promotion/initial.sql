DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `key` varchar(32) NOT NULL DEFAULT '',
    `data` longtext NOT NULL ,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(4) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(4) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(20) NOT NULL DEFAULT '',
    `password` varchar(32) NOT NULL DEFAULT '',
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(4) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(4) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `firstName` varchar(50) NOT NULL DEFAULT '',
    `lastName` varchar(50) NOT NULL DEFAULT '',
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(4) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(4) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `key` varchar(32) NOT NULL DEFAULT '',
    `data` longtext NOT NULL ,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(4) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(4) unsigned NOT NULL DEFAULT 0,
    `rootId` int(4) unsigned NOT NULL DEFAULT 0,
    `parentId` int(4) unsigned NOT NULL DEFAULT 0,
    `position` varchar(255) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`rootId`)
    ,INDEX (`parentId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `key` varchar(32) NOT NULL DEFAULT '',
    `data` longtext NOT NULL ,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(4) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(4) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
) ENGINE=innodb DEFAULT CHARSET=utf8;