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
#	2020-11-14		khw		khw     inception;
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

CREATE TABLE `Member` (
    `Id` int(8) NOT NULL,
    `MemberNo` varchar(32) NOT NULL,
    `FirstName` varchar(40) NOT NULL,
    `MiddleName` varchar(40) NOT NULL,
    `LastName` varchar(40) NOT NULL,
    `ZIP` varchar(8) NOT NULL,
    `City` varchar(32) NOT NULL,
    `Street` varchar(32) NOT NULL,
    `Number` varchar(6) NOT NULL,
    `Country` varchar(32) NOT NULL,
    `Language` varchar(10) NOT NULL DEFAULT 'de_DE',
    `Phone` varchar(32) NOT NULL,
    `Fax` varchar(32) NOT NULL,
    `Cellphone` varchar(32) NOT NULL,
    `eMail` varchar(64) NOT NULL,
    `Remark` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `Member`
    ADD PRIMARY KEY (`Id`),
    ADD UNIQUE KEY `MemberNo` (`MemberNo`);

ALTER TABLE `Member`
    MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT;COMMIT;

#
#
#

CREATE TABLE `MemberContact` (
    `Id` int(8) NOT NULL,
    `MemberNo` varchar(32) NOT NULL,
    `MemberContactNo` varchar(32) NOT NULL,
    `Salutation` varchar(8) NOT NULL,
    `Title` varchar(16) NOT NULL,
    `FirstName` varchar(32) NOT NULL,
    `LastName` varchar(32) NOT NULL,
    `Phone` varchar(32) NOT NULL,
    `Fax` varchar(32) NOT NULL,
    `Cellphone` varchar(32) NOT NULL,
    `eMail` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `MemberContact`
    ADD PRIMARY KEY (`Id`),
    ADD KEY `AdresseNr` (`MemberNo`,`MemberContactNo`);

ALTER TABLE `MemberContact`
    MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT;COMMIT;

#
#
#

CREATE TABLE `MemberPeriod` (
    `Id` int(8) NOT NULL,
    `MemberNo` varchar(12) NOT NULL,
    `DateEntry` date NOT NULL,
    `DateExit` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `MemberPeriod`
    ADD PRIMARY KEY (`Id`),
    ADD KEY `MemberNoDateEntry` (`MemberNo`,`DateEntry`) USING BTREE;

ALTER TABLE `MemberPeriod`
    MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT;COMMIT;

#
#
#

DROP VIEW IF EXISTS v_MemberSurvey ;
CREATE VIEW v_MemberSurvey AS
	SELECT M.Id, M.MemberNo, M.FirstName, M.LastName, M.ZIP
		FROM Member AS M ;

DROP VIEW IF EXISTS v_MemberSurvey ;
CREATE VIEW v_MemberSurvey AS
SELECT M.Id, M.MemberNo, M.FirstName, M.LastName, M.ZIP
FROM Member AS M
         LEFT JOIN MemberContact AS MC on MC.MemberNo = M.MemberNo ;

#
#
#

DROP VIEW IF EXISTS v_MemberContactSurvey ;
CREATE VIEW v_MemberContactSurvey AS
	SELECT MC.Id, M.MemberNo, MC.MemberContactNo, MC.LastName, MC.FirstName
		FROM Member AS M
		JOIN MemberContact AS MC ON MC.MemberNo = M.MemberNo ;

#
#
#

DROP VIEW IF EXISTS v_MemberPeriodSurvey ;
CREATE VIEW v_MemberPeriodSurvey AS
	SELECT MP.Id, M.MemberNo, MP.DateEntry, MP.DateExit
		FROM Member AS M
		JOIN MemberPeriod AS MP ON MP.MemberNo = M.MemberNo
		ORDER BY MP.DateEntry DESC;
