############################ add library table
insert into `library` (`id`, `name`, `connector`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    (2, 'Bankstown Library', 'LC_Bankstown',1, NOW(), 100, NOW(), 100);
update library set connector='LC_Local' where id = 1;

############################ add libraryinfotype table
insert into `libraryinfotype` (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    (6, 'The SOAP WSDL URL', 'soap_wsdl',  1, NOW(), 100, NOW(), 100);
    
insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('2', '1', 'NBANK', 1, NOW(), 100, NOW(), 100),
    ('2', '2', 'bankstownlib.ebmv.com.au', 1, NOW(), 100, NOW(), 100),
    ('2', '3', 'Australia/Melbourne', 1, NOW(), 100, NOW(), 100),
    ('2', '4', 'default', 1, NOW(), 100, NOW(), 100),
    ('2', '5', '1', 1, NOW(), 100, NOW(), 100),
    ('2', '6', 'http://library.bankstown.nsw.gov.au/Libero/LiberoWebServices.WebOpac.cls', 1, NOW(), 100, NOW(), 100);