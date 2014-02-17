############################ add library table
insert into `library` (`id`, `name`, `connector`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    (4, 'Whitehorse Library', 'LC_SIP2',1, NOW(), 100, NOW(), 100);
    

insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('4', '1', 'UNK', 1, NOW(), 100, NOW(), 100),
    ('4', '2', 'whlib.ebmv.com.au', 1, NOW(), 100, NOW(), 100),
    ('4', '3', 'Australia/Melbourne', 1, NOW(), 100, NOW(), 100),
    ('4', '4', 'default', 1, NOW(), 100, NOW(), 100),
    ('4', '5', '1', 1, NOW(), 100, NOW(), 100),
    ('4', '7', '203.89.253.70:6021', 1, NOW(), 100, NOW(), 100);
