############################ add library table
insert into `library` (`id`, `name`, `connector`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    (3, 'Yarra Plenty Library', 'LC_SIP2',1, NOW(), 100, NOW(), 100);
    

############################ add libraryinfotype table
insert into `libraryinfotype` (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    (7, 'The SIP2 host addr[203.23.231.1:8627]', 'sip2_host',  1, NOW(), 100, NOW(), 100);
    
insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('3', '1', 'NBANK', 1, NOW(), 100, NOW(), 100),
    ('3', '2', 'yarraplenty.ebmv.com.au', 1, NOW(), 100, NOW(), 100),
    ('3', '3', 'Australia/Melbourne', 1, NOW(), 100, NOW(), 100),
    ('3', '4', 'default', 1, NOW(), 100, NOW(), 100),
    ('3', '5', '1', 1, NOW(), 100, NOW(), 100),
    ('3', '7', '206.187.32.61:8163', 1, NOW(), 100, NOW(), 100);
