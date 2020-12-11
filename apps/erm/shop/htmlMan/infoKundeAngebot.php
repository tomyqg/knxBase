<h1><?php echo FTr::tr( "Customer Data") ; ?></h1>
<fieldset>
	<legend><?php echo FTr::tr( "Organisation") ; ?>:</legend>
	<table>
		<tr>
		<td><?php echo FTr::tr( "Customer No.") ; ?>:</td>
			<td><?php echo $myCustomer->CustomerNo ; ?></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Company/School/University") ; ?>:</td>
			<td><input name="_IFirmaName1" value="<?php echo $myCustomer->FirmaName1 ; ?>"/></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "more Company") ; ?>:</td>
			<td><input name="_IFirmaName2" value="<?php echo $myCustomer->FirmaName2 ; ?>"/></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="3" align="left">Firmendaten:</td>
		</tr>
	</table>
</fieldset>

<h1><?php echo FTr::tr( "Quotations") ; ?></h1>
<fieldset>
<legend><?php echo FTr::tr( "Quotations") ; ?>:</legend>
<table>
	<tr>
	<th width="40pt"><?php echo FTr::tr( "Quotation No.") ; ?>:</th>
		<th><?php echo FTr::tr( "Date") ; ?>:</th>
		<th><?php echo FTr::tr( "Items") ; ?>:</th>
		<th><?php echo FTr::tr( "Total net value") ; ?>:</th>
		<th><?php echo FTr::tr( "Status") ; ?>:</th>
		<th><?php echo FTr::tr( "Optionions") ; ?>:</th>
	</tr>
<?php
	$query	=	"select * " ;
	$query	.=	"from KdAng " ;
	$query	.=	"where CustomerNo = '" . $myCustomer->CustomerNo . "' " ;
	$query	.=	"order by KdAngNr DESC " ;
	$sqlResult      =       FDb::query( $query) ;
	if ( !$sqlResult) {
		echo $query . " \n" ;
		printf( "001: Probleme mit query ... \n") ;
		die() ;
	}
	$numrows        =       FDb::rowCount() ;
	$myTempKdAng	=	new KdAng() ;
	while ($row = mysql_fetch_assoc( $sqlResult)) {
		//
		$myTempKdAng->assignFromRow( $row) ;
		//
?>
		<tr>
			<td><?php echo $myTempKdAng->KdAngNr ; ?></td>
			<td><?php echo $myTempKdAng->Datum ; ?></td>
			<td><?php echo $myTempKdAng->Positionen ; ?></td>
			<td><?php echo $myTempKdAng->GesamtPreis ; ?></td>
			<td><?php echo $myTempKdAng->Status ; ?></td>
			<td>
			</td>
		<tr>
<?php
	}
	//
?>
</table>
</fieldset>
