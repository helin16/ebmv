############################ add supplier table
insert into `supplier` (`id`, `name`, `connector`,`active`, `created`, `createdById`, `updated`, `updatedById`) values 
	(3, '大公报', 'SC_TaKungPao', 1, NOW(), 100, NOW(), 100);

############################ add supplierinfo table
insert into `supplierinfo` (`supplierId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('3', 2, 'http://news.takungpao.com.hk/paper/{productKey}.html', 1, NOW(), 100, NOW(), 100),
    ('3', 3, 'http://paper.takungpao.com/resfile/PDF/{productKey}/ZIP/{productKey}_pdf.zip', 1, NOW(), 100, NOW(), 100),
    ('3', 4, '2', 1, NOW(), 100, NOW(), 100),
    ('3', 5, '2', 1, NOW(), 100, NOW(), 100),
    ('3', 8, '2', 1, NOW(), 100, NOW(), 100),
    ('3', 6, '/var/www/html/protected/asset/supplier3/', 1, NOW(), 100, NOW(), 100);
