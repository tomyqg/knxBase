#!/bin/bash
#
#
echo "sourcing installation dependent data"
source /srv/www/vhosts/modis-gmbh.eu/etc/dbPwd
#
cd $scriptPath
./job_dumpDbDaily.bash
./job_dumpDbBackup.bash