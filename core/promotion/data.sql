ALTER TABLE `role` AUTO_INCREMENT = 10;
insert into `role`(`name`,`active`, `created`, `createdById`, `updated`, `updatedById`) values ('admin', 1, NOW(), 100, NOW(), 100);

insert into `person`(`id`, `firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (10, 'guest', 'user', 1, NOW(), 100, NOW(), 100);
ALTER TABLE `person` AUTO_INCREMENT = 100;
insert into `person`(`firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) values ('admin', 'system', 1, NOW(), 100, NOW(), 100);

insert into `useraccount`(`id`, `username`, `password`, `personId`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (10, md5('guestusername'), 'disabled', '10', 1, NOW(), 100, NOW(), 100);
ALTER TABLE `useraccount` AUTO_INCREMENT = 100;
insert into `useraccount`(`username`, `password`, `personId`, `active`, `created`, `createdById`, `updated`, `updatedById`) values ('admin', sha1('admin'), '100', 1, NOW(), 100, NOW(), 100);

insert into `role_useraccount` (`roleId`, `userAccountId`, `created`, `createdById`) values (10, 100, NOW(), 100);

insert into `productattributetype` (`name`, `code`, `searchable`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
('Author', 'author', 1, 1, NOW(), 100, NOW(), 100),
('ISBN', 'isbn', 1, 1, NOW(), 100, NOW(), 100),
('Publisher', 'publisher', 1, 1, NOW(), 100, NOW(), 100),
('PublishDate', 'publish_date', 1, 1, NOW(), 100, NOW(), 100),
('Number Of Words', 'no_of_words', 1, 1, NOW(), 100, NOW(), 100),
('Image', 'image', 1, 1, NOW(), 100, NOW(), 100),
('ImageThumbnail', 'image_thumb', 1, 1, NOW(), 100, NOW(), 100),
('Description', 'description', 1, 1, NOW(), 100, NOW(), 100),
('Cno', 'cno', 1, 1, NOW(), 100, NOW(), 100);

insert into `language` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
(1, '简体',  1, NOW(), 100, NOW(), 100),
(2, '繁體',  1, NOW(), 100, NOW(), 100);

insert into `producttype` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
(1, 'book',  1, NOW(), 100, NOW(), 100),
(2, 'newspaper',  1, NOW(), 100, NOW(), 100),
(3, 'magzine',  1, NOW(), 100, NOW(), 100);

insert into `productstaticstype` (`name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
('Click Rate', 'no_of_clicks',  1, NOW(), 100, NOW(), 100),
('Borrow Rate', 'no_of_borrows',  1, NOW(), 100, NOW(), 100);

insert into `supplierinfotype` (`id`,`name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
(1, 'The URL to import', 'import_url',  1, NOW(), 100, NOW(), 100),
(2, 'The username to import', 'import_username',  1, NOW(), 100, NOW(), 100),
(3, 'The password to import', 'import_password',  1, NOW(), 100, NOW(), 100),
(4, 'The URL to view online', 'view_url',  1, NOW(), 100, NOW(), 100),
(5, 'The URL to download', 'download_url',  1, NOW(), 100, NOW(), 100);

insert into `supplier` (`id`, `name`, `supplierLocation`,`scheduledTime`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
(1, 'Xin Hua', 'http://xhestore.com', '', 1, NOW(), 100, NOW(), 100);

insert into `supplierinfo` (`supplierId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('1', '1', 'http://sh.xhestore.com/ReadBook.aspx?uid={uid}&pwd={pass}&bookid={bookid}&errorurl={errUrl}', 1, NOW(), 100, NOW(), 100),
    ('1', '2', 'test_username', 1, NOW(), 100, NOW(), 100),
    ('1', '3', 'test_password', 1, NOW(), 100, NOW(), 100),
    ('1', '4', 'http://sh.xhestore.com/ReadBook.aspx?uid={uid}&pwd={pass}&bookid={bookid}&errorurl={errUrl}', 1, NOW(), 100, NOW(), 100),
    ('1', '5', 'http://sh.xhestore.com/ReadBook.aspx?uid={uid}&pwd={pass}&bookid={bookid}&errorurl={errUrl}', 1, NOW(), 100, NOW(), 100);
