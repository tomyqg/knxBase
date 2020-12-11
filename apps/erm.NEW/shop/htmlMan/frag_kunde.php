<script>
function	showB2B() {
	divb2c	=	document.getElementById( "B2C") ;
	divb2c.style.display	=	'none' ;
	divb2b	=	document.getElementById( "B2B") ;
	divb2b.style.display	=	'block' ;
}
function	showB2C() {
	divb2c	=	document.getElementById( "B2C") ;
	divb2c.style.display	=	'block' ;
	divb2b	=	document.getElementById( "B2B") ;
	divb2b.style.display	=	'none' ;
}
</script>
<?php
	echo "<h1>" . FTr::tr( "Registration") . "</h1>\n" ;
	if ( isset( $_POST['_IPasswort'])) {
		echo FTr::tr( "LOGIN PROBLEM") ;
	}
error_log( "NextFunction := '$nextFunction'") ;
?>
<!--
en:
<h3>The login was not successful</h3>It may be that you have not activated your account after a new registration or that you did not enter your password correctly.<br/>Please try again.
de:
<h3>Anmeldung war nicht erfolgreich</h3>Das kann daranliegen, dass Sie entweder Ihren Account nach einer Neuanmeldung noch nicht aktiviert haben, oder dass Sie sich bei Ihrem Passwort vertippt haben.<br /> Bitte versuchen Sie es erneut.
-->
<form method="post" action="<?php echo $nextFunction ; ?>">
<fieldset>
<legend><b><?php echo FTr::tr( "Customer login") ; ?></b></legend>
	<input name="_IFunktion" type="hidden" value="bestellen"/>
	<table>
		<tr>
		<td><?php echo FTr::tr( "Customer no. or E-Mail") ; ?>:</td>
			<td><input name="_IKundeNr" class="inputBasic" /></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Password") ; ?>:</td>
			<td><input type="password" name="_IPasswort" class="inputBasic"/></td>
		</tr>
	</table>
	<input type="submit" class="buttonBasic" value="<?php echo FTr::tr( "Register & continue with step 2") ; ?>" ;>
</fieldset>
</form>
<br/>
<form method="post" action="<?php echo $nextFunction ; ?>">
<fieldset>
<?php echo FTr::tr( "INFO NEW PASSWORD") ; ?>
<!--
en:
This website stores passwords only in MD5 encoded version (read: unreadable for humans), no exceptions! Therefor we can <b>NOT</b> return your existing password but only provide a <b>NEW</b> password.<br/>
de:
Passw&ouml;rter werden auf dieser WebSite ausschliesslich in verschl&uuml;sselter Form gespeichert. Daher k&ouml;nnen wir Ihnen lediglich ein NEUES Passwort erzeugen (und nicht das existierende zusenden).<br/>
-->
		<legend><b><?php echo FTr::tr( "Request new password") ; ?></b></legend>
	<input name="_neuesPasswort" type="hidden"/>
	<table>
		<tr>
		<td><?php echo FTr::tr( "Customer no. or E-Mail") ; ?>:</td>
			<td><input name="_IKundeNr" class="inputBasic"/></td>
		</tr>
	</table>
	<input type="submit" class="buttonBasic" value="<?php echo FTr::tr( "Request new password") ; ?>">
</fieldset>
</form>
<br />
<?php
	if ( isset( $neuKundeFehler)) {
		switch ( $neuKundeFehler) {
		case	1	:
?>
			<h3>Die von Ihnen angegebene E-Mail Adresses kann leider nicht akzeptiert werden.</h3>
<?php
			break ;
		case	2	:
?>
			<h3>Ihre Daten konnten leider aufgrund eines internen Fehlers nicht registriert werden.</h3>
<?php
			break ;
		case	4	:
?>
			<h3>Die beiden E-Mail Adressen unterscheiden sich.</h3>
<?php
			break ;
		case	5	:
?>
			<h3>Diese E-Mail Adresse ist bereits registriert. Bitte fordern Sie ein neues Passwort an.</h3>
<?php
			break ;
		case	6	:
?>
			<h3>Die von Ihnen genannte E-Mail Adresse ist bereits registriert. Fordern Sie bitte ein neues Passwort an.</h3>
<?php
			break ;
		case	11	:
?>
			<h3>Der von Ihnen eingegebe Name f&uuml;r Firma/Schule/Universit&auml;t ist nicht g&uuml;ltig.</h3>
<?php
			break ;
		case	12	:
?>
			<h3>Die von Ihnen eingegebene Strasse ist nicht g&uuml;ltig.</h3>
<?php
			break ;
		case	13	:
?>
			<h3>Die von Ihnen eingegebene PLZ ist nicht g&uuml;ltig.</h3>
<?php
			break ;
		case	14	:
?>
			<h3>Der von Ihnen eingegebene Ort ist nicht g&uuml;ltig.</h3>
<?php
			break ;
		case	21	:
?>
			<h3>Der von Ihnen eingegebene Name des Ansprechpartners ist nicht g&uuml;ltig.</h3>
<?php
			break ;
		case	31	:
?>
			<h3>Die angegebene Umsatzsteuerident Nr. ist nicht g&uuml;ltig.</h3>
<?php
			break ;
		}
?>
		<br/>Bitte versuchen Sie es erneut.<br/>
<?php
	}
?>
<!--	<form method="post" action="<?php echo $nextFunction ; ?>">		-->
<form method="post" action="/AllgemeineDaten.php">
<fieldset>
<legend><b><?php echo FTr::tr( "New registration") ; ?></b></legend>
<?php echo FTr::tr( "INFO NEW REGISTRATION") ; ?>
<?php
if ( isset( $_POST['_ITypeCust'])) {
	$bMode	=	intval( $_POST['_ITypeCust']) ;
} else {
	$bMode	=	0 ;
}
?>
<!--
-->
<?php if ( $bMode == 1) {		?>
<input type="radio" name="_ITypeCust" value="0" onclick="showB2C() ; "><?php echo FTr::tr( "Individual") ; ?></input><br/>
<input type="radio" name="_ITypeCust" value="1" checked onclick="showB2B() ; "><?php echo FTr::tr( "Company/School/University") ; ?></input>
<div id="B2B" style="display:block;">
<?php } else {		?>
<input type="radio" name="_ITypeCust" value="0" checked onclick="showB2C() ; "><?php echo FTr::tr( "Individual") ; ?></input><br/>
<input type="radio" name="_ITypeCust" value="1" onclick="showB2B() ; "><?php echo FTr::tr( "Company/School/University") ; ?></input>
<div id="B2B" style="display:none;">
<?php }						?>
	<table>
		<tr>
		<td>
	<fieldset>
	<legend><b>Organisation</b></legend>
	<input name="_neuKunde" type="hidden"/>
		<table>
		<tr>
		<td><?php echo FTr::Tr( "Company/School/University") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 11) {		?>
			<td class="r18">*</td>
			<td><input name="_IFirmaName1" class="inputFalse" value="<?php if ( isset ( $_POST['_IFirmaName1'])) echo $_POST['_IFirmaName1'] ; ?>" /></td>
<?php	} else {													?>
			<td>*</td>
			<td><input name="_IFirmaName1" class="inputBasic" value="<?php if ( isset ( $_POST['_IFirmaName1'])) echo $_POST['_IFirmaName1'] ; ?>" /></td>
<?php	}															?>
			<td></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "more Company") ; ?>:</td>
			<td></td>
			<td><input name="_IFirmaName2" class="inputBasic" value="<?php if ( isset ( $_POST['_IFirmaName2'])) echo $_POST['_IFirmaName2'] ; ?>" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Street / No.") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 12) {	?>
			<td class="r18">*</td>
			<td><input name="_IStrasse" class="inputFalse" value="<?php if ( isset ( $_POST['_IStrasse'])) echo $_POST['_IStrasse'] ; ?>" /></td>
<?php	} else {													?>
			<td>*</td>
			<td><input name="_IStrasse" class="inputBasic" value="<?php if ( isset ( $_POST['_IStrasse'])) echo $_POST['_IStrasse'] ; ?>" /></td>
<?php	}															?>
			<td></td>
			<td align="left"><input size="5" name="_IHausnummer" class="inputBasic" value="<?php if ( isset ( $_POST['_IHausnummer'])) echo $_POST['_IHausnummer'] ; ?>" /></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "ZIP / City") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 13) {	?>
			<td class="r18">*</td>
			<td><input name="_IPLZ" size="8" class="inputFalse" value="<?php if ( isset ( $_POST['_IPLZ'])) echo $_POST['_IPLZ'] ; ?>" /></td>
<?php	} else {													?>
			<td>*</td>
			<td><input name="_IPLZ" size="8" class="inputBasic" value="<?php if ( isset ( $_POST['_IPLZ'])) echo $_POST['_IPLZ'] ; ?>" /></td>
<?php	}															?>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 14) {	?>
			<td class="r18">*</td>
			<td align="left"><input name="_IOrt" class="inputFalse" value="<?php if ( isset ( $_POST['_IOrt'])) echo $_POST['_IOrt'] ; ?>" /></td>
<?php	} else {													?>
			<td>*</td>
			<td align="left"><input name="_IOrt" class="inputBasic" value="<?php if ( isset ( $_POST['_IOrt'])) echo $_POST['_IOrt'] ; ?>" /></td>
<?php	}															?>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Country") ; ?>:</td>
			<td></td>
			<td>
<?php
			echo Option::optionRet( Option::getRLaender(), "de", "_ILand") ;
?>
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Tax Id.") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 31) {	?>
			<td class="r18">*</td>
			<td><input name="_IUStId" class="inputFalse" value="<?php if ( isset ( $_POST['_IUStId'])) echo $_POST['_IUStId'] ; ?>" /></td>
<?php	} else {													?>
			<td></td>
			<td><input name="_IUStId" class="inputBasic" value="<?php if ( isset ( $_POST['_IUStId'])) echo $_POST['_IUStId'] ; ?>" /></td>
<?php	}															?>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Organisation") ; ?>:</td>
			<td></td>
			<td>
<?php
			echo Option::optionRet( Option::getROrgType(), "Sonstige", "_IOrgType") ;
?>
			</td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Phone") ; ?>:</td>
			<td></td>
			<td><input name="_ITelefon" class="inputBasic" value="<?php if ( isset ( $_POST['_ITelefon'])) echo $_POST['_ITelefon'] ; ?>" /></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Fax") ; ?>:</td>
			<td></td>
			<td><input name="_IFAX" class="inputBasic" value="<?php if ( isset ( $_POST['_IFAX'])) echo $_POST['_IFAX'] ; ?>" /></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Cellphone") ; ?>:</td>
			<td></td>
			<td><input name="_IMobil" class="inputBasic" value="<?php if ( isset ( $_POST['_IMobil'])) echo $_POST['_IMobil'] ; ?>" /></td>
		</tr>
		</table>
	</fieldset></td>
	<td>
	<fieldset>
	<legend><b>Ansprechpartner</b></legend>
	<table>
		<tr>
		<td><?php echo FTr::tr( "Anrede") ; ?>:</td>
			<td></td>
			<td>
<?php
			if ( isset( $_POST['_IIAnrede'])) {
				Option::optionListe( Option::getRAnreden(), $_POST['_IIAnrede'], "_IIAnrede") ;
			} else {
				Option::optionListe( Option::getRAnreden(), "Frau", "_IIAnrede") ;
			}
?>
<!--				<input name="_IIAnrede" size="8" class="inputBasic" value="<?php if ( isset ( $_POST['_IIAnrede'])) echo $_POST['_IIAnrede'] ; ?>" />	-->
				</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Title") ; ?>:</td>
			<td></td>
			<td>
<?php
			if ( isset( $_POST['_IITitel'])) {
				Option::optionListe( Option::getRTitel(), $_POST['_IITitel'], "_IITitel") ;
			} else {
				Option::optionListe( Option::getRTitel(), "", "_IITitel") ;
			}
?>
<!--				<input name="_IITitel" size="8" class="inputBasic" value="<?php if ( isset ( $_POST['_IITitel'])) echo $_POST['_IITitel'] ; ?>" />	-->
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "First (given) Name") ; ?>:</td>
			<td></td>
			<td><input name="_IIVorname" class="inputBasic" value="<?php if ( isset ( $_POST['_IIVorname'])) echo $_POST['_IIVorname'] ; ?>" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Last Name") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 21) {	?>
			<td class="r18">*</td>
			<td><input name="_IIName" class="inputFalse" value="<?php if ( isset ( $_POST['_IIName'])) echo $_POST['_IIName'] ; ?>" /></td>
<?php	} else {													?>
			<td>*</td>
			<td><input name="_IIName" class="inputBasic" value="<?php if ( isset ( $_POST['_IIName'])) echo $_POST['_IIName'] ; ?>" /></td>
<?php	}															?>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Fachbereich") ; ?>:</td>
			<td></td>
			<td><input name="_IIAdrZusatz" class="inputBasic" value="<?php if ( isset ( $_POST['_IIAdrZusatz'])) echo $_POST['_IIAdrZusatz'] ; ?>" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Phone") ; ?>:</td>
			<td></td>
			<td><input name="_IITelefon" class="inputBasic" value="<?php if ( isset ( $_POST['_IITelefon'])) echo $_POST['_IITelefon'] ; ?>" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Fax") ; ?>:</td>
			<td></td>
			<td><input name="_IIFAX" class="inputBasic" value="<?php if ( isset ( $_POST['_IIFAX'])) echo $_POST['_IIFAX'] ; ?>" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Cellphone") ; ?>:</td>
			<td></td>
			<td><input name="_IIMobil" class="inputBasic" value="<?php if ( isset ( $_POST['_IIMobil'])) echo $_POST['_IIMobil'] ; ?>" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "E-Mail") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && ( $neuKundeFehler == 1 || $neuKundeFehler == 4 || $neuKundeFehler == 5)) {	?>
			<td class="r18">*</td>
			<td><input name="_IIeMail" class="inputFalse" value="<?php if ( isset ( $_POST['_IIeMail'])) echo $_POST['_IIeMail'] ; ?>" /></td>
<?php	} else {			?>
			<td>*</td>
			<td><input name="_IIeMail" class="inputBasic" value="<?php if ( isset ( $_POST['_IIeMail'])) echo $_POST['_IIeMail'] ; ?>" /></td>
<?php }						?>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "E-Mail (confirm)") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && ( $neuKundeFehler == 4 || $neuKundeFehler == 5)) {		?>
			<td class="r18">*</td>
			<td><input name="_IIIeMail" class="inputFalse" value="<?php if ( isset ( $_POST['_IIIeMail'])) echo $_POST['_IIIeMail'] ; ?>" /></td>
<?php	} else {			?>
			<td>*</td>
			<td><input name="_IIIeMail" class="inputBasic" value="<?php if ( isset ( $_POST['_IIIeMail'])) echo $_POST['_IIIeMail'] ; ?>" /></td>
<?php }						?>
			<td></td>
		</tr>
	</table>
	</fieldset></td></tr>
	</table>
</div>
<?php if ( $bMode == 1) {		?>
<div id="B2C" style="display:none;">
<?php } else {		?>
<div id="B2C" style="display:block;">
<?php }						?>
	<fieldset>
	<legend><b><?php echo FTr::tr( "Individual") ; ?></b></legend>
	<input type="hidden" name="_IOrgType" value="Privat" />
	<input name="_neuKunde" type="hidden"/>
		<table>
		<tr>
			<td><?php echo FTr::tr( "Anrede") ; ?>:</td>
			<td></td>
			<td>
<?php
			if ( isset( $_POST['_IB2CAnrede'])) {
				Option::optionListe( Option::getRAnreden(), $_POST['_IB2CAnrede'], "_IB2CAnrede") ;
			} else {
				Option::optionListe( Option::getRAnreden(), "Frau", "_IB2CAnrede") ;
			}
?>
<!--				<input name="_IB2CAnrede" class="select" size="8" value="<?php if ( isset ( $_POST['_IB2CAnrede'])) echo $_POST['_IB2CAnrede'] ; ?>" />	-->
			</td>
			<td colspan="3">Bitte richtig ausw&auml;hlen, sonst werden Sie ggf. verkehrt angesprochen !</td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Title") ; ?>:</td>
			<td></td>
			<td>
<?php
			if ( isset( $_POST['_IB2CTitel'])) {
				Option::optionListe( Option::getRTitel(), $_POST['_IB2CTitel'], "_IB2CTitel") ;
			} else {
				Option::optionListe( Option::getRTitel(), "", "_IB2CTitel") ;
			}
?>
<!--				<input name="_IB2CTitel" class="select" size="8" value="<?php if ( isset ( $_POST['_IB2CTitel'])) echo $_POST['_IB2CTitel'] ; ?>" />	-->
			</td>
			<td colspan="3">Ehre wem Ehre geb&uuml;hrt</td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Last Name") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 21) {	?>
			<td class="r18">*</td>
			<td><input name="_IB2CFirmaName1" class="inputFalse" value="<?php if ( isset ( $_POST['_IB2CFirmaName1'])) echo $_POST['_IB2CFirmaName1'] ; ?>" />
			</td>
<?php	} else {													?>
			<td>*</td>
			<td><input name="_IB2CFirmaName1" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CFirmaName1'])) echo $_POST['_IB2CFirmaName1'] ; ?>" />
			</td>
<?php 	}															?>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "First (given) Name") ; ?>:</td>
			<td></td>
			<td><input name="_IB2CFirmaName2" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CFirmaName2'])) echo $_POST['_IB2CFirmaName2'] ; ?>" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Street / No.") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 12) {	?>
			<td class="r18">*</td>
			<td><input name="_IB2CStrasse" class="inputFalse" value="<?php if ( isset ( $_POST['_IB2CStrasse'])) echo $_POST['_IB2CStrasse'] ; ?>" /></td>
<?php	} else {													?>
			<td>*</td>
			<td><input name="_IB2CStrasse" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CStrasse'])) echo $_POST['_IB2CStrasse'] ; ?>" /></td>
<?php	}															?>
			<td></td>
			<td><input size="5" name="_IB2CHausnummer" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CHausnummer'])) echo $_POST['_IB2CHausnummer'] ; ?>" /></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "ZIP / City") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 13) {	?>
			<td class="r18">*</td>
			<td><input name="_IB2CPLZ" size="8" class="inputFalse" value="<?php if ( isset ( $_POST['_IB2CPLZ'])) echo $_POST['_IB2CPLZ'] ; ?>" /></td>
<?php	} else {													?>
			<td>*</td>
			<td><input name="_IB2CPLZ" size="8" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CPLZ'])) echo $_POST['_IB2CPLZ'] ; ?>" /></td>
<?php	}															?>
<?php	if ( isset( $neuKundeFehler) && $neuKundeFehler == 14) {	?>
			<td class="r18">*</td>
			<td><input name="_IB2COrt" class="inputFalse" value="<?php if ( isset ( $_POST['_IB2COrt'])) echo $_POST['_IB2COrt'] ; ?>" /></td>
<?php	} else {													?>
			<td>*</td>
			<td><input name="_IB2COrt" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2COrt'])) echo $_POST['_IB2COrt'] ; ?>" /></td>
<?php	}															?>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Country") ; ?>:</td>
			<td></td>
			<td>
<?php
			echo Option::optionRet( Option::getRLaender(), "de", "_IB2CLand") ;
?>
			</td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Phone") ; ?>:</td>
			<td></td>
			<td><input name="_IB2CTelefon" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CTelefon'])) echo $_POST['_IB2CTelefon'] ; ?>" /></td>
			<td colspan="3">Wichtig im Falle von R&uuml;ckfragen</td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Fax") ; ?>:</td>
			<td></td>
			<td><input name="_IB2CFAX" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CFAX'])) echo $_POST['_IB2CFAX'] ; ?>" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Cellphone") ; ?>:</td>
			<td></td>
			<td><input name="_IB2CMobil" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CMobil'])) echo $_POST['_IB2CMobil'] ; ?>" /></td>
			<td colspan="3">Wichtig im Falle von R&uuml;ckfragen</td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "E-Mail") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && ( $neuKundeFehler == 1 || $neuKundeFehler == 4 || $neuKundeFehler == 5)) {	?>
			<td class="r18">*</td>
			<td><input name="_IB2CeMail" class="inputFalse" value="<?php if ( isset ( $_POST['_IB2CeMail'])) echo $_POST['_IB2CeMail'] ; ?>" /></td>
<?php	} else {										?>
			<td>*</td>
			<td><input name="_IB2CeMail" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CeMail'])) echo $_POST['_IB2CeMail'] ; ?>" /></td>
<?php	}												?>
			<td rowspan="2" colspan="3"><?php echo FTr::tr( "EMAILS MUST MATCH") ; ?></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "E-Mail (confirm)") ; ?>:</td>
<?php	if ( isset( $neuKundeFehler) && ( $neuKundeFehler == 4 || $neuKundeFehler == 5)) {		?>
			<td class="r18">*</td>
			<td><input name="_IB2CeMailV" class="inputFalse" value="<?php if ( isset ( $_POST['_IB2CeMailV'])) echo $_POST['_IB2CeMailV'] ; ?>" /></td>
<?php	} else {										?>
			<td>*</td>
			<td><input name="_IB2CeMailV" class="inputBasic" value="<?php if ( isset ( $_POST['_IB2CeMailV'])) echo $_POST['_IB2CeMailV'] ; ?>" /></td>
<?php	}												?>
		</table>
	</fieldset>
</div>
<input type="submit" class="buttonBasic" value="<?php echo FTr::tr( "Register & continue with step 2") ; ?>">
</fieldset>
</form>
