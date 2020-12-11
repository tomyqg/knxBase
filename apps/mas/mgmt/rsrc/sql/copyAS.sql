#
#
#

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Block`, `Parameter`, `Value`, `Help`, `Imported`)
    SELECT 'bpw', 'tcrs', '99999999', `Class`, `Block`, `Parameter`, `Value`, `Help`, 2
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'bpw' AND `ApplicationId` = 'r3pa1' AND `ClientId` = '99999999' ;
