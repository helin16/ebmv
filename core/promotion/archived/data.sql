############################ add role table
ALTER TABLE `role` AUTO_INCREMENT = 10;
insert into `role`(`id`, `name`,`active`, `created`, `createdById`, `updated`, `updatedById`) values 
	(1, 'Guest', 1, NOW(), 100, NOW(), 100),
	(2, 'Reader', 1, NOW(), 100, NOW(), 100),
	(10, 'Admin', 1, NOW(), 100, NOW(), 100);

############################ add person table
ALTER TABLE `person` AUTO_INCREMENT = 100;
insert into `person`(`id`, `firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, 'guest', 'user', 1, NOW(), 100, NOW(), 100),
	(10, 'test', 'user', 1, NOW(), 100, NOW(), 100),
	(11, 'test', 'YL user', 1, NOW(), 100, NOW(), 100),
	(100, 'admin', 'system', 1, NOW(), 100, NOW(), 100);


############################ add user table
ALTER TABLE `useraccount` AUTO_INCREMENT = 100;
insert into `useraccount`(`id`, `username`, `password`, `personId`, `libraryId`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, md5('guestusername'), 'disabled', 1, 1, 1, NOW(), 100, NOW(), 100),
	(10, 'test_user', sha1('test_pass'), 10, 1, 1, NOW(), 100, NOW(), 100),
	(11, 'testuser_yl', sha1('testpass_yl'), 11, 1, 1, NOW(), 100, NOW(), 100),
	(100, 'admin', sha1('admin'), '100', 1, 1, NOW(), 100, NOW(), 100);

############################ add role_useraccount table
insert into `role_useraccount`(`userAccountId`, `roleId`, `created`, `createdById`) values 
	(1, 1, NOW(), 100),
	(10, 2, NOW(), 100),
	(11, 2, NOW(), 100),
	(100, 10, NOW(), 100);
	
############################ add language table
insert into `language` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, '简体中文',  1, NOW(), 100, NOW(), 100),
	(2, '繁体中文',  1, NOW(), 100, NOW(), 100),
	(3, 'English',  1, NOW(), 100, NOW(), 100);
	
############################ add languagecode table
insert into `languagecode`(`languageId`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, 'zh-CN', 1, NOW(), 100, 1, 100),
	(1, 'zh_CN', 1, NOW(), 100, 1, 100),
	(2, 'zh-hk', 1, NOW(), 100, 1, 100),
	(2, 'zh_hk', 1, NOW(), 100, 1, 100),
	(2, 'zh-tw', 1, NOW(), 100, 1, 100),
	(2, 'zh_tw', 1, NOW(), 100, 1, 100),
	(3, 'en_us', 1, NOW(), 100, 1, 100),
	(3, 'en-us', 1, NOW(), 100, 1, 100);
	
############################ add productattributetype table
insert into `productattributetype` (`name`, `code`, `searchable`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	('Author', 'author', 1, 1, NOW(), 100, NOW(), 100),
	('ISBN', 'isbn', 1, 1, NOW(), 100, NOW(), 100),
	('Publisher', 'publisher', 1, 1, NOW(), 100, NOW(), 100),
	('PublishDate', 'publish_date', 1, 1, NOW(), 100, NOW(), 100),
	('Number Of Words', 'no_of_words', 1, 1, NOW(), 100, NOW(), 100),
	('Image', 'image', 1, 1, NOW(), 100, NOW(), 100),
	('ImageThumbnail', 'image_thumb', 1, 1, NOW(), 100, NOW(), 100),
	('Description', 'description', 1, 1, NOW(), 100, NOW(), 100),
	('Cno', 'cno', 1, 1, NOW(), 100, NOW(), 100),
	('Cip', 'cip', 1, 1, NOW(), 100, NOW(), 100);

############################ add producttype table
insert into `producttype` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, 'book',  1, NOW(), 100, NOW(), 100),
	(2, 'newspaper',  1, NOW(), 100, NOW(), 100),
	(3, 'magazine',  1, NOW(), 100, NOW(), 100);

############################ add productstaticstype table
insert into `productstaticstype` (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, 'Click Rate', 'no_of_clicks',  1, NOW(), 100, NOW(), 100),
	(2, 'Borrow Rate', 'no_of_borrows',  1, NOW(), 100, NOW(), 100);

############################ add productstaticstype table
insert into `supplierinfotype` (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
(1, 'The URL to import', 'import_url',  1, NOW(), 100, NOW(), 100),
(2, 'The URL to view online', 'view_url',  1, NOW(), 100, NOW(), 100),
(3, 'The URL to download', 'download_url',  1, NOW(), 100, NOW(), 100),
(4, 'Default Language ID', 'default_lang_id',  1, NOW(), 100, NOW(), 100),
(5, 'Default Type ID', 'default_product_type_id',  1, NOW(), 100, NOW(), 100),
(6, 'Default Image Directory for products', 'default_img_dir',  1, NOW(), 100, NOW(), 100),
(7, 'Supplier Key', 'skey',  1, NOW(), 100, NOW(), 100),
(8, 'Supplied Product Type Ids', 'stype_ids',  1, NOW(), 100, NOW(), 100);

############################ add supplier table
insert into `supplier` (`id`, `name`, `connector`,`active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, 'Xin Hua', 'SC_XinHua', 1, NOW(), 100, NOW(), 100),
	(2, 'Tai Wan', 'SC_TW', 1, NOW(), 100, NOW(), 100);

############################ add supplierinfo table
insert into `supplierinfo` (`supplierId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('1', 1, 'http://au.xhestore.com/AULibService.asmx?wsdl', 1, NOW(), 100, NOW(), 100),
    ('1', 2, 'http://au.xhestore.com/book/readbook', 1, NOW(), 100, NOW(), 100),
    ('1', 3, 'http://au.xhestore.com/book/downloadbook', 1, NOW(), 100, NOW(), 100),
    ('1', 4, '1', 1, NOW(), 100, NOW(), 100),
    ('1', 5, '1', 1, NOW(), 100, NOW(), 100),
    ('1', 6, '/var/www/html/protected/asset/supplier1/', 1, NOW(), 100, NOW(), 100),
    ('1', 7, '8985A41E813AE00A78EE4AACF606F643', 1, NOW(), 100, NOW(), 100),
    ('1', 8, '1', 1, NOW(), 100, NOW(), 100),
    
    (2, 1, 'http://m2.ebook4rent.tw/pont/1.00/{SiteID}/SyncBooks/', 1, NOW(), 100, NOW(), 100),
    (2, 4, 2, 1, NOW(), 100, NOW(), 100),
    (2, 5, '1', 1, NOW(), 100, NOW(), 100),
    (2, 6, '/var/www/html/protected/asset/supplier2/', 1, NOW(), 100, NOW(), 100),
    (2, 8, '1,3', 1, NOW(), 100, NOW(), 100);

############################ add library table
insert into `library` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, 'test lib', 1, NOW(), 100, NOW(), 100);

############################ add libraryinfotype table
insert into `libraryinfotype` (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	(1, 'The Australian Library Code', 'aus_code',  1, NOW(), 100, NOW(), 100),
	(2, 'The url of the library', 'lib_url',  1, NOW(), 100, NOW(), 100),
	(3, 'The timezone of the library', 'lib_timezone',  1, NOW(), 100, NOW(), 100),
	(4, 'The theme of the library', 'lib_theme',  1, NOW(), 100, NOW(), 100);
	
############################ add libraryinfo table
insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('1', '1', '37', 1, NOW(), 100, NOW(), 100),
    ('1', '2', 'localhost', 1, NOW(), 100, NOW(), 100),
    ('1', '2', 'ebmv.com.au', 1, NOW(), 100, NOW(), 100),
    ('1', '2', 'www.ebmv.com.au', 1, NOW(), 100, NOW(), 100),
    ('1', '3', 'Australia/Melbourne', 1, NOW(), 100, NOW(), 100),
    ('1', '4', 'default', 1, NOW(), 100, NOW(), 100);


