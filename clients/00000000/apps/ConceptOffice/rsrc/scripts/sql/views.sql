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
#	2015-10-08		PA1		khw		inception;
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

DROP VIEW IF EXISTS v_ProductSurvey ;
CREATE VIEW v_ProductSurvey AS
	SELECT C.Id, C.ProductId, C.Manufacturer, C.PartNo
			FROM Product AS C
	;

DROP VIEW IF EXISTS v_BatchSurvey ;
CREATE VIEW v_BatchSurvey AS
	SELECT C.Id, C.ProductId, C.BatchNo, C.Year, C.DayOfYear
			FROM Batch AS C
	;

DROP VIEW IF EXISTS v_BatchItemSurvey ;
CREATE VIEW v_BatchItemSurvey AS
	SELECT BI.Id, BI.BatchNo, BI.ItemNo, BI.Year, BI.DayOfYear
		FROM BatchItem AS BI
;
