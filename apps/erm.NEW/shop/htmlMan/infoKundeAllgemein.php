<h1><?php echo FTr::tr( "Customer Data") ; ?></h1>
<form method="post" action="/AllgemeineDaten.php">
<fieldset>
<legend><b><?php echo FTr::tr( "Organisation") ; ?>:</b></legend>
	<input type="hidden" name="_action" value="updCustomer" />
	<table>
		<tr>
		<td><?php echo FTr::tr( "Customer No.") ; ?>:</td>
			<td><?php echo $myCustomer->CustomerNo ; ?></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Company/School/University") ; ?>:</td>
			<td><input name="_IFirmaName1" value="<?php echo $myCustomer->FirmaName1 ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "more Company") ; ?>:</td>
			<td><input name="_IFirmaName2" value="<?php echo $myCustomer->FirmaName2 ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Street / No.") ; ?>:</td>
			<td><input name="_IStrasse" value="<?php echo $myCustomer->Strasse ; ?>" class="inputBasic" /></td>
			<td align="left"><input name="_IHausnummer" value="<?php echo $myCustomer->Hausnummer ; ?>" class="inputBasic" /></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "ZIP / City") ; ?>:</td>
			<td><input name="_IPLZ" value="<?php echo $myCustomer->PLZ ; ?>" class="inputBasic" /></td>
			<td align="left"><input name="_IOrt" value="<?php echo $myCustomer->Ort ; ?>" class="inputBasic" /></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Country") ; ?>:</td>
			<td>
<!--
			<input name="_ILand" value="<?php echo $myCustomer->Land ; ?>"/>
-->
			<?php	echo Option::optionRet( Option::getRLaender(), $myCustomer->Land, "_ILand") ;	?>
			</td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Phone") ; ?>:</td>
			<td><input name="_ITelefon" value="<?php echo $myCustomer->Telefon ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Fax") ; ?>:</td>
			<td><input name="_IFAX" value="<?php echo $myCustomer->FAX ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Cellphone") ; ?>:</td>
			<td><input name="_IMobil" value="<?php echo $myCustomer->Mobil ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "E-Mail") ; ?>:</td>
			<td><input name="_IeMail" value="<?php echo $myCustomer->eMail ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
	</table>
<?php
	if ( isset( $_POST[ '_neuCustomer'])) {
	} else {
?>
	<input type="submit" value="<?php echo FTr::tr( "Update Customer Data") ; ?>" class="buttonBasic" />
<?php
	}
?>
</fieldset>
</form>
<form method="post" action="/AllgemeineDaten.php">
<fieldset>
<legend><b><?php echo FTr::tr( "Customer Contact") ; ?>:</b></legend>
	<input type="hidden" name="_action" value="fncKontakt" />
	<input type="hidden" name="_IIId" value="<?php echo $myCustomerContact->Id ; ?>"/>
	<table>
		<tr>
		<td><?php echo FTr::tr( "Customer No.") ; ?>:</td>
			<td><?php echo $myCustomerContact->CustomerNo ; ?></td>
			<td></td>
		</tr>
<?php
//		<tr>
//			<td><?php echo FTr::tr( "Customernkontaktnummer:</td>
//			<td>000</td>
//			<td></td>
//		</tr>
?>
		<tr>
		<td><?php echo FTr::tr( "Educ. Department") ; ?>:</td>
			<td>
			<?php	echo Option::optionRet( CustomerContact::getRFunktion(), $myCustomerContact->Funktion, "_IIFunktion") ;	?>
			</td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Addition") ; ?>:</td>
			<td>
				<input name="_IIAdrZusatz" value="<?php echo $myCustomerContact->AdrZusatz ; ?>" class="inputBasic" />
			</td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Salutation") ; ?>:</td>
			<td>
			<?php	echo Option::optionRet( Option::getRAnreden(), $myCustomerContact->Anrede, "_IIAnrede") ;	?>
			</td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Title") ; ?>:</td>
			<td>
			<?php	echo Option::optionRet( Option::getRTitel(), $myCustomerContact->Titel, "_IITitel") ;	?>
			</td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "First (given) name") ; ?>:</td>
			<td><input name="_IIVorname" value="<?php echo $myCustomerContact->Vorname ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Last name") ; ?>:</td>
			<td><input name="_IIName" value="<?php echo $myCustomerContact->Name ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Phone") ; ?>:</td>
			<td><input name="_IITelefon" value="<?php echo $myCustomerContact->Telefon ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Fax") ; ?>:</td>
			<td><input name="_IIFAX" value="<?php echo $myCustomerContact->FAX ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Cellphone") ; ?>:</td>
			<td><input name="_IIMobil" value="<?php echo $myCustomerContact->Mobil ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "E-Mail") ; ?>:</td>
			<td><input name="_IIeMail" value="<?php echo $myCustomerContact->eMail ; ?>" class="inputBasic" /></td>
			<td></td>
		</tr>
	</table>
<?php
	if ( isset( $_POST[ '_neuCustomer'])) {
	} else {
?>
	<input type="submit" name="_upd" value="<?php echo FTr::tr( "Update Customer Contact") ; ?>" class="buttonBasic" />
<?php
	}
?>
</fieldset>
</form>
