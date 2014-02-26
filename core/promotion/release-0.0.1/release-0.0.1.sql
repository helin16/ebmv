############################ add library table
update library set connector='LC_Local' where id = 1;
insert into `library` (`id`, `name`, `connector`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    (2, 'Bankstown Library', 'LC_Bankstown',1, NOW(), 100, NOW(), 100),
    (3, 'Yarra Plenty Library', 'LC_SIP2',1, NOW(), 100, NOW(), 100),
    (4, 'Whitehorse Library', 'LC_SIP2',1, NOW(), 100, NOW(), 100);

############################ add libraryinfotype table
insert into `libraryinfotype` (`id`, `name`, `code`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    (6, 'The SOAP WSDL URL', 'soap_wsdl',  1, NOW(), 100, NOW(), 100),
    (7, 'The SIP2 host addr[203.23.231.1:8627]', 'sip2_host',  1, NOW(), 100, NOW(), 100);
    
insert into `libraryinfo` (`libraryId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('2', '1', 'NBANK', 1, NOW(), 100, NOW(), 100),
    ('2', '2', 'bankstownlib.ebmv.com.au', 1, NOW(), 100, NOW(), 100),
    ('2', '3', 'Australia/Melbourne', 1, NOW(), 100, NOW(), 100),
    ('2', '4', 'bankstown', 1, NOW(), 100, NOW(), 100),
    ('2', '5', '1', 1, NOW(), 100, NOW(), 100),
    ('2', '6', 'http://library.bankstown.nsw.gov.au/Libero/LiberoWebServices.WebOpac.cls', 1, NOW(), 100, NOW(), 100),
    
    ('3', '1', 'UNK', 1, NOW(), 100, NOW(), 100),
    ('3', '2', 'yarraplenty.ebmv.com.au', 1, NOW(), 100, NOW(), 100),
    ('3', '3', 'Australia/Melbourne', 1, NOW(), 100, NOW(), 100),
    ('3', '4', 'bankstown', 1, NOW(), 100, NOW(), 100),
    ('3', '5', '1', 1, NOW(), 100, NOW(), 100),
    ('3', '7', '206.187.32.61:8163', 1, NOW(), 100, NOW(), 100),
    
    ('4', '1', 'UNK', 1, NOW(), 100, NOW(), 100),
    ('4', '2', 'whlib.ebmv.com.au', 1, NOW(), 100, NOW(), 100),
    ('4', '3', 'Australia/Melbourne', 1, NOW(), 100, NOW(), 100),
    ('4', '4', 'bankstown', 1, NOW(), 100, NOW(), 100),
    ('4', '5', '1', 1, NOW(), 100, NOW(), 100),
    ('4', '7', '203.89.253.70:6021', 1, NOW(), 100, NOW(), 100);

############################ add supplier table
insert into `supplier` (`id`, `name`, `connector`,`active`, `created`, `createdById`, `updated`, `updatedById`) values 
	(3, '大公报', 'SC_TaKungPao', 1, NOW(), 100, NOW(), 100),
	(4, '文匯報', 'SC_WenHuiPo', 1, NOW(), 100, NOW(), 100);

############################ add supplierinfo table
insert into `supplierinfo` (`supplierId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
	('3', 2, 'http://news.takungpao.com.hk/paper/{productKey}.html', 1, NOW(), 100, NOW(), 100),
    ('3', 3, 'http://paper.takungpao.com/resfile/PDF/{productKey}/ZIP/{productKey}_pdf.zip', 1, NOW(), 100, NOW(), 100),
    ('3', 4, '2', 1, NOW(), 100, NOW(), 100),
    ('3', 5, '2', 1, NOW(), 100, NOW(), 100),
    ('3', 8, '2', 1, NOW(), 100, NOW(), 100),
    ('3', 6, '/var/www/html/protected/asset/supplier3/', 1, NOW(), 100, NOW(), 100),

    ('4', 2, 'http://pdf.wenweipo.com/{productKey}/pdf1.htm', 1, NOW(), 100, NOW(), 100),
    ('4', 4, '2', 1, NOW(), 100, NOW(), 100),
    ('4', 5, '2', 1, NOW(), 100, NOW(), 100),
    ('4', 8, '2', 1, NOW(), 100, NOW(), 100),
    ('4', 6, '/var/www/html/protected/asset/supplier4/', 1, NOW(), 100, NOW(), 100);