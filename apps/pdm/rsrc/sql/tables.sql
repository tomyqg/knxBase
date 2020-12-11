#
#	tables.sql
#	==========
#
#	Path:	apps/pdm/rsrc/sql
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

CREATE TABLE IF NOT EXISTS `Customer` (
  `Id` int(8) NOT NULL,
  `CustomerNo` varchar(32) NOT NULL DEFAULT '',
  `AddressType` varchar(1) NOT NULL DEFAULT '',
  `AddressNo` varchar(5) NOT NULL DEFAULT '',
  `CustomerName1` varchar(64) NOT NULL,
  `CustomerName2` varchar(64) NOT NULL,
  `CustomerName3` varchar(64) NOT NULL,
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
  `Remark` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
ALTER TABLE `Customer` ADD UNIQUE(`Id`);
ALTER TABLE `Customer` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES (NULL, 'dbt_Customer_*', 'dbt', 'def.Customer.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES   (NULL, 'pdmMaster', 'dbt_Customer_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES (NULL, 'dbt_Customer_read', 'dbt', 'def.Customer.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES   (NULL, 'pdmMaster', 'dbt_Customer_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES  (NULL, 'dbt_CustomerSurvey_read', 'dbt', 'def.v_CustomerSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES (NULL, 'pdmMaster', 'dbt_CustomerSurvey_read', '1');

CREATE TABLE IF NOT EXISTS `CustomerContact` (
  `Id` int(8) NOT NULL,
  `CustomerNo` varchar(32) NOT NULL,
  `CustomerContactNo` varchar(32) NOT NULL,
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
  `Remark` varchar(250) NOT NULL,
  `Mailing` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
ALTER TABLE `CustomerContact` ADD UNIQUE(`Id`);
ALTER TABLE `CustomerContact` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES (NULL, 'dbt_CustomerContact_*', 'dbt', 'def.CustomerContact.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES   (NULL, 'pdmMaster', 'dbt_CustomerContact_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES (NULL, 'dbt_CustomerContact_read', 'dbt', 'def.CustomerContact.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES   (NULL, 'pdmMaster', 'dbt_CustomerContact_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
  VALUES  (NULL, 'dbt_CustomerContactSurvey_read', 'dbt', 'def.v_CustomerContactSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
  VALUES (NULL, 'pdmMaster', 'dbt_CustomerContactSurvey_read', '1');

DROP TABLE IF EXISTS `CustomerSystem` ;
CREATE TABLE IF NOT EXISTS `CustomerSystem` (
  `Id` int(11) NOT NULL,
  `CustomerNo` varchar(32) NOT NULL,
  `ProjectNo` varchar(64) NOT NULL,
  `DeviceNo` varchar(64) NOT NULL,
  `SerialNo` varchar(64) NOT NULL,
  `SystemTypeId` varchar(32) NOT NULL,
  `SystemTypeVariantId` int(4) NOT NULL,
  `LicenseKey` VARCHAR(128) NOT NULL,
  `DateOrdered` date NOT NULL,
  `DateDelivered` date NOT NULL,
  `DateInstalled` date NOT NULL,
  `Remark` text
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
ALTER TABLE `CustomerSystem` ADD UNIQUE(`Id`);
ALTER TABLE `CustomerSystem` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_CustomerSystem_*', 'dbt', 'def.CustomerSystem.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_CustomerSystem_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_CustomerSystem_*', 'dbt', 'def.CustomerSystem.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_CustomerSystem_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES  (NULL, 'dbt_CustomerSystemSurvey_read', 'dbt', 'def.v_CustomerSystemSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_CustomerSystemSurvey_read', '1');

INSERT INTO `mas_pdm_00000002`.`CustomerSystem` ( CustomerNo, ProjectNo, DeviceNo, SerialNo, SystemTypeId, SystemtypeVariantId, LicenseKey, DateOrdered, DateDelivered, DateInstalled)
  SELECT "00000001", "", CustomerDeviceNumber, CustomerSerialNumberPanel, "", 0, "", CurDate(), CurDate(), CurDate() FROM `mas_pdm_00000002_INPUT`.`2km_customer`
;

DROP TABLE IF EXISTS `CustomerSystemUpdate` ;
CREATE TABLE IF NOT EXISTS `CustomerSystemUpdate` (
  `Id` int(11) NOT NULL,
  `SerialNo` varchar(64) NOT NULL,
  `SoftwareIdent` varchar(32) NOT NULL,
  `Revision` varchar(8) NOT NULL,
  `DateUpdates` date NOT NULL,
  `Remark` text
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
ALTER TABLE `CustomerSystemUpdate` ADD UNIQUE(`Id`);
ALTER TABLE `CustomerSystemUpdate` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_CustomerSystemUpdate_*', 'dbt', 'def.CustomerSystemUpdate.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_CustomerSystemUpdate_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_CustomerSystemUpdate_read', 'dbt', 'def.CustomerSystemUpdate.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_CustomerSystemUpdate_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES  (NULL, 'dbt_CustomerSystemUpdateSurvey_read', 'dbt', 'def.v_CustomerSystemUpdateSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_CustomerSystemUpdateSurvey_read', '1');

DROP TABLE IF EXISTS `SystemType` ;
CREATE TABLE IF NOT EXISTS `SystemType` (
  `Id` int(11) NOT NULL,
  `SystemTypeId` varchar(32) NOT NULL,
  `Description1` varchar(255) NOT NULL,
  `Remark` text
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
ALTER TABLE `SystemType` ADD UNIQUE(`Id`);
ALTER TABLE `SystemType` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_SystemType_*', 'dbt', 'def.SystemType.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_SystemType_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_SystemType_read', 'dbt', 'def.SystemType.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_SystemType_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_SystemTypeSurvey_read', 'dbt', 'def.v_SystemTypeSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_SystemTypeSurvey_read', '1');

INSERT INTO `mas_pdm_00000002`.`SystemType` ( SystemTypeId, Description1)
  SELECT DeviceName, DeviceName AS DN FROM `mas_pdm_00000002_INPUT`.`2km_device`
  ;

DROP TABLE IF EXISTS `SystemTypeVariant` ;
CREATE TABLE IF NOT EXISTS `SystemTypeVariant` (
  `Id` int(11) NOT NULL,
  `SystemTypeId` varchar(32) NOT NULL,
  `SystemTypeVariantId` int(4) NOT NULL,
  `Description1` varchar(255) NOT NULL,
  `SoftwareId` varchar(32) NOT NULL,
  `Remark` text
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
ALTER TABLE `SystemTypeVariant` ADD UNIQUE(`Id`);
ALTER TABLE `SystemTypeVariant` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_SystemTypeVariant_*', 'dbt', 'def.SystemTypeVariant.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_SystemTypeVariant_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_SystemTypeVariant_read', 'dbt', 'def.SystemTypeVariant.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_SystemTypeVariant_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES  (NULL, 'dbt_SystemTypeVariantSurvey_read', 'dbt', 'def.v_SystemTypeVariantSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_SystemTypeVariantSurvey_read', '1');

DROP TABLE IF EXISTS `PLCSystem` ;
CREATE TABLE IF NOT EXISTS `PLCSystem` (
  `Id` int(11) NOT NULL,
  `PLCSystemId` varchar(32) NOT NULL,
  `Description1` varchar(255) NOT NULL,
  `Remark` text
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
ALTER TABLE `PLCSystem` ADD UNIQUE(`Id`);
ALTER TABLE `PLCSystem` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_PLCSystem_*', 'dbt', 'def.PLCSystem.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_PLCSystem_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_PLCSystem_read', 'dbt', 'def.PLCSystem.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_PLCSystem_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES  (NULL, 'dbt_PLCSystemSurvey_read', 'dbt', 'def.v_PLCSystemSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_PLCSystemSurvey_read', '1');

DROP TABLE IF EXISTS `HMISystem` ;
CREATE TABLE IF NOT EXISTS `HMISystem` (
  `Id` int(11) NOT NULL,
  `HMISystemId` varchar(32) NOT NULL,
  `Description1` varchar(255) NOT NULL,
  `Remark` text
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
ALTER TABLE `HMISystem` ADD UNIQUE(`Id`);
ALTER TABLE `HMISystem` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_HMISystem_*', 'dbt', 'def.HMISystem.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_HMISystem_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_HMISystem_read', 'dbt', 'def.HMISystem.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_HMISystem_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES  (NULL, 'dbt_HMISystemSurvey_read', 'dbt', 'def.v_HMISystemSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_HMISystemSurvey_read', '1');

DROP TABLE IF EXISTS `Software` ;
CREATE TABLE IF NOT EXISTS `Software` (
  `Id` int(11) NOT NULL,
  `SoftwareId` varchar(32) NOT NULL,
  `Slogan` varchar(128) NOT NULL,
  `SystemTypeId` varchar(32) NOT NULL,
  `TargetSystem` varchar(32) NOT NULL,
  `PLCSystemId` varchar(32) NOT NULL,
  `HMISystemId` varchar(32) NOT NULL,
  `Caution` text NOT NULL,
  `Description1` text NOT NULL,
  `Description2` text NOT NULL,
  `Remark` text
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
ALTER TABLE `Software` ADD UNIQUE(`Id`);
ALTER TABLE `Software` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_Software_*', 'dbt', 'def.Software.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_Software_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_Software_red', 'dbt', 'def.Software.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_Software_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES  (NULL, 'dbt_SoftwareSurvey_read', 'dbt', 'def.v_SoftwareSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_SoftwareSurvey_read', '1');

DROP TABLE IF EXISTS `SoftwareVersion` ;
CREATE TABLE IF NOT EXISTS `SoftwareVersion` (
  `Id` int(11) NOT NULL,
  `SoftwareId` varchar(32) NOT NULL,
  `Version` varchar(8) NOT NULL,
  `Build` int(10) NOT NULL,
  `RequiredPLCVersion` varchar(32) DEFAULT "n/a",
  `RequiredHMIVersion` varchar(32) DEFAULT "n/a",
  `CheckdInBy` varchar(32) NOT NULL,
  `Locked` int(1) DEFAULT "1",
  `DateReview` date NOT NULL,
  `DateApproval` date NOT NULL,
  `DateAvailable` date NOT NULL,
  `DateEndOfLife` date NOT NULL,
  `Filename` varchar(64),
  `Sha1` varchar(64),
  `Remark` text
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
ALTER TABLE `SoftwareVersion` ADD UNIQUE(`Id`);
ALTER TABLE `SoftwareVersion` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_SoftwareVersion_*', 'dbt', 'def.SoftwareVersion.*', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_SoftwareVersion_*', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES (NULL, 'dbt_SoftwareVersion_read', 'dbt', 'def.SoftwareVersion.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES   (NULL, 'pdmMaster', 'dbt_SoftwareVersion_read', '1');
INSERT INTO `mas_pdm_00000002_sys`.`AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`)
VALUES  (NULL, 'dbt_SoftwareVersionSurvey_read', 'dbt', 'def.v_SoftwareVersionSurvey.read', 'grant', '', '');
INSERT INTO `mas_pdm_00000002_sys`.`ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`, `Active`)
VALUES (NULL, 'pdmMaster', 'dbt_SoftwareVersionSurvey_read', '1');

#
#
#
