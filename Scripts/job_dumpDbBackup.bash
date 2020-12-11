#!/bin/bash
#
#
echo "sourcing installation dependent data"
source /srv/www/vhosts/modis-gmbh.eu/etc/dbPwd
#
file=dbFull-`date +%y%m%d-%H%M`.mysql.gz
/usr/local/mysql/bin/mysqldump -u $user -p$pwd --hex-blob $db | gzip >$backupPath/$file