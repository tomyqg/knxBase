<h1><?php echo FTr::tr( "Customer Data") ; ?></h1>
<fieldset>
	<legend><?php echo FTr::tr( "Organisation") ; ?>:</legend>
	<table>
		<tr>
		<td><?php echo FTr::tr( "Customer No.") ; ?>:</td>
			<td><?php echo $myKunde->CustomerNo ; ?></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Company/School/University") ; ?>:</td>
			<td><input name="_IFirmaName1" value="<?php echo $myKunde->FirmaName1 ; ?>"/></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "more Company") ; ?>:</td>
			<td><input name="_IFirmaName2" value="<?php echo $myKunde->FirmaName2 ; ?>"/></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="3" align="left">Firmendaten:</td>
		</tr>
	</table>
</fieldset>

<h1><?php echo FTr::tr( "Enquiries") ; ?></h1>
<fieldset>
<legend><_php FTr::tr( "Enquiries") ; ?>:</legend>
<table>
	<tr>
	<th width="40pt"><?php echo FTr::tr( "Enquiry no.") ; ?>:</th>
		<th><?php echo FTr::tr( "Date") ; ?>:</th>
		<th><?php echo FTr::tr( "Items") ; ?>:</th>
	</tr>
<?php
	$query	=	"select * " ;
	$query	.=	"from KdAnf " ;
	$query	.=	"where CustomerNo = '" . $myKunde->CustomerNo . "' " ;
	$query	.=	"order by KdAnfNr DESC " ;
	$sqlResult      =       FDb::mysql_query( $query) ;
	if ( !$sqlResult) {
		echo $query . " \n" ;
		printf( "001: Probleme mit query ... \n") ;
		die() ;
	}
	$numrows        =       FDb::rowCount() ;
	$myTempKdAnf	=	new KdAnf() ;
	while ($row = mysql_fetch_assoc( $sqlResult)) {
		//
		$myTempKdAnf->assignFromRow( $row) ;
		//
?>
		<tr>
			<td><?php echo $myTempKdAnf->KdAnfNr ; ?></td>
			<td><?php echo $myTempKdAnf->Datum ; ?></td>
			<td><?php echo $myTempKdAnf->Positionen ; ?></td>
			<td>
			</td>
		<tr>
<?php
	}
	//
?>
</table>
</fieldset>
