#
#	VIEWs.sql
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

DROP VIEW IF EXISTS CustomerSurvey ;
CREATE VIEW CustomerSurvey AS
	SELECT C.Id, C.CustomerNo, CONCAT( C.CustomerName1, ", ", C.CustomerName2) AS Name, C.ZIP, CONCAT( CC.FirstName, " ", CC.LastName) AS Contact
		FROM Customer AS C
		LEFT JOIN CustomerContact AS CC on CC.CustomerNo = C.CustomerNo ;

DROP VIEW IF EXISTS CustomerContactSurvey ;
CREATE VIEW CustomerContactSurvey AS
	SELECT CC.Id, C.CustomerNo, CC.CustomerContactNo, CONCAT( C.CustomerName1, ", ", C.CustomerName2) AS Name, CONCAT( CC.Salutation, ' ', CC.Title, ' ', CC.FirstName, ' ', CC.LastName) AS Contact
		FROM Customer AS C
		JOIN CustomerContact AS CC ON CC.CustomerNo = C.CustomerNo ;
