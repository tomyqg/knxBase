#!/bin/bash
#
# job_reloadDb.bash
#
# Server:       frontend
# Schedule:     taeglich
#
# Dieses Script laedt die sql-Dateien aus dem Verzeichnins /srv/www/vhosts/modis-gmbh.eu/Archive/XML/up/
# in die MySQL Datenbank.
#
cd /srv/www/vhosts/flaschen24.eu/Archive/XML/up
for i in *.gz
do
	if [ -e $i ];
	then
		echo "unzip dump " $i
		gunzip $i
		echo "loaded"
		echo `date` "unzipped " $i >lastReload.txt
		head -20 __infoOld.txt >> lastReload.txt
		mv lastReload.txt __infoOld.txt
	fi
done
for i in *.mysql
do
	if [ -e $i ];
	then
        echo "load dump " $i
        mysql -u modisde -pdemodis modisde_r3 < $i
        rm $i
        echo "loaded"
        echo `date` "database reloaded with " $i >lastReload.txt
		head -20 __infoOld.txt >> lastReload.txt
		mv lastReload.txt __infoOld.txt
	fi
done