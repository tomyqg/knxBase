<!--# <br/> 						-->
<!--# PHP update mask for PHP class Kunde <br/> 		-->
<!--# <br/> 						-->
<script defer language="JavaScript" src="/javascript/Kunde.sel.js" type="text/javascript"> 
</script> 
<?php	$actKunde	=	$acpCuOrdr->getKunde() ;
		$actKundeKontakt	=	$acpCuOrdr->getKundeKontakt() ;
	if ( $actKunde !== NULL) {						?>
	<fieldset>
	<legend>Kunde</legend>
	<table> 
		<tr> 
			<td>Kunden Nr.</td>
			<td>Firmen Name 1</td>
			<td>Firmen Name 2</td>
		</tr> 
		<tr> 
			<td><input readonly name="_IKundeNr" type="text" value="<?php echo $actKunde->KundeNr ; ?>"></input></td>
			<td><input readonly name="_IFirmaName1" size="32" maxlength="32" type="text" value="<?php echo $actKunde->FirmaName1 ; ?>"></input></td>
			<td><input readonly name="_IFirmaName2" size="32" maxlength="32" type="text" value="<?php echo $actKunde->FirmaName2 ; ?>"></input></td>
		</tr> 
		<tr> 
		</tr> 
		<tr> 
			<td>PLZ</td>
			<td>Ort</td>
			<td>Strasse</td>
			<td>Hausnummer</td>
		</tr> 
		<tr> 
			<td><input readonly name="_IPLZ" size="8" maxlength="8" type="text" value="<?php echo $actKunde->PLZ ; ?>"></input></td>
			<td><input readonly name="_IOrt" size="32" maxlength="32" type="text" value="<?php echo $actKunde->Ort ; ?>"></input></td>
			<td><input readonly name="_IStrasse" size="32" maxlength="32" type="text" value="<?php echo $actKunde->Strasse ; ?>"></input></td>
			<td><input readonly name="_IHausnummer" size="6" maxlength="6" type="text" value="<?php echo $actKunde->Hausnummer ; ?>"></input></td>
		</tr> 
		<tr> 
			<td>Land</td>
			<td><input readonly name="_ILand" size="32" maxlength="32" type="text" value="<?php echo $actKunde->Land ; ?>"></input></td>
			<td><input readonly name="_ISprache" size="6" maxlength="6" type="text" value="<?php echo $actKunde->Sprache ; ?>"></input></td>
		</tr> 
		<tr> 
			<td>Telefon</td>
			<td>Fax</td>
			<td>Mobil Telefon</td>
		</tr> 
		<tr> 
			<td><input readonly name="_ITelefon" size="20" maxlength="32" type="text" value="<?php echo str_replace( " ", "", $actKunde->Telefon) ; ?>" /></td>
			<td><input readonly name="_IFAX" size="20" maxlength="32" type="text" value="<?php echo str_replace( " ", "", $actKunde->FAX) ; ?>" /></td>
			<td><input readonly name="_IMobil" size="20" maxlength="32" type="text" value="<?php echo $actKunde->Mobil ; ?>" /></td>
		</tr> 
		<tr> 
			<td>eMail Adresse</td>
			<td colspan="2"><input readonly name="_IeMail" size="32" maxlength="32" type="text" value="<?php echo $actKunde->eMail ; ?>" /></td>
		</tr> 
		<tr> 
			<td>Internet Bestellung</td>
			<td>Bestell Bestaetigung</td>
			<td>Liefermodus</td>
			<td>Rechnungsmodus</td>
			<td>Skontomodus</td>
		</tr> 
		<tr> 
			<td><input readonly name="_IModusBestInternet" size="3" maxlength="3" type="text" value="<?php echo $actKunde->ModusBestInternet ; ?>"></input></td>
			<td><input readonly name="_IModusBestConf" size="3" maxlength="3" type="text" value="<?php echo $actKunde->ModusBestConf ; ?>"></input></td>
			<td><input readonly name="_IModusLief" size="3" maxlength="3" type="text" value="<?php echo $actKunde->ModusLief ; ?>"></input></td>
			<td><input readonly name="_IModusRech" size="3" maxlength="3" type="text" value="<?php echo $actKunde->ModusRech ; ?>"></input></td>
			<td><input readonly name="_IModusSkonto" size="3" maxlength="3" type="text" value="<?php echo $actKunde->ModusSkonto ; ?>"></input></td>
		</tr> 
	</table> 
	</fieldset>
	<fieldset>
	<legend>Aktueller Kontakt</legend>
	<table> 
		<tr> 
			<td>Kontakt Nr.</td>
			<td>Adress Zusatz</td>
			<td>Anrede</td>
			<td>Titel</td>
			<td>Vorname</td>
			<td>Name</td>
			<td>Telefon</td>
			<td>Fax</td>
			<td>E-Mail</td>
		</tr> 
		<tr> 
			<td><input readonly name="_IKundeKontaktNr" size="8" maxlength="8" type="text" value="<?php echo $actKundeKontakt->KundeKontaktNr ; ?>"></input></td>
			<td><input readonly name="_IAdrZusatz" size="8" maxlength="8" type="text" value="<?php echo $actKundeKontakt->AdrZusatz ; ?>"></input></td>
			<td><input readonly name="_IAnrede" size="8" maxlength="8" type="text" value="<?php echo $actKundeKontakt->Anrede ; ?>"></input></td>
			<td><input readonly name="_ITitel" size="12" maxlength="12" type="text" value="<?php echo $actKundeKontakt->Titel ; ?>"></input></td>
			<td><input readonly name="_IVorname" size="20" maxlength="20" type="text" value="<?php echo $actKundeKontakt->Vorname ; ?>"></input></td>
			<td><input readonly name="_IName" size="20" maxlength="20" type="text" value="<?php echo $actKundeKontakt->Name ; ?>"></input></td>
			<td><input readonly name="_ITelefon" size="16" maxlength="20" type="text" value="<?php echo $actKundeKontakt->Telefon ; ?>"></input></td>
			<td><input readonly name="_IFAX" size="16" maxlength="20" type="text" value="<?php echo $actKundeKontakt->FAX ; ?>"></input></td>
			<td><input readonly name="_IeMail" size="20" maxlength="40" type="text" value="<?php echo $actKundeKontakt->eMail ; ?>"></input></td>
		</tr> 
	</table> 
	</fieldset>
<?php	}				?>
	<fieldset>
	<legend>Rechungsanschrift
<?php	if ( $acpCuOrdr->Status <= NEU) {	?>
		<input type="image" src="/licon/yellow/24/zoom.png" onclick="return selKundeIdWithPOST( '/CuOrdr/CuOrdrBearb.php', 'CuOrdr', 'CuOrdrMain', 'actionUpdRechAdr', '<?php echo $actKunde->KundeNr ; ?>-R') ;" />
<?php	}									?>
	</legend>
<?php
$actRKunde	=	$acpCuOrdr->getRechKunde() ;
$actRKundeKontakt	=	$acpCuOrdr->getRechKundeKontakt() ;
if ( $actRKunde !== NULL) {
	include( "showRKunde.php") ;
} else {
	echo "wie Auftragsanschrift" ;
}
?>
	</fieldset>
	<fieldset>
		<legend>Lieferanschrift
<?php	if ( $acpCuOrdr->Status <= NEU) {	?>
		<input type="image" src="/licon/yellow/24/zoom.png" onclick="return selKundeLiefAdrByIdWithPOST( '/CuOrdr/CuOrdrBearb.php', 'CuOrdr', 'CuOrdrMain', 'actionUpdLiefAdr', '<?php echo $actKunde->KundeNr ; ?>-L%') ;" />
<?php	}									?>
		</legend>
<?php
$actLKunde	=	$acpCuOrdr->getLiefKunde() ;
$actLKundeKontakt	=	$acpCuOrdr->getLiefKundeKontakt() ;
if ( $actLKunde !== NULL) {
	include( "showLKunde.php") ;
} else {
	echo "wie Auftragsanschrift" ;
}
?>
	</fieldset>
<?php
?>
