#!/bin/bash
#
#
echo "sourcing installation dependent data"
source /srv/www/vhosts/modis-gmbh.eu/etc/dbPwd
tables="VKPreisCache ArtikelBestand Options Trans"
#
echo "setting data"
file=dbHourly-`date +%y%m%d-%H%M`.mysql.gz
#
echo "executing mysqldump with " $tables to $file
/usr/local/mysql/bin/mysqldump -u $user -p$pwd --hex-blob $db $tables | gzip >$srcPath/$file
#
echo "executing rsync from " $srcPath/$file " to " $trgt
rsync -q -r -l -vv --password-file /etc/rsync_upload_pwd --exclude-from="$scriptPath/rsync.exclude" $srcPath/$file $trgt
rm $srcPath/$file
