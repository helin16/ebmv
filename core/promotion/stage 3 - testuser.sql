insert into `person`(`id`, `firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (2, 'test user', 'YL', 1, NOW(), 100, NOW(), 100);
insert into `useraccount`(`id`, `username`, `password`, `personId`, `libraryId`, `active`, `created`, `createdById`, `updated`, `updatedById`) values (2, 'testuser_yl', sha1('testpass_yl'), '2', '1', 1, NOW(), 100, NOW(), 100);
insert into `role_useraccount` (`roleId`, `userAccountId`, `created`, `createdById`) values (2, 2, NOW(), 100);