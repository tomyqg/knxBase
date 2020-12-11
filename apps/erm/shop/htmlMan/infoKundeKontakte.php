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

<h1><?php echo FTr::tr( "Customer Contact Data") ; ?></h1>
<?php
	if ( strcmp($myKundeKontakt->KundeKontaktNr, "000") == 0) {
?>
<fieldset>
<legend><?php echo FTr::tr( "Customer") ; ?>:</legend>
<table>
	<tr>
		<th><?php echo FTr::tr( "Customer Contact No.") ; ?>:</th>
		<th width="40pt"><?php echo FTr::tr( "Anrede") ; ?>:</th>
		<th><?php echo FTr::tr( "Title/First/Last") ; ?>:</th>
		<th><?php echo FTr::tr( "Phone") ; ?>:</th>
	</tr>
<?php
	$query	=	"select * " ;
	$query	.=	"from KundeKontakt " ;
	$query	.=	"where KundeNr = '" . $myKunde->KundeNr . "' " ;
	$query	.=	"order by KundeKontaktNr " ;
	$sqlResult      =       FDb::query( $query) ;
	if ( !$sqlResult) {
		echo $query . " \n" ;
		printf( "001: Probleme mit query ... \n") ;
		die() ;
	}
	$numrows        =       FDb::rowCount() ;
	$myKundeKontakt	=	new KundeKontakt() ;
	$myTab	=	0 ;
	while ($row = mysql_fetch_assoc( $sqlResult)) {
		//
		$myKundeKontakt->assignFromRow( $row) ;
		//
?>
	<form method="post" action="/kundeKontakte.php">
		<tr>
			<input type="hidden" name="_action" value="fncKontakt" />
			<input type="hidden" name="_IIId" value="<?php echo $myKundeKontakt->Id ; ?>"/>
			<td><input size="3" maxlength="3" readonly name="_IIKundeKontaktNr" value="<?php echo $myKundeKontakt->KundeKontaktNr ; ?>"/></td>
			<td><input tabindex="<?php echo $myTab+1 ; ?>" name="_IIAnrede" value="<?php echo $myKundeKontakt->Anrede ; ?>"/></td>
			<td><input tabindex="<?php echo $myTab+2 ; ?>" name="_IITitel" value="<?php echo $myKundeKontakt->Titel ; ?>"/></td>
			<td><input tabindex="<?php echo $myTab+5 ; ?>" name="_IITelefon" value="<?php echo $myKundeKontakt->Telefon ; ?>"/></td>
			<td rowspan="2">
				<input type="hidden" name="_IIId" value="<?php echo $myKundeKontakt->Id ; ?>"/>
<?php
		if ( strcmp( $myKundeKontakt->KundeKontaktNr, "000") != 0) {
?>
	<input type="submit" tabindex="<?php echo $myTab+10 ; ?>" name="_del" value="<?php echo FTr::tr( "Delete") ; ?>"/>
<?php
		}
?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td><input tabindex="<?php echo $myTab+3 ; ?>" name="_IIVorname" value="<?php echo $myKundeKontakt->Vorname ; ?>"/></td>
			<td><input tabindex="<?php echo $myTab+6 ; ?>" name="_IIFAX" value="<?php echo $myKundeKontakt->FAX ; ?>"/></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td><input tabindex="<?php echo $myTab+4 ; ?>" name="_IIName" value="<?php echo $myKundeKontakt->Name ; ?>"/></td>
			<td><input tabindex="<?php echo $myTab+7 ; ?>" name="_IIMobil" value="<?php echo $myKundeKontakt->Mobil ; ?>"/></td>
			<td rowspan="2"><input type="submit" tabindex="<?php echo $myTab+9 ; ?>" name="_upd" value="<?php echo FTr::tr( "Update") ; ?>"/></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td><input tabindex="<?php echo $myTab+8 ; ?>" name="_IIeMail" value="<?php echo $myKundeKontakt->eMail ; ?>"/></td>
		</tr>
		<tr>
			<td colspan="2">Neues Password (2 mal zur Kontrolle):</td>
			<td><input type="password" name="_INewPassword1" value="" /></td>
			<td><input type="password" name="_INewPassword2" value="" /></td>
		</tr>
	</form>
<?php
		//
		$myTab	+=	20 ;
	}
	//
?>
	<form method="post" action="/kundeKontakte.php">
		<tr>
			<input type="hidden" name="_action" value="insKontakt" />
			<td></td>
			<td>
				<select tabindex="<?php echo $myTab+1 ; ?>" name="_IIAnrede" size="1" value="">
					<option selected>Herr</option>
					<option>Frau</option>
					<option>Fr&auml;ulein</option>
				</select>
			</td>
			<td>
				<select tabindex="<?php echo $myTab+2 ; ?>" name="_IITitel" size="1" value="">
					<option selected />
					<option>Dr.</option>
					<option>Dr.-Ing.</option>
					<option>Dr.-Med.</option>
				</select>
			</td>
			<td><input tabindex="<?php echo $myTab+5 ; ?>" name="_IITelefon" value=""/></td>
			<td rowspan="4"><input type="submit" tabindex="<?php echo $myTab+9 ; ?>" value="Einf&uuml;gen"/></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td><input tabindex="<?php echo $myTab+3 ; ?>" name="_IIVorname" value=""/></td>
			<td><input tabindex="<?php echo $myTab+6 ; ?>" name="_IIFAX" value=""/></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td><input tabindex="<?php echo $myTab+4 ; ?>" name="_IIName" value=""/></td>
			<td><input tabindex="<?php echo $myTab+7 ; ?>" name="_IIMobil" value=""/></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td><input tabindex="<?php echo $myTab+8 ; ?>" name="_IIeMail" value=""/></td>
		</tr>
		<tr>
			<td colspan="2">Neues Password (2 mal zur Kontrolle):</td>
			<td><input type="password" name="_INewPassword1" value="" /></td>
			<td><input type="password" name="_INewPassword2" value="" /></td>
		</tr>
	</form>
</table>
</fieldset>
<?php
	} else {
		echo FTr::tr( "AVAILABILITY CUSTOMER CONTACT SCREEN") ;
//Die Funktion der "Kunden Kontakte" steht ausschliesslich dem Hauptkontakt des Kunden zur Verf&uuml;gung.
	}
?>
