#
#
#

DROP TABLE IF EXISTS `Rentable`;
CREATE TABLE IF NOT EXISTS `Rentable` (
	`Id` int(8) NOT NULL,
	`RentableNo` varchar(32) NOT NULL,
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
ALTER TABLE `Rentable` ADD UNIQUE(`Id`);
ALTER TABLE `Rentable` CHANGE `Id` `Id` INT(8) NOT NULL AUTO_INCREMENT;

DROP VIEW IF EXISTS v_RentableSurvey ;
CREATE VIEW v_RentableSurvey AS
	SELECT R.Id, R.RentableNo, CONCAT( R.Description1, " ", R.Description2) AS RentableName
		FROM Rentable AS R
	ORDER BY R.RentableNo
;

CREATE TABLE IF NOT EXISTS `PropertyRentable` (
	`Id` int(10) NOT NULL,
	`PropertyNo` varchar(32) NOT NULL,
	`RentableNo` varchar(32) NOT NULL,
	`Active` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `PropertyRentable` CHANGE `Id` `Id` INT(10) NOT NULL AUTO_INCREMENT;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `PropertyRentable`
--
ALTER TABLE `PropertyRentable`
	ADD PRIMARY KEY (`Id`);

DROP VIEW IF EXISTS v_PropertyRentableSurvey ;
CREATE VIEW v_PropertyRentableSurvey AS
	SELECT PR.Id, P.PropertyNo, R.RentableNo, P.Description1 AS PropertyDescription, R.Description1 AS RentableDescription
		FROM PropertyRentable AS PR
		LEFT JOIN Property AS P on P.PropertyNo = PR.PropertyNo
		LEFT JOIN Rentable AS R on R.RentableNo = PR.RentableNo
	ORDER BY PR.Id
;
