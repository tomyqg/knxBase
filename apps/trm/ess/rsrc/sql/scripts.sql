#
#
#

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'trm', 'ess', '00000000', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'trm' AND `ApplicationId` = 'be' AND `ClientId` = '00000000' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`)
    SELECT 'trm', 'ess', '', `Class`, `Block`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'trm' AND `ApplicationId` = 'be' AND `ClientId` = '' ;
