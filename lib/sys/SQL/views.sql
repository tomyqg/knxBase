#
#	views.sql
#	=========
#
#	Path:	lib/sys/SQL/
#
#	Product id.:
#	Version:
#
#	Revision history
#
#	Date			Rev.	Who		what
#	----------------------------------------------------------------------------
#	2015-03-23				khw		inception;
#
#	ToDo
#
#	Date			what
#	----------------------------------------------------------------------------
#
#	@package		??
#	@subpackage	System
#	@author		khwelter
#

#
#
#

drop view if exists ApplicationSystemPerClient ;
create view ApplicationSystemPerClient as
	select CA.ClientId, CA.ApplicationSystemId, CA.ApplicationId, A.Description1 from ClientApplication as CA
		join ApplicationSystem as A on A.ApplicationSystemId = CA.ApplicationSystemId
		group by ClientId, ApplicationSystemId ;

#
#
#

drop view if exists ApplicationPerClient ;
create view ApplicationPerClient as
	select CA.ClientId, CA.ApplicationSystemId, CA.ApplicationId, A.Description1 from ClientApplication as CA
		join Application as A on A.ApplicationId = CA.ApplicationId and A.ApplicationSystemId = CA.ApplicationSystemId
		group by ClientId, ApplicationSystemId, ApplicationId ;
