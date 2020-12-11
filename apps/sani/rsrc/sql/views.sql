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

DROP VIEW IF EXISTS v_KundeSurvey ;
CREATE VIEW v_KundeSurvey AS
	SELECT K.Id, K.KundeNr, CONCAT( K.Name1, ", ", K.Vorname) AS KundeName
		FROM Kunde AS K
	ORDER BY K.KundeNr
;

#
#
