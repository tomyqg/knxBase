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

DROP VIEW IF EXISTS v_AccountingPeriodItem_1 ;
CREATE VIEW v_AccountingPeriodItem_1 AS
	SELECT API.*, C.CustomerName1 FROM AccountingPeriodItem AS API
		LEFT JOIN Customer AS C ON C.CustomerNo = API.CustomerNo
	;

#
#
#
