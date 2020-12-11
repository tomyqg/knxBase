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
# Customer related views
#

DROP VIEW IF EXISTS v_CustomerCustomerContactSurvey ;
CREATE VIEW v_CustomerCustomerContactSurvey AS
	SELECT C.Id, C.CustomerNo, CC.CustomerContactNo, CONCAT( C.CustomerName1, " ", C.CustomerName2) AS CustomerName, CONCAT( CC.FirstName, " ", CC.LastName) AS Name
		FROM Customer AS C
		LEFT JOIN CustomerContact AS CC on CC.CustomerNo = C.CustomerNo
	ORDER BY C.CustomerNo, CC.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerContactSurvey ;
CREATE VIEW v_CustomerContactSurvey AS
	SELECT CC.Id, CC.CustomerNo, CC.CustomerContactNo, CC.FirstName, CC.LastName
		FROM CustomerContact AS CC
	ORDER BY CC.CustomerContactNo
;

DROP VIEW IF EXISTS v_CustomerActivitySurvey ;
CREATE VIEW v_CustomerActivitySurvey AS
	SELECT CA.Id, CA.CustomerNo, CA.CustomerContactNo, CC.FirstName, CC.LastName, CA.ActivityDate, CA.ActivityType, CA.Description, CA.FollowUp
		FROM CustomerActivity AS CA
		LEFT JOIN CustomerContact AS CC ON CC.CustomerNo = CA.CustomerNo AND CC.CustomerContactNo = CA.CustomerContactNo
;

#
# MySQL Variante
#

DROP VIEW IF EXISTS v_Tables ;
DROP VIEW IF EXISTS v_Tables ;
CREATE VIEW  v_Tables AS
	SELECT TABLE_NAME AS TableName, TABLE_SCHEMA AS DataBaseName
		FROM information_schema.TABLES
;

#
# MSSQL Variante
#

DROP VIEW IF EXISTS v_Tables ;
CREATE VIEW  v_Tables AS
	SELECT Distinct TABLE_NAME AS TableName, TABLE_CATALOG AS DataBaseName
		FROM information_schema.TABLES
;
