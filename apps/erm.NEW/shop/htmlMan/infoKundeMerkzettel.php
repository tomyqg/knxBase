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

<h1><?php echo FTr::tr( "Stored wishlists") ; ?></h1>
<fieldset>
	<legend><b><?php echo FTr::tr( "Wishlist") ?>:</b></legend>
<table><tbody>
	<tr>
		<th class="gmz"><?php echo FTr::tr( "No.") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Date") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Items") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Price") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Status") ; ?>:</th>
		<th class="gmz"><?php echo FTr::tr( "Remark(s)") ; ?>:</th>
		<th class="gmz" colspan="2"><?php echo FTr::tr( "Optionions") ; ?>:</th>
	</tr>
<?php
	$where	=	"KundeNr = '" . $myKunde->KundeNr . "' " ;
	$where	.=	"AND Status < '9' " ;
	$where	.=	"ORDER BY MerkzettelNr DESC " ;
	$myTempMerkzettel	=	new Merkzettel() ;
	for ( $myTempMerkzettel->_firstFromDb( $where) ;
			$myTempMerkzettel->_valid ;
			$myTempMerkzettel->_nextFromDb()) {
		if ( strcmp( $myMerkzettel->MerkzettelUniqueId, $myTempMerkzettel->MerkzettelUniqueId) == 0 && $myMerkzettel->_valid) {
			echo "<tr style=\"background-color: #0c0\">\n" ;
		} else {
			echo "<tr>\n" ;
		}
?>
			<td class="gmz"><?php echo $myTempMerkzettel->MerkzettelNr ; ?></td>
			<td class="gmz"><?php echo $myTempMerkzettel->Datum ; ?></td>
			<td class="gmz"><?php echo $myTempMerkzettel->Positionen ; ?></td>
			<td class="gmz"><?php echo $myTempMerkzettel->GesamtPreis ; ?></td>
			<td class="gmz"><?php echo $myTempMerkzettel->getStatusT() . "[" . $myTempMerkzettel->Status . "]" ; ?></td>
			<td class="gmz">
			<form class="gmz" method="post" action="/GespeicherteMerkzettel.php">
				<fieldset>
					<input type="hidden" name="_action" value="updateMZVermerk" />
					<input type="hidden" name="_IId" value="<?php echo $myTempMerkzettel->Id ; ?>"/>
					<input type="text" name="_IVermerk" value="<?php echo $myTempMerkzettel->Vermerk ; ?>">
					<input type="submit" value="<?php echo FTr::tr( "Update") ; ?>" class="buttonSearch" />
				</fieldset>
			</form>
			<td class="gmz">
<?php		if ( strcmp( $mySession->MerkzettelUniqueId, $myTempMerkzettel->MerkzettelUniqueId) == 0 && $myMerkzettel->_valid) {			?>
			<form class="gmz" method="post" action="/GespeicherteMerkzettel.php">
				<fieldset>
					<input type="hidden" name="_action" value="switchMZ" />
					<input type="hidden" name="_IMerkzettelUniqueId" value="<?php echo $myTempMerkzettel->MerkzettelUniqueId ; ?>"/>
					<input type="submit" value="<?php echo FTr::tr( "Store and create new wishlist") ; ?>" class="buttonSearch" />
				</fieldset>
			</form>
<?php
		} else {
?>
			<table>
			<tr>
			<td class="gmz">
<!--			<form method="post" action="/GespeicherteMerkzettel.php">		-->
			<form class="gmz" method="post" action="/Merkzettel.php">
<?php
			switch ( $myTempMerkzettel->Status) {
			case	0	:
?>
				<fieldset>
					<input type="hidden" name="_action" value="activateMZ" />
					<input type="hidden" name="_IMerkzettelUniqueId" value="<?php echo $myTempMerkzettel->MerkzettelUniqueId ; ?>"/>
					<input type="submit" value="<?php echo FTr::tr( "Activate") ; ?>" class="buttonSearch" />
				</fieldset>
<?php
				break ;
			case	1	:
?>
<!--				<fieldset>
					<input type="hidden" name="_action" value="activateMZ" />
					<input type="hidden" name="_IMerkzettelUniqueId" value="<?php echo $myTempMerkzettel->MerkzettelUniqueId ; ?>"/>
					<input type="submit" value="Aktivieren">
				</fieldset>	-->
<?php
				break ;
			case	7	:
?>
				<fieldset>
					<input type="hidden" name="_action" value="activateMZ" />
					<input type="hidden" name="_IMerkzettelUniqueId" value="<?php echo $myTempMerkzettel->MerkzettelUniqueId ; ?>"/>
					<input type="submit" value="<?php echo FTr::tr( "Activate") ; ?>" class="buttonSearch" />
				</fieldset>
<?php
				break ;
			}
?>
			</form>
			</td>
			<td class="gmz">
			<form class="gmz" method="post" action="/GespeicherteMerkzettel.php">
				<fieldset>
					<input type="hidden" name="_action" value="removeMZ" />
					<input type="hidden" name="_IMerkzettelUniqueId" value="<?php echo $myTempMerkzettel->MerkzettelUniqueId ; ?>"/>
					<input type="submit" value="<?php echo FTr::tr( "Delete") ; ?>" class="buttonSearch" />
				</fieldset>
			</form>
			</td>
<?php
			if ( $myTempMerkzettel->Status == 1) {
?>

			<td class="gmz">
<!-- 			<form class="gmz" method="post" action="/showMZ.php" target="pdfwin"> 	-->
			<form class="gmz" method="post" action="/rsrc/htmlMan/showMZ.php" target="pdfwin">
				<fieldset>
					<input type="hidden" name="_action" value="showMZasPDF" />
					<input type="hidden" name="_IMerkzettelUniqueId" value="<?php echo $myTempMerkzettel->MerkzettelUniqueId ; ?>"/>
					<input type="submit" value="PDF" class="buttonSearch" />
				</fieldset>
			</form>
			</td>
<?php
			}
?>
			</tr>
			</table>
<?php
		}
?>
			</td>
<?php
		echo "</tr>\n" ;
		//
	}
	//
?>
</tbody></table>
</fieldset>
