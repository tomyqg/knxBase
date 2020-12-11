#
#
#

DROP TABLE IF EXISTS `Property`;
CREATE TABLE IF NOT EXISTS `Property` (
	`Id` int(8) NOT NULL,
	`PropertyNo` varchar(32) NOT NULL,
	`Description1` varchar(40) NOT NULL,
	`Description2` varchar(40) NOT NULL,
	`Description3` varchar(40) NOT NULL,
	`ZIP` varchar(8) NOT NULL,
	`City` varchar(32) NOT NULL,
	`Street` varchar(32) NOT NULL,
	`Number` varchar(6) NOT NULL,
	`Country` varchar(32) NOT NULL,
	`Remark` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `Property` ADD UNIQUE(`Id`);
ALTER TABLE `Property` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;

DROP VIEW IF EXISTS v_PropertySurvey ;
CREATE VIEW v_PropertySurvey AS
	SELECT P.Id, P.PropertyNo, CONCAT( P.Description1, " ", P.Description2) AS PropertyName
		FROM Property AS P
	ORDER BY P.PropertyNo
;
