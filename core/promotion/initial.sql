-- Setting Up Database
DROP TABLE IF EXISTS `asset`;
CREATE TABLE `asset` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `assetId` varchar(32) NOT NULL DEFAULT '',
    `filename` varchar(100) NOT NULL DEFAULT '',
    `mimeType` varchar(50) NOT NULL DEFAULT '',
    `path` varchar(200) NOT NULL DEFAULT '',
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,UNIQUE INDEX (`assetId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `productstatics`;
CREATE TABLE `productstatics` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `productId` int(10) unsigned NOT NULL DEFAULT 0,
    `value` int(100) unsigned NOT NULL DEFAULT 0,
    `typeId` int(10) unsigned NOT NULL DEFAULT 0,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`productId`)
    ,INDEX (`typeId`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`value`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `productstaticstype`;
CREATE TABLE `productstaticstype` (
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
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT '',
    `rootId` int(10) unsigned NULL DEFAULT NULL,
    `parentId` int(10) unsigned NULL DEFAULT NULL,
    `position` varchar(255) NOT NULL DEFAULT '1',
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`rootId`)
    ,INDEX (`parentId`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL DEFAULT '',
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
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(200) NOT NULL DEFAULT '',
    `suk` varchar(50) NOT NULL DEFAULT '',
    `languageId` int(10) unsigned NOT NULL DEFAULT 0,
    `productTypeId` int(10) unsigned NOT NULL DEFAULT 0,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`languageId`)
    ,INDEX (`productTypeId`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`title`)
    ,INDEX (`suk`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `category_product`;
CREATE TABLE `category_product` (
    `categoryId` int(10) unsigned NOT NULL,
    `productId` int(10) unsigned NOT NULL,
    `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    `createdById` int(10) unsigned NOT NULL,
    UNIQUE KEY `uniq_category_product` (`categoryId`,`productId`),
    KEY `idx_category_product_categoryId` (`categoryId`),
    KEY `idx_category_product_productId` (`productId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `productattribute`;
CREATE TABLE `productattribute` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `productId` int(10) unsigned NOT NULL DEFAULT 0,
    `attribute` varchar(500) NOT NULL DEFAULT '',
    `typeId` int(10) unsigned NOT NULL DEFAULT 0,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`productId`)
    ,INDEX (`typeId`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`attribute`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `productattributetype`;
CREATE TABLE `productattributetype` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL DEFAULT '',
    `code` varchar(50) NOT NULL DEFAULT '',
    `searchable` bool NOT NULL DEFAULT 0,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`name`)
    ,INDEX (`searchable`)
    ,UNIQUE INDEX (`code`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `producttype`;
CREATE TABLE `producttype` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL DEFAULT '',
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
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL DEFAULT '',
    `supplierLocation` varchar(200) NOT NULL DEFAULT '',
    `scheduledTime` varchar(200) NOT NULL DEFAULT '',
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`name`)
    ,INDEX (`supplierLocation`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `supplierinfo`;
CREATE TABLE `supplierinfo` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `supplierId` int(10) unsigned NOT NULL DEFAULT 0,
    `typeId` int(10) unsigned NOT NULL DEFAULT 0,
    `value` varchar(255) NOT NULL DEFAULT '',
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`supplierId`)
    ,INDEX (`typeId`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `supplierinfotype`;
CREATE TABLE `supplierinfotype` (
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
DROP TABLE IF EXISTS `supplierprice`;
CREATE TABLE `supplierprice` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `productId` int(10) unsigned NOT NULL DEFAULT 0,
    `supplierId` int(10) unsigned NOT NULL DEFAULT 0,
    `price` double(10,4) unsigned NOT NULL DEFAULT 0,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`productId`)
    ,INDEX (`supplierId`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`price`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `firstName` varchar(50) NOT NULL DEFAULT '',
    `lastName` varchar(50) NOT NULL DEFAULT '',
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`firstName`)
    ,INDEX (`lastName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL DEFAULT '',
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,UNIQUE INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `key` varchar(32) NOT NULL DEFAULT '',
    `data` longtext NOT NULL ,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,UNIQUE INDEX (`key`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `useraccount`;
CREATE TABLE `useraccount` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `username` varchar(100) NOT NULL DEFAULT '',
    `password` varchar(40) NOT NULL DEFAULT '',
    `personId` int(10) unsigned NOT NULL DEFAULT 0,
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`personId`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`password`)
    ,UNIQUE INDEX (`username`)
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

-- Completed CRUD Setup.