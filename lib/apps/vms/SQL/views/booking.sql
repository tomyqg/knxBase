#
#	VIEWs.sql
#	=========
#
#	Path:	        lib/apps/vms/SQL/
#
#	Product id.:
#	Version:
#
#	Revision history
#
#	Date			Rev.	Who	    what
#	----------------------------------------------------------------------------
#	2020-12-09		khw		khw     inception;
#
#	To-Do
#
#	Date			what
#	----------------------------------------------------------------------------
#
#	@package	??
#	@subpackage	apps/vms/mms
#	@author		karl-heinz welter
#

#
#
#

CREATE TABLE `Booking` (
                                  `Id` int(8) NOT NULL,
                                  `BookingNo` int,
                                  `Description` varchar(64),
                                  `Date` date,
                                  `AccountNoDebit` varchar(8) NOT NULL,
                                  `AccountNoCredit` varchar(8) NOT NULL,
                                  `Amount` float
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `Booking`
    ADD PRIMARY KEY (`Id`);

ALTER TABLE `AccountBooking`
    MODIFY `Id` int(8) NOT NULL AUTO_INCREMENT;COMMIT;

#
#
#

DROP VIEW IF EXISTS v_BookingSurvey ;
CREATE VIEW v_BookingSurvey AS
SELECT AB.Id, AB.BookingNo, AB.Date, AB.Description, A.AccountNo, AB.AccountNoDebit, AB.AccountNoCredit,
    case
        when AB.AccountNoDebit <> "" then
            AB.Amount
        else
            ""
    end AS AmountDebit,
    case
        when AB.AccountNoCredit <> "" then
            AB.Amount
        else
            ""
    end as AmountCredit
FROM Account AS A
         JOIN Booking AS AB ON AB.AccountNoDebit = A.AccountNo OR AB.AccountNoCredit = A.AccountNo ;

