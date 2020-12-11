#!/bin/sh
for i in *.php
do
	echo $i
	LANG=C sed -f /srv/www/vhosts/wimtecc.de/r3/Tools/cmd.sed $i >$i.new
	mv $i.new $i
done
for i in *.js
do
	echo $i
	LANG=C sed -f /srv/www/vhosts/wimtecc.de/r3/Tools/cmd.sed $i >$i.new
	mv $i.new $i
done
