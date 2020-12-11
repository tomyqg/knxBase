#
#
#

DROP TABLE IF EXISTS `RentalContract`;
CREATE TABLE IF NOT EXISTS `RentalContract` (
	`Id` int(11) NOT NULL,
	`RentalContractNo` varchar(8) NOT NULL,
	`RentableNo` varchar(8) NOT NULL,
	`Idx` int(11) NOT NULL COMMENT 'Index',
	`TennantNo` varchar(8) NOT NULL,
	`RentalStart` date NOT NULL,
	`RentalEnd` date DEFAULT NULL,
	`Kaltmiete` float(8,2) NOT NULL,
	`Nebenkosten` float(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `RentalContract` ADD PRIMARY KEY (`Id`);

DROP VIEW IF EXISTS v_RentalContractSurvey ;
CREATE VIEW v_RentalContractSurvey AS
	SELECT RC.Id, RC.RentalContractNo, RC.RentableNo, RC.RentalStart, RC.RentalEnd, RC.Kaltmiete, RC.Nebenkosten 
		FROM RentalContract AS RC
	ORDER BY RC.RentalContractNo
;
