-- Setting Up Database
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
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL DEFAULT '',
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
DROP TABLE IF EXISTS `useraccount`;
CREATE TABLE `useraccount` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(100) NOT NULL DEFAULT '',
    `password` varchar(40) NOT NULL DEFAULT '',
    `personId` int(4) unsigned NOT NULL DEFAULT 0,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(4) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(4) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`personId`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`username`)
    ,INDEX (`password`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `role_useraccount`;
CREATE TABLE `role_useraccount` (
    `roleId` int(10) unsigned NOT NULL,
    `useraccountId` int(10) unsigned NOT NULL,
    `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    `createdById` int(10) unsigned NOT NULL,
    UNIQUE KEY `uniq_role_useraccount` (`roleId`,`useraccountId`),
    KEY `idx_role_useraccount_roleId` (`roleId`),
    KEY `idx_role_useraccount_useraccountId` (`useraccountId`)
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

-- Completed CRUD Setup.