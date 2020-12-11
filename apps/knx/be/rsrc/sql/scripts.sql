#
#
#

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'knx', 'be', '00000001', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'trm' AND `ApplicationId` = 'be' AND `ClientId` = '00000000' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'knx', 'be', '', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'trm' AND `ApplicationId` = 'be' AND `ClientId` = '' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'knx', 'mgmt', '00000001', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'trm' AND `ApplicationId` = 'be' AND `ClientId` = '00000000' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'knx', 'mgmt', '', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'trm' AND `ApplicationId` = 'be' AND `ClientId` = '' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'frieda', 'be', '00000000', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'trm' AND `ApplicationId` = 'be' AND `ClientId` = '00000000' AND Block = 'appSys' ;
