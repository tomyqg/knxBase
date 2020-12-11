#
# create an antry for a new application system
#
INSERT INTO `mas_sys`.`ApplicationSystem` (`Id`, `ApplicationSystemId`, `Description1`, `VersionMajor`, `VersionMinor`, `VersionBuild`)
    VALUES (NULL, 'frieda', 'Frieda 0.1', '0', '0', '0');
#
#
#
INSERT INTO `mas_sys`.`Application` (`Id`, `ApplicationId`, `ApplicationSystemId`, `Description1`)
    VALUES (NULL, 'bekh', 'frieda', 'Frieda Backend, KH\'s Mockup');
#
#
#
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'def', 'dbAlias', 'def', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'def', 'dbDriver', 'mysql', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'def', 'dbHost', 'localhost', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'def', 'dbName', 'mas_bc_99999998', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'def', 'dbPassword', 'demoerp', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'def', 'dbUser', 'erpdemo', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'def', 'dbPrefix', '', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'appSys', 'dbAlias', 'def', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'appSys', 'dbDriver', 'mysql', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'appSys', 'dbHost', 'localhost', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'appSys', 'dbName', 'mas_bc_99999998_sys', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'appSys', 'dbPassword', 'demoerp', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'appSys', 'dbUser', 'erpdemo', '');
INSERT INTO `mas_sys`.`SysConfigObj` (`Id`, `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`) VALUES (NULL, 'mueller-textil', 'bc', '00000000', 'def', 'appSys', 'dbPrefix', '', '');
#
# create users for frieda
#
INSERT INTO `mas_sys`.`ClientApplication` (`Id`, `ClientId`, `UserId`, `ApplicationSystemId`, `ApplicationId`, `PathConfig`, `PathApplication`) VALUES (NULL, '00000000', 'miskhwe', 'frieda', 'bekh', '', '/frieda/bekh/index.php');
