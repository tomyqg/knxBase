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

DROP VIEW IF EXISTS v_AppUserRoleProfileAuthObjectSurvey ;
CREATE VIEW v_AppUserRoleProfileAuthObjectSurvey AS
	SELECT AO.Id, AUR.UserId, AO.AuthObjectId, AO.AuthObjectType, AO.ObjectName, AO.ObjectAttribute
		FROM AppUserRole AS AUR
		JOIN RoleProfile AS RP on RP.RoleId = AUR.RoleId
		JOIN ProfileAuthObject AS PAO ON PAO.ProfileId = RP.ProfileId
		JOIN AuthObject AS AO ON AO.AuthObjectId = PAO.AuthObjectId
	ORDER BY AO.AuthObjectType, AO.ObjectName
;

DROP VIEW IF EXISTS v_AppUserRoleSurvey ;
CREATE VIEW v_AppUserRoleSurvey AS
	SELECT AUR.Id, AUR.UserId, AUR.RoleId, R.Name
		FROM AppUserRole AS AUR
		LEFT JOIN Role AS R on R.RoleId = AUR.RoleId
	ORDER BY AUR.RoleId
;

DROP VIEW IF EXISTS v_RoleProfileSurvey ;
CREATE VIEW v_RoleProfileSurvey AS
	SELECT RP.Id, RP.RoleId, RP.ProfileId, P.Name
		FROM RoleProfile AS RP
		LEFT JOIN Profile AS P on P.ProfileId = RP.ProfileId
	ORDER BY RP.RoleId
;

DROP VIEW IF EXISTS v_ProfileAuthObjectSurvey ;
CREATE VIEW v_ProfileAuthObjectSurvey AS
	SELECT PAO.Id, PAO.ProfileId, PAO.AuthObjectId, AO.AuthObjectType, AO.ObjectName, AO.ObjectAttribute
		FROM ProfileAuthObject AS PAO
		LEFT JOIN AuthObject AS AO on AO.AuthObjectId = PAO.AuthObjectId
	ORDER BY AO.ObjectName
;

#
# list all AppUser having Role
#
DROP VIEW IF EXISTS v_AppUserWithRoleSurvey ;
CREATE VIEW v_AppUserWithRoleSurvey AS
	SELECT AUR.Id, R.RoleId, AUR.UserId, AUR.RoleId AS AUR_RoleId
		FROM AppUserRole AS AUR
		LEFT JOIN Role AS R on R.RoleId = AUR.RoleId
	ORDER BY AUR.UserId
;

#
# list all Role having Profile
#
DROP VIEW IF EXISTS v_RoleWithProfileSurvey ;
CREATE VIEW v_RoleWithProfileSurvey AS
	SELECT R.Id, RP.ProfileId, R.RoleId, R.Name
		FROM RoleProfile AS RP
		LEFT JOIN Role AS R on R.RoleId = RP.RoleId
	ORDER BY RP.RoleId
;

#
# list all Profile having AuthObject
#
DROP VIEW IF EXISTS v_ProfileWithAuthObjectSurvey ;
CREATE VIEW v_ProfileWithAuthObjectSurvey AS
	SELECT P.Id, PAO.AuthObjectId, P.ProfileId, P.Name
		FROM ProfileAuthObject AS PAO
		LEFT JOIN Profile AS P on P.ProfileId = PAO.ProfileId
	ORDER BY P.ProfileId
;

##
#
#
