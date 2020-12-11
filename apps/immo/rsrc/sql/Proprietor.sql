CREATE TABLE IF NOT EXISTS `Proprietor` (
  `Id` int(8) NOT NULL,
  `ProprietorNo` varchar(32) NOT NULL DEFAULT '',
  `AddressType` varchar(1) NOT NULL DEFAULT '',
  `AddressNo` varchar(5) NOT NULL DEFAULT '',
  `ProprietorName1` varchar(64) NOT NULL,
  `ProprietorName2` varchar(64) NOT NULL,
  `ProprietorName3` varchar(64) NOT NULL,
  `ZIP` varchar(16) NOT NULL,
  `City` varchar(32) NOT NULL,
  `Street` varchar(32) NOT NULL,
  `Number` varchar(6) NOT NULL,
  `Country` varchar(32) NOT NULL DEFAULT 'de',
  `Language` varchar(10) NOT NULL DEFAULT 'de_de',
  `Phone` varchar(32) NOT NULL,
  `Fax` varchar(32) NOT NULL,
  `Cellphone` varchar(32) NOT NULL,
  `URL` varchar(64) NOT NULL,
  `eMail` varchar(64) NOT NULL,
  `TaxId` varchar(32) NOT NULL,
  `MandNr` varchar(16) NOT NULL,
  `UserName` varchar(16) NOT NULL,
  `Password` varchar(128) NOT NULL,
  `Currency` varchar(4) NOT NULL DEFAULT 'EUR',
  `Tax` int(1) NOT NULL DEFAULT '1',
  `ProprietorType` varchar(8) NOT NULL DEFAULT '0' COMMENT 'Proprietor type, 0=consumer, 1=business',
  `Organization` varchar(48) NOT NULL DEFAULT 'Sonstige',
  `Remark` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ProprietorContact` (
  `Id` int(8) NOT NULL,
  `ProprietorNo` varchar(32) NOT NULL,
  `ProprietorContactNo` varchar(32) NOT NULL,
  `Function` varchar(12) NOT NULL,
  `Address` varchar(32) NOT NULL,
  `Salutation` varchar(8) NOT NULL,
  `Title` varchar(16) NOT NULL,
  `FirstName` varchar(32) NOT NULL,
  `LastName` varchar(32) NOT NULL,
  `Phone` varchar(32) NOT NULL,
  `Fax` varchar(32) NOT NULL,
  `Cellphone` varchar(32) NOT NULL,
  `eMail` varchar(64) NOT NULL,
  `UserName` varchar(16) NOT NULL,
  `Password` varchar(128) NOT NULL,
  `ActivationKey` varchar(100) NOT NULL,
  `Remark` varchar(250) NOT NULL,
  `Mailing` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP VIEW IF EXISTS v_ProprietorProprietorContactSurvey ;
CREATE VIEW v_ProprietorProprietorContactSurvey AS
	SELECT S.Id, S.ProprietorNo, SC.ProprietorContactNo, CONCAT( S.ProprietorName1, " ", S.ProprietorName2) AS ProprietorName, CONCAT( SC.FirstName, " ", SC.LastName) AS Name
		FROM Proprietor AS S
		LEFT JOIN ProprietorContact AS SC on SC.ProprietorNo = S.ProprietorNo
	ORDER BY S.ProprietorNo, SC.ProprietorContactNo
;

DROP VIEW IF EXISTS v_ProprietorContactSurvey ;
CREATE VIEW v_ProprietorContactSurvey AS
	SELECT CC.Id, CC.ProprietorNo, CC.ProprietorContactNo, CC.FirstName, CC.LastName
		FROM ProprietorContact AS CC
	ORDER BY CC.ProprietorContactNo
;
