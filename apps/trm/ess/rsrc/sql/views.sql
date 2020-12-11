#
#	VIEWs.sql
#	=========
#
#	Path:	apps/trm/ess/rsrc/sql
#
#	Product id.:
#	Version:
#
#	Revision history
#
#	Date			Rev.	Who		what
#	----------------------------------------------------------------------------
#	2015-06-25				khw		inception;
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

DROP VIEW IF EXISTS EmployeeSurvey ;
CREATE VIEW EmployeeSurvey AS
	SELECT C.Id, C.EmployeeNo, CONCAT( C.FirstName, " ", C.LastName) AS Name
		FROM Employee AS C
;
