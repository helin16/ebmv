DROP TABLE IF EXISTS `language_product`;
CREATE TABLE `language_product` (
	`languageId` int(10) unsigned NOT NULL,
	`productId` int(10) unsigned NOT NULL,
	`created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	`createdById` int(10) unsigned NOT NULL,
	UNIQUE KEY `uniq_language_product` (`languageId`,`productId`),
	KEY `idx_language_product_languageId` (`languageId`),
	KEY `idx_language_product_productId` (`productId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;

ALTER TABLE `product` DROP COLUMN `languageId`, DROP INDEX `languageId`;