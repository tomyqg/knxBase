-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 27. Aug 2015 um 09:03
-- Server Version: 5.6.21
-- PHP-Version: 5.5.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `mas_erm_1a2b3c4d_sys`
--

--
-- Daten für Tabelle `AppConfigObj`
--


--
-- Daten für Tabelle `AppOption`
--

INSERT INTO `AppOption` (`Id`, `Class`, `OptionName`, `Key`, `Value`, `Symbol`) VALUES
(1, '', 'Flag', '0', 'No', ''),
(2, '', 'Flag', '1', 'Yes', ''),
(7, '', 'Application', 'A', 'Application', 'fdgfdgdf'),
(8, '', 'Application', 'S', 'System', ''),
(9, '', 'Language', 'de', 'deutsch', ''),
(10, '', 'Language', 'de_DE', 'deutsch/Deutschland', ''),
(11, '', 'Language', 'en', 'english', ''),
(12, '', 'Language', 'en_US', 'englich/USA', ''),
(13, '', 'Language', 'fr', 'französisch', ''),
(14, '', 'Language', 'fr_FR', 'französisch/Frankreich', ''),
(15, '', 'Language', 'es', 'spanisch', ''),
(16, '', 'Language', 'es_ES', 'spanisch/Spanien', ''),
(17, '', 'Language', 'es_CL', 'spanisch/Chile', ''),
(18, '', 'Language', 'en_UK', 'englisch/England', ''),
(19, '', 'Language', 'en_CA', 'englisch/Kanada', ''),
(20, '', 'Language', 'fr_CA', 'französisch/Kanada', ''),
(21, '', 'Language', 'de_AU', 'deutsch/Österreich', ''),
(22, '', 'Language', 'de_CH', 'deutsch/Schweiz', ''),
(31, '', 'Country', 'de', 'Deutschland', ''),
(32, '', 'Country', 'dk', 'Dänemark', ''),
(33, '', 'Country', 'at', 'Österreich', ''),
(34, '', 'Country', 'uk', 'England', ''),
(35, '', 'Country', 'nl', 'Niederlande', ''),
(36, '', 'Country', 'be', 'Belgien', ''),
(37, '', 'Country', 'es', 'Spanien', ''),
(38, '', 'Country', 'pt', 'Portugal', ''),
(39, '', 'Country', 'se', 'Schweden', ''),
(40, '', 'Country', 'fi', 'Finnland', ''),
(41, '', 'Country', 'no', 'Norwegen', ''),
(42, '', 'Country', 'fr', 'Frankreich', ''),
(43, '', 'Country', 'it', 'Italien', ''),
(44, '', 'Country', 'ch', 'Schweiz', ''),
(45, '', 'Country', 'us', 'Vereinigte Staaten', ''),
(46, '', 'Country', 'cl', 'Chile', ''),
;

--
-- Daten für Tabelle `AppUser`
--

INSERT INTO `AppUser` (`Id`, `ClientId`, `UserId`, `ApplicationSystemId`, `ApplicationId`, `OrgName1`, `OrgName2`, `LastName`, `FirstName`, `MailId`, `Password`, `MD5Password`, `Street`, `Number`, `City`, `ZIP`, `Province`, `Country`, `Telephone`, `Cellphone`, `FAX`, `Registration`, `Type`, `Lang`, `Confirmed`, `Level`, `ValidFrom`, `ValidTo`, `DateReg`, `DateLastAcc`, `Packages`, `Modules`) VALUES
(1, '', 'miskhwe', 'erpdemo', '', '', '', NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, 'de', NULL, NULL, NULL, '2015-01-01', 1, 'de', 0, 0, '2015-01-01', '2099-12-31', '2099-12-31', '2099-12-31', '*', '*'),
(2, '', 'shop', 'shop', '', '', '', NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, 'de', NULL, NULL, NULL, '2015-01-01', 1, 'de', 0, 0, '2015-01-01', '2099-12-31', '2099-12-31', '2099-12-31', '*', '*');

--
-- Daten für Tabelle `AppUserRole`
--

INSERT INTO `AppUserRole` (`Id`, `UserId`, `RoleId`) VALUES
(1, 'miskhwe', 'admin'),
(6, 'miskhwe', 'sysAdmin'),
(7, 'miskhwe', 'sysOp'),
(9, 'miskhwe', 'hr_basic'),
(10, 'miskhwe', '_basic')
;

--
-- Daten für Tabelle `AuthObject`
--

INSERT INTO `AuthObject` (`Id`, `AuthObjectId`, `AuthObjectType`, `ObjectName`, `ObjectAttribute`, `AttrValue`, `Description`) VALUES
(1, 'dbt_AppUser_store', 'dbt', 'AppUser.store', 'grant', '', ''),
(2, 'fnc_AppUser_create', 'fnc', 'AppUser.add', 'grant', '', ''),
(3, 'fnc_AppUser_update', 'fnc', 'AppUser.upd', 'grant', '', ''),
(4, 'fnc_Role_create', 'fnc', 'Role.add', 'grant', '', ''),
(5, 'fnc_Role_update', 'fnc', 'Role.upd', 'grant', '', ''),
(6, 'fnc_Profile_create', 'fnc', 'Profile.add', 'grant', '', ''),
(7, 'fnc_Profile_update', 'fnc', 'Profile.upd', 'grant', '', ''),
(8, 'fnc_AuthObject_*', 'fnc', 'AuthObject.*', 'grant', '', ''),
(9, 'dbt_RoleProfile_store', 'dbt', 'RoleProfile.store', 'grant', '', ''),
(10, 'dbt_RoleProfile_update', 'dbt', 'RoleProfile.update', 'grant', '', ''),
(11, 'scr_ModAdmin_Client', 'scr', 'ModAdmin.client', 'grant', '', ''),
(12, 'dbt_AppUserRole_store', 'dbt', 'AppUserRole.store', 'grant', '', ''),
(13, 'scr_ModAdmin', 'scr', 'ModAdmin', 'grant', '', ''),
(14, 'scr_ModAdmin_AppUser', 'scr', 'ModAdmin.appUser', 'grant', '', ''),
(15, 'scr_ModAdmin_Role', 'scr', 'ModAdmin.role', 'grant', '', ''),
(16, 'scr_ModBase', 'scr', 'ModBase', 'grant', '', ''),
(17, 'scr_ModBase_*', 'scr', 'ModBase.*', 'grant', '', ''),
(18, 'scr_ModAdmin_Profile', 'scr', 'ModAdmin.profile', 'grant', '', ''),
(19, 'scr_ModAdmin_AuthObject', 'scr', 'ModAdmin.authObject', 'grant', '', ''),
(20, 'dbt_Role_*', 'dbt', 'Role.*', 'grant', '', ''),
(21, 'dbt_AuthObject_*', 'dbt', 'AuthObject.*', 'grant', '', ''),
(22, 'dbt_Profile_*', 'dbt', 'Profile.*', 'grant', '', ''),
(23, 'dbt_ProfileAuthObject_*', 'dbt', 'ProfileAuthObject.*', 'grant', '', ''),
(24, 'fnc_Profile_*', 'fnc', 'Profile.*', 'grant', '', ''),
(25, 'dbt_AppTrans_store', 'dbt', 'AppTrans.store', 'grant', '123', ''),
(26, 'scr_ModOperator_*', 'scr', 'ModOperator.*', 'grant', '', ''),
(27, 'scr_ModOperator', 'scr', 'ModOperator', 'grant', '', ''),
(28, 'scr_ModAdmin_AuthObject_gridAuthObjectOV', 'scr', 'ModAdmin.authObject.gridAuthObjectOV', 'grant', '', ''),
(29, 'scr_ModAdmin_*', 'scr', 'ModAdmin.*', 'grant', '', ''),
(30, 'scr_ModAdmin_AppUser_*', 'scr', 'ModAdmin.appUser.*', 'grant', '', ''),
(31, 'scr_ModAdmin_Role_*', 'scr', 'ModAdmin.role.*', 'grant', '', ''),
(32, 'scr_ModAdmin_Profile_*', 'scr', 'ModAdmin.profile.*', 'grant', '', ''),
(33, 'scr_ModAdmin_Client_*', 'scr', 'ModAdmin.client.*', 'grant', '', ''),
(34, 'scr_ModAdmin_Profile_hide_gridProfileAuthObjectsFNC', 'scr', 'ModAdmin.profile.gridProfileAuthObjectsFNC', 'grant', '', ''),
(35, 'scr_ModAdmin_Profile_hide_gridRoleWithProfile', 'scr', 'ModAdmin.profile.gridRoleWithProfile', 'revoke', '', ''),
(36, 'scr_ModAdmin_Profile_hide_tabPageProfileAuthObjectsFNC', 'scr', 'ModAdmin.profile.tabPageProfileAuthObjectsFNC', 'grant', '', ''),
(37, 'scr_ModAdmin_Profile_hide_storeProfile', 'scr', 'ModAdmin.profile.buttonStoreProfile', 'revoke', '', ''),
(38, 'scr_ModAdmin_Profile_1', 'scr', 'ModAdmin.profile.Name.edit', 'revoke', '', ''),
(39, 'scr_ModAdmin_AppUser_MiscInfo', 'scr', 'ModAdmin.appUser.MiscInfo', 'grant', '', ''),
(40, 'scr_ModAdmin_AppUser_Create', 'scr', 'ModAdmin.appUser.Create', 'grant', '', ''),
(41, 'scr_ModAdmin_AppUser_Access', 'scr', 'ModAdmin.appUser.Access', 'grant', '', ''),
(42, 'dbt_AuthObject_update', 'dbt', 'AuthObject.update', 'grant', '', ''),
(43, 'dbt_AuthObject_update_AuthObjectType', 'dbt', 'AuthObject.update.AuthObjectType', 'grant', '123', ''),
(44, 'dbt_AppTrans_avoidFY', 'dbv', 'AppTrans.AttrValue', 'revokevalue', 'fuck', ''),
(45, 'dbt_AuthObject_avoidFY', 'dbv', 'AuthObject.AttrValue', 'revokevalue', 'fuck', ''),
(46, 'dbt_AppUser_*', 'dbt', 'AppUser.*', 'grant', '', ''),
(47, 'dbv_AppUser_avoidFY', 'dbv', 'AppUser.Street', 'revokevalue', '/f[ui]ck/', ''),
(48, 'dbv_AppUser_Number_avoid1', 'dbv', 'AppUser.Number', 'revokevalue', '/^0+[1..9]*/', ''),
(49, 'dbt_AppTrans_read', 'dbt', 'AppTrans.read', 'grant', '', ''),
(50, 'dbt_ApplicationSystem_*', 'dbt', 'ApplicationSystem.*', 'grant', '', ''),
(51, 'dbt_Application_*', 'dbt', 'Application.*', 'grant', '', ''),
(52, 'dbt_Client_*', 'dbt', 'Client.*', 'grant', '', ''),
(53, 'dbt_ClientApplication_*', 'dbt', 'ClientApplication.*', 'grant', '', ''),
(54, 'dbt_v_tables_*', 'dbt', 'v_tables.*', 'grant', '', ''),
(55, 'dbt_ApplicationVersion_*', 'dbt', 'ApplicationVersion.*', 'grant', '', ''),
(56, 'dbt_SysConfigObj_*', 'dbt', 'SysConfigObj.*', 'grant', '', ''),
(57, 'dbt_SysSession_*', 'dbt', 'SysSession.*', 'grant', '', ''),
(58, 'dbt_SysTrans_*', 'dbt', 'SysTrans.*', 'grant', '', ''),
(59, 'dbt_SysUser_*', 'dbt', 'SysUser.*', 'grant', '', ''),
(60, 'dbt_applicationperclient_*', 'dbt', 'applicationperclient.*', 'grant', '', ''),
(61, 'dbt_applicationsystemperclient_*', 'dbt', 'applicationsystemperclient.*', 'grant', '', ''),
(62, 'dbt_AppConfigObj_*', 'dbt', 'AppConfigObj.*', 'grant', '', ''),
(63, 'dbt_AppTrans_*', 'dbt', 'AppTrans.*', 'grant', '', ''),
(64, 'dbt_AppUserRole_*', 'dbt', 'AppUserRole.*', 'grant', '', ''),
(65, 'dbt_RoleProfile_*', 'dbt', 'RoleProfile.*', 'grant', '', ''),
(66, 'dbt_v_AppUserAuthObjectSurvey_*', 'dbt', 'v_AppUserAuthObjectSurvey.*', 'grant', '', ''),
(67, 'dbt_v_AppUserRoleProfileAuthObjectSurvey_*', 'dbt', 'v_AppUserRoleProfileAuthObjectSurvey.*', 'grant', '', ''),
(68, 'dbt_v_AppUserRoleSurvey_*', 'dbt', 'v_AppUserRoleSurvey.*', 'grant', '', ''),
(69, 'dbt_v_AppUserWithRoleSurvey_*', 'dbt', 'v_AppUserWithRoleSurvey.*', 'grant', '', ''),
(70, 'dbt_v_ProfileAuthObjectSurvey_*', 'dbt', 'v_ProfileAuthObjectSurvey.*', 'grant', '', ''),
(71, 'dbt_v_ProfileWithAuthObjectSurvey_*', 'dbt', 'v_ProfileWithAuthObjectSurvey.*', 'grant', '', ''),
(72, 'dbt_v_RoleProfileSurvey_*', 'dbt', 'v_RoleProfileSurvey.*', 'grant', '', ''),
(73, 'dbt_v_RoleWithProfileSurvey_*', 'dbt', 'v_RoleWithProfileSurvey.*', 'grant', '', ''),
(74, 'dbt_v_tableswithoutauthobject_*', 'dbt', 'v_tableswithoutauthobject.*', 'grant', '', ''),
(75, 'dbt_SysTrans_read', 'dbt', 'SysTrans.read', 'grant', '', ''),
(76, 'scr_ModAdmin_AppTrans', 'scr', 'ModAdmin.AppTrans', 'grant', '', ''),
(77, 'scr_ModAdmin_AppTrans_*', 'scr', 'ModAdmin.AppTrans.*', 'grant', '', ''),
(78, 'dbt_CashSaleItem_*', 'dbt', 'CashSaleItem.*', 'grant', '', '');

--
-- Daten für Tabelle `Profile`
--

INSERT INTO `Profile` (`Id`, `ProfileId`, `Name`, `Description`) VALUES
(1, 'sysOp', 'system operator', ''),
(2, 'sysAdmin', 'system operator', ''),
(3, 'admin', 'system operator', ''),
(4, '_basic', 'system operator', '');

--
-- Daten für Tabelle `ProfileAuthObject`
--

INSERT INTO `ProfileAuthObject` (`Id`, `ProfileId`, `AuthObjectId`) VALUES
(1, 'admin', 'fnc_AppUser_create'),
(2, 'admin', 'fnc_AppUser_update'),
(3, 'admin', 'fnc_Role_create'),
(4, 'admin', 'fnc_Role_update'),
(5, 'admin', 'dbt_AppTrans_store'),
(6, 'admin', 'dbt_AppUser_*'),
(7, 'admin', 'scr_ModAdmin_Client'),
(8, 'admin', 'dbt_AppUserRole_*'),
(9, 'admin', 'scr_ModAdmin'),
(10, 'admin', 'scr_ModAdmin_AppUser'),
(11, 'admin', 'scr_ModAdmin_Role'),
(12, 'admin', 'scr_ModBase'),
(13, 'admin', 'scr_ModBase_*'),
(14, 'admin', 'scr_ModAdmin_Profile'),
(15, 'admin', 'scr_ModAdmin_AuthObject'),
(16, 'admin', 'dbt_Role_*'),
(19, 'admin', 'dbt_AuthObject_*'),
(20, 'admin', 'dbt_ProfileAuthObject_*'),
(21, 'admin', 'fnc_Profile_*'),
(22, 'admin', 'dbt_Profile_*'),
(23, 'admin', 'fnc_AuthObject_*'),
(24, 'sysOp', 'scr_ModOperator_*'),
(25, 'sysOp', 'scr_ModOperator'),
(26, 'admin', 'scr_ModAdmin_AuthObject_gridAuthObjectOV'),
(27, 'admin', 'scr_ModAdmin_*'),
(28, 'admin', 'scr_ModAdmin_AppUser_*'),
(29, 'admin', 'scr_ModAdmin_Client_*'),
(30, 'admin', 'scr_ModAdmin_Role_*'),
(31, 'admin', 'scr_ModAdmin_Profile_hide_gridProfileAuthObjectsFNC'),
(32, 'admin', 'scr_ModAdmin_Profile_hide_gridRoleWithProfile'),
(33, 'admin', 'scr_ModAdmin_Profile_hide_tabPageProfileAuthObjectsFNC'),
(34, 'admin', 'scr_ModAdmin_Profile_hide_storeProfile'),
(35, 'admin', 'scr_ModAdmin_Profile_1'),
(36, 'admin', 'scr_ModAdmin_AppUser_MiscInfo'),
(37, 'admin', 'scr_ModAdmin_AppUser_Create'),
(38, 'admin', 'scr_ModAdmin_AppUser_Access'),
(39, 'admin', 'dbt_AuthObject_update_AuthObjectType'),
(40, 'admin', 'dbt_AppUser_update_*'),
(41, 'admin', 'dbv_AppUser_avoidFY'),
(42, 'admin', 'dbv_AppUser_Number_avoid1'),
(43, 'admin', 'dbt_*'),
(44, 'admin', 'dbt_AppTrans_read'),
(45, '_basic', 'dbt_AppTrans_read'),
(46, '_basic', 'dbt_AppTrans_store'),
(47, 'sysOp', 'dbt_ApplicationSystem_*'),
(48, 'sysOp', 'dbt_Application_*'),
(49, 'sysOp', 'dbt_Client_*'),
(50, 'sysOp', 'dbt_ClientApplication_*'),
(51, 'admin', 'dbt_AppConfigObj_*'),
(52, 'admin', 'dbt_AppTrans_*'),
(53, 'admin', 'dbt_RoleProfile_*'),
(54, 'admin', 'dbt_v_AppUserAuthObjectSurvey_*'),
(55, 'admin', 'dbt_v_AppUserRoleProfileAuthObjectSurvey_*'),
(56, 'admin', 'dbt_v_AppUserRoleSurvey_*'),
(57, 'admin', 'dbt_v_appuserwithrolesurvey_*'),
(58, 'admin', 'dbt_v_profileauthobjectsurvey_*'),
(59, 'admin', 'dbt_v_profilewithauthobjectsurvey_*'),
(60, 'admin', 'dbt_v_roleprofilesurvey_*'),
(61, 'admin', 'dbt_v_rolewithprofilesurvey_*'),
(62, 'admin', 'dbt_v_tables_*'),
(63, 'admin', 'dbt_v_tableswithoutauthobject_*'),
(64, '_basic', 'dbt_SysTrans_read'),
(65, 'admin', 'ModAdmin_AppTrans'),
(66, 'admin', 'scr_ModAdmin_AppTrans'),
(67, 'admin', 'scr_ModAdmin_AppTrans_*');

--
-- Daten für Tabelle `Role`
--

INSERT INTO `Role` (`Id`, `RoleId`, `Name`, `Description`) VALUES
(1, 'sysOp', 'Role: Administrator', 'This is the basic role which all administrators should have.'),
(2, 'sysAdmin', 'Role: Administrator', 'This role shall be provided only to those people who can translate the labels of user input elements into their native language.'),
(3, 'admin', 'Role: Tester', 'sfdsafdfsd f sds');

--
-- Daten für Tabelle `RoleProfile`
--

INSERT INTO `RoleProfile` (`Id`, `RoleId`, `ProfileId`) VALUES
(1, 'sysOp', 'admin'),
(2, 'sysAdmin', 'admin'),
(3, 'admin', 'admin');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
