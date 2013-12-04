DROP TABLE IF EXISTS `languagecode`;
CREATE TABLE `languagecode` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`code` varchar(50) NOT NULL DEFAULT '',
	`languageId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`languageId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,UNIQUE INDEX (`code`)
) ENGINE=innodb DEFAULT CHARSET=utf8;

insert into `languagecode`(`languageId`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, 'zh-CN', 1, NOW(), 100, 1, 100),
	(2, 'zh-hk', 1, NOW(), 100, 1, 100),
	(2, 'zh-tw', 1, NOW(), 100, 1, 100),
	(3, 'en-us', 1, NOW(), 100, 1, 100);
ALTER TABLE `language` DROP COLUMN `code`;