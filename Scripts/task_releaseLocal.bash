#
#
#
php ./inc.php ../ERP/rc.txt
rsync -r -l -vv --password-file /etc/rsync_developer_pwd --exclude-from="rsync.exclude" /srv/www/vhosts/wimtecc.de/r2/* erpdeveloper@$(REFSRV)::r2
