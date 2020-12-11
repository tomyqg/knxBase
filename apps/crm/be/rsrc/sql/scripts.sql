#
#
#

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'crm', 'be', '00000000', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'knx' AND `ApplicationId` = 'be' AND `ClientId` = '00000001' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'crm', 'be', '00000000', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'knx' AND `ApplicationId` = 'be' AND `ClientId` = '' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'crm', 'mgmt', '00000000', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'knx' AND `ApplicationId` = 'mgmt' AND `ClientId` = '00000001' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'crm', 'mgmt', '00000000', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'knx' AND `ApplicationId` = 'mgmt' AND `ClientId` = '' ;
