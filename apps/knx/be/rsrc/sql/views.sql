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

DROP VIEW IF EXISTS v_FullDPT ;
DROP VIEW IF EXISTS v_FullDPT;
CREATE VIEW  v_FullDPT AS
	SELECT CONCAT( DST.DPTMainTypeId, '.', DST.DPTSubTypeId) AS Id, DST.Description AS Value
		FROM DataPointSubType AS DST
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
