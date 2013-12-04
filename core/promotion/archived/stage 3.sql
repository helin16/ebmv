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
(1, 'test lib', 1, NOW(), 100, NOW(), 100);

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
    ('1', '1', '37', 1, NOW(), 100, NOW(), 100);

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
(1, 'The Australian Library Code', 'aus_code',  1, NOW(), 100, NOW(), 100);

#added library url type
insert into libraryinfotype (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(2, 'The url of the library', 'lib_url',  1, NOW(), 100, NOW(), 100);
insert into libraryinfotype (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(3, 'The timezone of the library', 'lib_timezone',  1, NOW(), 100, NOW(), 100);
insert into libraryinfotype (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(4, 'The theme of the library', 'lib_theme',  1, NOW(), 100, NOW(), 100);

#added url info for ebmv.com.au
insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('1', '2', 'ebmv.com.au', 1, NOW(), 100, NOW(), 100);
    
#added url info for www.ebmv.com.au
insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('1', '2', 'www.ebmv.com.au', 1, NOW(), 100, NOW(), 100);
#added the timezone  
insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('1', '3', 'Australia/Melbourne', 1, NOW(), 100, NOW(), 100);
#added the theme for lib  
insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('1', '4', 'default', 1, NOW(), 100, NOW(), 100);

#added testing user
insert into `person`(`id`, `firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (2, 'test user', 'YL', 1, NOW(), 100, NOW(), 100);
insert into `useraccount`(`id`, `username`, `password`, `personId`, `libraryId`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (2, 'testuser_yl', sha1('testpass_yl'), '2', '1', 1, NOW(), 100, NOW(), 100);
insert into `role_useraccount` (`roleId`, `userAccountId`, `created`, `createdById`) values (2, 2, NOW(), 100);