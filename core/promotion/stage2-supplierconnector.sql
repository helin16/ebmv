update supplier set connector='SC_XinHua' where id = 1;

ALTER TABLE `bmv`.`supplier` CHANGE COLUMN `supplierLocation` `connector` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
 DROP INDEX `supplierLocation`,
 ADD INDEX `supplierLocation` USING BTREE(`connector`);