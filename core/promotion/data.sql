ALTER TABLE `role` AUTO_INCREMENT = 10;
insert into `role`(`name`,`active`, `created`, `createdById`, `updated`, `updatedById`) values ('admin', 1, NOW(), 100, NOW(), 100);

ALTER TABLE `person` AUTO_INCREMENT = 10;
insert into `person`(`firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) values ('admin', 'system', 1, NOW(), 100, NOW(), 100);

ALTER TABLE `useraccount` AUTO_INCREMENT = 100;
insert into `useraccount`(`username`, `password`, `personId`, `active`, `created`, `createdById`, `updated`, `updatedById`) values ('admin', md5('admin'), '10', 1, NOW(), 100, NOW(), 100);

insert into `role_useraccount` (`roleId`, `userAccountId`, `created`, `createdById`) values (10, 100, NOW(), 100);