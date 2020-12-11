#
#	VIEWs.sql
#	=========
#
#	Path:	        lib/apps/vms/SQL/
#
#	Product id.:    
#	Version:        
#
#	Revision history
#
#	Date			Rev.	Who	    what
#	----------------------------------------------------------------------------
#	2020-12-09		khw		khw     inception;
#
#	To-Do
#
#	Date			what
#	----------------------------------------------------------------------------
#
#	@package	??
#	@subpackage	apps/vms/mms
#	@author		karl-heinz welter
#

#
#
#

CREATE TABLE `Account` (
    `Id` int(8) NOT NULL,
    `AccountNo` varchar(32) NOT NULL,
    `Description` varchar(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `Account`
    ADD PRIMARY KEY (`Id`),
    ADD UNIQUE KEY `AccountNo` (`AccountNo`);

ALTER TABLE `Account`
    MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT;

#
#
#

DROP VIEW IF EXISTS v_AccountSurvey ;
CREATE VIEW v_AccountSurvey AS
SELECT A.Id, A.AccountNo, A.Description
FROM Account AS A ;

