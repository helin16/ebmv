############################ add supplier table
insert into `supplier` (`id`, `name`, `connector`,`active`, `created`, `createdById`, `updated`, `updatedById`) values
	(5, '新民晚报', 'SC_XinMinWanBao', 1, NOW(), 100, NOW(), 100),
	(6, '新民周刊', 'SC_XinMinZhouKan', 1, NOW(), 100, NOW(), 100);
	
############################ add supplierinfo table
insert into `supplierinfo` (`supplierId`, `typeId`,`value`, `active`, `created`, `createdById`, `updated`, `updatedById`) values
    ('5', 2, 'http://xmwb.xinmin.cn/html/{productKey}/node_1.htm', 1, NOW(), 100, NOW(), 100),
    ('5', 4, '1', 1, NOW(), 100, NOW(), 100),
    ('5', 5, '2', 1, NOW(), 100, NOW(), 100),
    ('5', 6, '/var/www/html/protected/asset/supplier4/', 1, NOW(), 100, NOW(), 100),
    ('5', 8, '2', 1, NOW(), 100, NOW(), 100),
    
    ('6', 2, 'http://xmzk.xinmin.cn/html/{productKey}/node_1.htm', 1, NOW(), 100, NOW(), 100),
    ('6', 4, '1', 1, NOW(), 100, NOW(), 100),
    ('6', 5, '3', 1, NOW(), 100, NOW(), 100),
    ('6', 6, '/var/www/html/protected/asset/supplier4/', 1, NOW(), 100, NOW(), 100),
    ('6', 8, '2', 1, NOW(), 100, NOW(), 100);