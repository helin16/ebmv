ALTER TABLE `producttype` DROP INDEX `name`, ADD UNIQUE INDEX `name` USING BTREE(`name`);

update `language` set `code` = 'zh-CN' where id = 1;
update `language` set `code` = 'zh-TW' where id = 2;

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


ALTER TABLE `supplier` CHANGE COLUMN `supplierLocation` `connector` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
 DROP INDEX `supplierLocation`,
 ADD INDEX `supplierLocation` USING BTREE(`connector`);
 
update supplier set connector='SC_XinHua' where id = 1;

insert into language(`name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values ('US English', 'en-us', '1', NOW(), 100, NOW(), 100);
