ALTER TABLE `language` ADD UNIQUE INDEX `code`(`code`);
ALTER TABLE `producttype` DROP INDEX `name`, ADD UNIQUE INDEX `name` USING BTREE(`name`);

update `language` set `code` = 'zh-CN' where id = 1;
update `language` set `code` = 'zh-TW' where id = 2;
