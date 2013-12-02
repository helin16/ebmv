ALTER TABLE `useraccount` ADD COLUMN `libraryId` INT(10) UNSIGNED NOT NULL DEFAULT 0 AFTER `personId`;
update `useraccount` set `libraryId` = 1 where id in (1, 10, 100);
ALTER TABLE  `language` ADD  `code` VARCHAR( 10 ) NOT NULL DEFAULT  '' AFTER  `name`;
update `language` set `code`='zh_CN' where id = 1;
update `language` set `code`='zh_TW' where id = 2;
ALTER TABLE  `language` ADD UNIQUE  `code` (  `code` ) COMMENT  '';
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
