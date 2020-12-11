#
# create the SysConfigObj objects for a customer
#

#
# create the basic application objects for the system database and the application database
#

USE mas_sys;#
SET @ClientId = '1a2b3c4d' ;
INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`, `Parameter`, `Value`, `Help`)
    SELECT 'immo', 'man', @ClientId, `Class`, `Section`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'immo' AND `ApplicationId` = 'man' AND `ClientId` = '1a2b3c4d'
	ORDER BY `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`,`Parameter`;

#
# create the basic application objects for the user interface database
#

INSERT INTO `SysConfigObj`
                (`ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`, `Parameter`, `Value`, `Help`)
    SELECT 'immo', 'man', @ClientId, `Class`, `Section`, `Parameter`, `Value`, `Help`
        FROM SysConfigObj
        WHERE `ApplicationSystemId` = 'immo' AND `ApplicationId` = 'man' AND `ClientId` = ''
	ORDER BY `ApplicationSystemId`, `ApplicationId`, `ClientId`, `Class`, `Section`,`Parameter`;

UPDATE `mas_sys`.`SysConfigObj` SET `Value` = concat( 'mas_immo_', @ClientId)
	WHERE `ClientId` = @ClientId
	  AND `Section` = 'def'
	  AND `Parameter` = 'dbName' ;
UPDATE `mas_sys`.`SysConfigObj` SET `Value` = concat( 'mas_immo_', @ClientId, '_sys')
	WHERE `ClientId` = @ClientId
	  AND `Section` = 'appSys'
	  AND `Parameter` = 'dbName' ;
