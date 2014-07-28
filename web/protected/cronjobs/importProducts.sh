#! /bin/sh

/usr/bin/php /var/www/ebmv/web/protected/cronjobs/ImportProduct_Run.php all 3,4,5,6,9 all > /tmp/productImport_`date +"%d_%b_%y"`.log
/usr/bin/php /var/www/ebmv/web/protected/cronjobs/AutoReturnExpiredShelfItems.php > /tmp/autoExpiryShelfItems_`date +"%d_%b_%y"`.log