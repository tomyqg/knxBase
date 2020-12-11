<h1><?php echo FTr::tr( "Customer Data") ; ?></h1>
<fieldset>
	<legend><b><?php echo FTr::tr( "Organisation") ; ?>:</b></legend>
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
<form method="post" action="/Passwort.php">
<fieldset>
<legend><b><?php echo FTr::tr( "Passwort") ; ?>:</b></legend>
	<input type="hidden" name="_action" value="chgPasswort" />
	<table>
		<tr>
		<td><?php echo FTr::tr( "Old Password") ; ?>:</td>
			<td><input type="password" name="_IOldPassword" value="" class="inputBasic" /></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "New Password") ; ?>:</td>
			<td><input type="password" name="_INewPassword1" value="" class="inputBasic" /></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "New Password (Repeat)") ; ?>:</td>
			<td><input type="password" name="_INewPassword2" value="" class="inputBasic" /></td>
		</tr>
	</table>
	<input type="submit" value="<?php echo FTr::tr( "Change password") ; ?>" class="buttonBasic" />
</fieldset>
</form>
