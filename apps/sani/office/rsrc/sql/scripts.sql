#
# create SysConfigObj entries for sani::office from erm::erp
#

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`, `Parameter`, `Value`, `Help`)
    SELECT 'sani', 'office', '1a2b3c4d', `Class`, `Section`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'erm' AND `ApplicationId` = 'erp' AND `ClientId` = '1a2b3c4d' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`, `Parameter`, `Value`, `Help`)
    SELECT 'sani', 'office', '1a2b3c4d', `Class`, `Section`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'erm' AND `ApplicationId` = 'erp' AND `ClientId` = '' ;

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`, `Parameter`, `Value`, `Help`)
    SELECT 'sani', 'office', '1a2b3c4d', `Class`, "cloud", `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'sani'
	  AND `ApplicationId` = 'office'
	  AND `ClientId` = '1a2b3c4d'
	  AND `Section` = "def"
	;
