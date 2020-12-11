#
#
#

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`, `Parameter`, `Value`, `Help`)
    SELECT 'erm', 'erp', '', `Class`, `Section`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'erm' AND `ApplicationId` = 'erp' AND `ClientId` = '' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`, `Parameter`, `Value`, `Help`)
    SELECT 'erm', 'erp', '', `Class`, `Section`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'erm' AND `ApplicationId` = 'erp' AND `ClientId` = '' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`, `Parameter`, `Value`, `Help`)
    SELECT 'erm', 'erp', '00000002', `Section`, `Section`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'erm' AND `ApplicationId` = 'erp' AND `ClientId` = '1a2b3c4d' ;
