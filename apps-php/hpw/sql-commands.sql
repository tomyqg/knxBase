CREATE USER 'writtelbrunft'@'localhost' IDENTIFIED BY 'writtelbrunft';
GRANT ALL PRIVILEGES ON a9ifGhdjei86GtDe . *  TO 'writtelbrunft'@'localhost';
CREATE USER 'writtelbrunft'@'%' IDENTIFIED BY 'writtelbrunft';
GRANT ALL PRIVILEGES ON a9ifGhdjei86GtDe . *  TO 'writtelbrunft'@'%';


CREATE TABLE IF NOT EXISTS `log` (
`Id` int(11) NOT NULL,
  `LogTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `GroupObjectId` int(11) NOT NULL,
  `DataType` int(11) NOT NULL,
  `Value` varchar(64) NOT NULL,
  `Transfer` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `logStructure` (
`Id` int(11) NOT NULL,
  `LogTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `GroupObjectId` int(11) NOT NULL,
  `DataType` int(11) NOT NULL,
  `Value` varchar(64) NOT NULL,
  `Transfer` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
