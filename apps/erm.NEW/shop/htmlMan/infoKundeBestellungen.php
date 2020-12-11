<h1><?php echo FTr::tr( "Customer Data") ; ?></h1>
<fieldset>
	<legend><?php echo FTr::tr( "Organisation") ; ?>:</legend>
	<table>
		<tr>
		<td><?php echo FTr::tr( "Customer No.") ; ?>:</td>
			<td><?php echo $myKunde->KundeNr ; ?></td>
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
	</table>
</fieldset>
<h1><?php echo FTr::tr( "Orders") ; ?></h1>
<fieldset>
<legend><?php echo FTr::tr( "Orders") ; ?>:</legend>
<table>
	<tr>
		<th width="40pt"><?php echo FTr::tr( "Order no.") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Date") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Items") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Total net value") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Total tax") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Total gross value") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Status payment") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Status") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Optionions") ; ?>:</th>
	</tr>
<?php
	$query	=	"select * " ;
	$query	.=	"from KdBest " ;
	$query	.=	"where KundeNr = '" . $myKunde->KundeNr . "' " ;
	$query	.=	"order by KdBestNr DESC " ;
	$sqlResult      =       FDb::query( $query) ;
	if ( !$sqlResult) {
		echo $query . " \n" ;
		printf( "001: Probleme mit query ... \n") ;
		die() ;
	}
	$numrows        =       FDb::rowCount() ;
	$myTempKdBest	=	new KdBest() ;
	while ($row = mysql_fetch_assoc( $sqlResult)) {
		$myTempKdBest->assignFromRow( $row) ;
?>
		<tr>
			<td><?php echo $myTempKdBest->KdBestNr ; ?></td>
			<td><?php echo $myTempKdBest->Datum ; ?></td>
			<td><?php echo $myTempKdBest->Positionen ; ?></td>
			<td><?php echo $myTempKdBest->GesamtPreis ; ?></td>
			<td><?php echo $myTempKdBest->GesamtMwst ; ?></td>
			<td><?php echo $myTempKdBest->GesamtPreis + $myTempKdBest->GesamtMwst ; ?></td>
			<td><?php echo FTr::tr( Option::getRStatPayment( $myTempKdBest->StatPayment)) ; ?></td>
			<td><?php echo $myTempKdBest->Status ; ?></td>
			<td>
<?php	if ( $myTempKdBest->StatPayment < Option::SP_PNDNG) {				?>
				<form name="f4" method="post" action="/Bestellen.php">
					<input type="hidden" name="step" value="11" />
					<input type="hidden" name="_DKdBestNr" value="<?php echo $myTempKdBest->KdBestNr ; ?>" />
					<button type="submit" class="buttonBasic" /><?php echo FTr::tr( "Pay now ...") ; ?></button>
				</form>
<?php		} else {													?>
<?php		}															?>
			</td>
		</tr>
<?php		}															?>
</table>
</fieldset>
