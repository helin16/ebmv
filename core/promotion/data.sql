ALTER TABLE `role` AUTO_INCREMENT = 10;
insert into `role`(`name`,`active`, `created`, `createdById`, `updated`, `updatedById`) values ('admin', 1, NOW(), 100, NOW(), 100);

insert into `person`(`id`, `firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (10, 'guest', 'user', 1, NOW(), 100, NOW(), 100);
ALTER TABLE `person` AUTO_INCREMENT = 100;
insert into `person`(`firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) values ('admin', 'system', 1, NOW(), 100, NOW(), 100);

insert into `useraccount`(`id`, `username`, `password`, `personId`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (10, md5('guestusername'), 'disabled', '10', 1, NOW(), 100, NOW(), 100);
ALTER TABLE `useraccount` AUTO_INCREMENT = 100;
insert into `useraccount`(`username`, `password`, `personId`, `active`, `created`, `createdById`, `updated`, `updatedById`) values ('admin', md5('admin'), '100', 1, NOW(), 100, NOW(), 100);

insert into `role_useraccount` (`roleId`, `userAccountId`, `created`, `createdById`) values (10, 100, NOW(), 100);

insert into `productattributetype` (`name`, `code`, `searchable`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
('Author', 'author', 1, 1, NOW(), 100, NOW(), 100),
('ISBN', 'isbn', 1, 1, NOW(), 100, NOW(), 100),
('Publisher', 'publisher', 1, 1, NOW(), 100, NOW(), 100),
('PublishDate', 'publish_date', 1, 1, NOW(), 100, NOW(), 100),
('Number Of Words', 'no_of_words', 1, 1, NOW(), 100, NOW(), 100),
('Image', 'image', 1, 1, NOW(), 100, NOW(), 100),
('ImageThumbnail', 'image_thumb', 1, 1, NOW(), 100, NOW(), 100),
('Description', 'description', 1, 1, NOW(), 100, NOW(), 100);