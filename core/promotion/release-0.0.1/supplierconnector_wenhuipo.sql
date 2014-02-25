############################ add supplier table
insert into `supplier` (`id`, `name`, `connector`,`active`, `created`, `createdById`, `updated`, `updatedById`) values 
	(4, '文匯報', 'SC_WenHuiPo', 1, NOW(), 100, NOW(), 100);

############################ add supplierinfo table
insert into `supplierinfo` (`supplierId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('4', 2, 'http://pdf.wenweipo.com/{productKey}/pdf1.htm', 1, NOW(), 100, NOW(), 100),
    ('4', 4, '2', 1, NOW(), 100, NOW(), 100),
    ('4', 5, '2', 1, NOW(), 100, NOW(), 100),
    ('4', 8, '2', 1, NOW(), 100, NOW(), 100),
    ('4', 6, '/var/www/html/protected/asset/supplier4/', 1, NOW(), 100, NOW(), 100);
