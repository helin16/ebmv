#/usr/bin/php /var/www/ebmv/web/protected/cronjobs/ImportProduct_Run.php all 2 all > /tmp/productImport_`date +"%d_%b_%y"`_YuanLiu.log
/usr/bin/php /var/www/ebmv/web/protected/cronjobs/ImportProduct_Run.php all 3 all > /tmp/productImport_`date +"%d_%b_%y"`_TaiKong.log
/usr/bin/php /var/www/ebmv/web/protected/cronjobs/ImportProduct_Run.php all 4 all > /tmp/productImport_`date +"%d_%b_%y"`_WenHui.log
/usr/bin/php /var/www/ebmv/web/protected/cronjobs/ImportProduct_Run.php all 5 all > /tmp/productImport_`date +"%d_%b_%y"`_XinMinNewsPaper.log
/usr/bin/php /var/www/ebmv/web/protected/cronjobs/ImportProduct_Run.php all 9 all > /tmp/productImport_`date +"%d_%b_%y"`_Apabi.log
/usr/bin/php /var/www/ebmv/web/protected/cronjobs/AutoReturnExpiredItems.php > /tmp/autoExpiryShelfItems_`date +"%d_%b_%y"`.log
