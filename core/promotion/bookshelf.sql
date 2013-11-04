ALTER TABLE `useraccount` ADD COLUMN `libraryId` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `personId`;
insert into `role`(`id`, `name`,`active`, `created`, `createdById`, `updated`, `updatedById`) values 
(1, 'Guest', 1, NOW(), 100, NOW(), 100),
(2, 'Reader', 1, NOW(), 100, NOW(), 100);

insert into `person`(`id`, `firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (1, 'test', 'user', 1, NOW(), 100, NOW(), 100);
insert into `useraccount`(`id`, `username`, `password`, `personId`, `libraryId`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (1, 'test_user', sha1('test_pass'), '1', '37', 1, NOW(), 100, NOW(), 100);
insert into `role_useraccount` (`roleId`, `userAccountId`, `created`, `createdById`) values (2, 1, NOW(), 100);




DROP TABLE IF EXISTS `productshelfitem`;
CREATE TABLE `productshelfitem` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`productId` int(10) unsigned NOT NULL DEFAULT 0,
	`ownerId` int(10) unsigned NOT NULL DEFAULT 0,
	`status` int(1) unsigned NOT NULL DEFAULT 0,
	`borrowTime` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`productId`)
	,INDEX (`ownerId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`status`)
	,INDEX (`borrowTime`)
) ENGINE=innodb DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `library`;
CREATE TABLE `library` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
insert into `library` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
(37, 'test lib', 1, NOW(), 100, NOW(), 100);

DROP TABLE IF EXISTS `libraryinfo`;
CREATE TABLE `libraryinfo` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`libraryId` int(10) unsigned NOT NULL DEFAULT 0,
	`typeId` int(10) unsigned NOT NULL DEFAULT 0,
	`value` varchar(255) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`libraryId`)
	,INDEX (`typeId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('37', '1', '8985A41E813AE00A78EE4AACF606F643', 1, NOW(), 100, NOW(), 100);

DROP TABLE IF EXISTS `libraryinfotype`;
CREATE TABLE `libraryinfotype` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT '',
	`code` varchar(50) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`name`)
	,UNIQUE INDEX (`code`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
insert into libraryinfotype (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
(1, 'The secret key', 'regno',  1, NOW(), 100, NOW(), 100);