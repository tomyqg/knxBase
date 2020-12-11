#!/bin/sh
echo $1
LANG=C sed -f /srv/www/vhosts/wimtecc.de/r3/Tools/cmd.sed $1 >$1.new
mv $1.new $1
