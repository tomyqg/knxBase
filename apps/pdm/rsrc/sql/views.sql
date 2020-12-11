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
#	Date			  Rev.	Who		what
#	----------------------------------------------------------------------------
#	2018-06-26	PA1		khw		inception;
#
#	ToDo
#
#	Date			what
#	----------------------------------------------------------------------------
#
#	@package		??
#	@subpackage	System
#	@author   	khwelter
#

#
#
#

DROP VIEW IF EXISTS v_CustomerSurvey ;
CREATE VIEW v_CustomerSurvey AS
	SELECT C.Id, C.CustomerNo, CONCAT( C.CustomerName1, ", ", C.CustomerName2) AS CustomerName, CONCAT( O.Value, ", ", C.ZIP, " ", C.City) AS Location
		FROM Customer AS C
		LEFT JOIN AppOption AS O ON O.OptionName = "Country" AND O.Key = C.Country ;

DROP VIEW IF EXISTS v_CustomerContactSurvey ;
CREATE VIEW v_CustomerContactSurvey AS
	SELECT CC.Id, C.CustomerNo, CC.CustomerContactNo, CustomerName1, CustomerName2, CONCAT( CC.Salutation, ' ', CC.Title, ' ', CC.FirstName, ' ', CC.LastName) AS Contact
		FROM Customer AS C
		JOIN CustomerContact AS CC ON CC.CustomerNo = C.CustomerNo ;

DROP VIEW IF EXISTS v_CustomerCustomerContactSurvey ;
CREATE VIEW v_CustomerCustomerContactSurvey AS
	SELECT C.Id, C.CustomerNo, CC.FirstName, CC.LastName, CONCAT( C.CustomerName1, ", ", C.CustomerName2) AS Name, C.ZIP, CONCAT( CC.FirstName, " ", CC.LastName) AS Contact
	FROM Customer AS C
		LEFT JOIN CustomerContact AS CC on CC.CustomerNo = C.CustomerNo ;

DROP VIEW IF EXISTS v_CustomerSystemSurvey ;
CREATE VIEW v_CustomerSystemSurvey AS
	SELECT * FROM CustomerSystem AS CS ;

DROP VIEW IF EXISTS v_CustomerSystemUpdateSurvey ;
CREATE VIEW v_CustomerSystemUpdateSurvey AS
	SELECT * FROM CustomerSystemUpdate AS CSU;

DROP VIEW IF EXISTS v_SystemTypeSurvey ;
CREATE VIEW v_SystemTypeSurvey AS
	SELECT ST.*
		FROM SystemType AS ST ;

DROP VIEW IF EXISTS v_SystemTypeVariantSurvey ;
CREATE VIEW v_SystemTypeVariantSurvey AS
	SELECT STV.*, ST.Description1 AS ST_Description1
		FROM SystemType AS ST
	  LEFT JOIN SystemTypeVariant AS STV ON STV.SystemTypeId = ST.SystemTypeId ;

DROP VIEW IF EXISTS v_PLCSystemSurvey ;
CREATE VIEW v_PLCSystemSurvey AS
	SELECT S.*
	FROM PLCSystem AS S ;

DROP VIEW IF EXISTS v_HMISystemSurvey ;
CREATE VIEW v_HMISystemSurvey AS
	SELECT S.*
	FROM HMISystem AS S ;

DROP VIEW IF EXISTS v_SoftwareSurvey ;
CREATE VIEW v_SoftwareSurvey AS
	SELECT S.*
	FROM Software AS S ;

DROP VIEW IF EXISTS v_SoftwareVersionSurvey ;
CREATE VIEW v_SoftwareVersionSurvey AS
	SELECT S.*
	FROM SoftwareVersion AS S ;

DROP VIEW IF EXISTS AppUser ;
CREATE VIEW v_AppUser AS
	SELECT CONCAT( FirstName, " ", LastName) AS Name
	FROM AppUser ;

#
#
#
