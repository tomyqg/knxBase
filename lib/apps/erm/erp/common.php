<?php
/**
 * required include files
 */
require_once( "globalLib.php" );
function	tableBlock( $_dtvObj, $_form) {
?>
<form name="<?php echo $_form ; ?>" id="<?php echo $_form ; ?>" style="margin-top:0px;margin-bottom:0px;">
	<?php	if ( strpos( $_form, "Top") !== false) {		?>
	<input type="hidden" name="_SOrdMode" id="_SOrdMode" value="INoASINoA" />
	<?php	}											?>
	<table style="border:0; width:100%">
		<thead>
		<tr>
			<td align="left"><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1"/>
			<select id="_SRowCOunt" name="_SRowCount">
				<option selected="" value="5">5</option>
				<option selected="true" value="10">10</option>
				<option selected="" value="20">20</option>
			</select></td>
			<td align="right">
				Search:&nbsp;
				<input type="text" name="_ISearch" onkeypress="return <?php echo $_dtvObj ; ?>.search( event, '<?php echo $_form ; ?>') ;"/>
			</td>
			<td align="left"><div class="memu-icon sprite-search" onclick="<?php echo $_dtvObj ; ?>.searchWE( '<?php echo $_form ; ?>') ;"></div></td>
			<td><div class="memu-icon sprite-dleft" onclick="<?php echo $_dtvObj ; ?>.onFirstPage('<?php echo $_form ; ?>') ;"></div>
			<div class="memu-icon sprite-left" onclick="<?php echo $_dtvObj ; ?>.onPreviousPage('<?php echo $_form ; ?>') ;"></div>
			<div class="memu-icon sprite-reload" onclick="<?php echo $_dtvObj ; ?>.onThisPage('<?php echo $_form ; ?>') ;"></div>
			<div class="memu-icon sprite-right" onclick="<?php echo $_dtvObj ; ?>.onNextPage('<?php echo $_form ; ?>') ;"></div>
			<div class="memu-icon sprite-dright" onclick="<?php echo $_dtvObj ; ?>.onLastPage('<?php echo $_form ; ?>') ;"></div></td>
		</tr>
		</thead>
	</table>
</form>
<?php	
}
function	tableBlockBot( $_dtvObj, $_form) {
?>
<form name="<?php echo $_form ; ?>" id="<?php echo $_form ; ?>" style="margin-top:0px;margin-bottom:0px;">
	<?php	if ( strpos( $_form, "Top") !== false) {		?>
	<input type="hidden" name="_SOrdMode" id="_SOrdMode" value="INoASINoA" />
	<?php	}											?>
	<table style="border:0; width:100%">
		<thead>
		<tr>
			<td align="left"><div class="memu-icon sprite-add" onclick="<?php echo $_dtvObj ; ?>.addItem() ;"></div></td>
		</tr>
		</thead>
	</table>
</form>
<?php	
}
function	tableBlockNF( $_dtvObj, $_form) {
?>
	<?php	if ( strpos( $_form, "Top") !== false) {		?>
	<input type="hidden" name="_SOrdMode" id="_SOrdMode" value="INoASINoA" />
	<?php	}											?>
	<center>
	<table style="border:0; width:100%"">
		<tr>
			<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" /></td>
			<td><input type="text" name="_SRowCount" size="2" maxlength="2" value="10" tabindex="1" /></td>
			<td><input type="image" src="/Rsrc/licon/Blue/24/object_12.png" onclick="<?php echo $_dtvObj ; ?>.onFirstPage() ; return false ;" /></td>
			<td><input type="image" src="/Rsrc/licon/Blue/24/left.png" onclick="<?php echo $_dtvObj ; ?>.onPreviousPage() ; return false ;" /></td>
			<td><input type="image" src="/Rsrc/licon/Blue/24/refresh.png" onclick="<?php echo $_dtvObj ; ?>.onThisPage() ; return false ;" /></td>
			<td><input type="image" src="/Rsrc/licon/Blue/24/right.png" onclick="<?php echo $_dtvObj ; ?>.onNextPage() ; return false ;" /></td>
			<td><input type="image" src="/Rsrc/licon/Blue/24/object_13.png" onclick="<?php echo $_dtvObj ; ?>.onLastPage() ; return false ;" /></td>
		</tr>
	</table>
	</center>
<?php	
}
/**
 * required include files
 */
require_once( "globalLib.php" );
/**
 * produces the standard I/O block for customer data
 * @param string JavaScript-code to attach to the customer no. field
 */
function	kundeBlock( $_js="") {
	rowEdit( FTr::tr( "Customer no."), "_DKundeNr", 11, 11, "", FTr::tr( "HELP-CustomerNo"), "", "screenCurrent.showSelCustomer() ;") ;
	rowEditDblBR( FTr::tr( "Company / Institution"), "_IFirmaName1", 48, 64, "", "_IFirmaName2", 48, 64, "", FTr::tr( "HELP-Company")) ;
	rowEdit( FTr::tr( "Addition"), "_IFirmaName3", 48, 64, "", "") ;
	rowEditDbl( FTr::tr( "Street / No."), "_IStrasse", 24, 32, "", "_IHausnummer", 6, 12, "", FTr::tr( "HELP-Street")) ;
	rowEditDbl( FTr::tr( "ZIP / City"), "_IPLZ", 6, 10, "", "_IOrt", 24, 32, "", FTr::tr( "HELP-Address")) ;
	rowOption( FTr::tr( "Country"), "_ILand", Opt::getRLaender(), "de", FTr::tr( "HELP-Country")) ;
	rowEdit( FTr::tr( "Phone"), "_ITelefon", 24, 32, "", FTr::tr( "HELP-Phone")) ;
	rowEdit( FTr::tr( "FAX"), "_IFAX", 24, 32, "", FTr::tr( "HELP-FAX")) ;
	rowEdit( FTr::tr( "E-Mail"), "_IeMail", 32, 64, "", FTr::tr( "HELP-EMail")) ;
}
/**
 * produces the standard I/O block for customer contact data
 * @param string JavaScript-code to attach to the customer contact no. field
 */
function	kundeKontaktBlock( $_js="") {
	rowEdit( FTr::tr( "Customer contact no."), "_DKundeKontaktNr", 10, 10, "", FTr::tr( "HELP-CustomerContactNo"), "", $_js) ;
	rowOption( FTr::tr( "Anrede"), "_IAnrede", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Salutation'"), "Herr", FTr::tr( "HELP-Salutation")) ;
	rowOption( FTr::tr( "Title"), "_ITitel", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Title'"), "", FTr::tr( "HELP-Titel")) ;
	rowEdit( FTr::tr( "First name"), "_IVorname", 24, 64, "", FTr::tr( "HELP-FirstName")) ;
	rowEdit( FTr::tr( "Last name"), "_IName", 24, 64, "", FTr::tr( "HELP-LastName")) ;
	rowEdit( FTr::tr( "additional address"), "_IAdrZusatz", 16, 16, "", FTr::tr( "HELP-AdrressAdd")) ;
	rowEdit( FTr::tr( "Phone"), "_ITelefon", 24, 32, "", FTr::tr( "HELP-Phone")) ;
	rowEdit( FTr::tr( "FAX"), "_IFAX", 24, 32, "", FTr::tr( "HELP-FAX")) ;
	rowEdit( FTr::tr( "E-Mail"), "_IeMail", 32, 64, "", FTr::tr( "HELP-EMail")) ;
}
/**
 * produces the standard I/O block for supplier data
 * @param string JavaScript-code to attach to the supplier no. field
 */
function	liefBlock( $_js="") {
	rowEdit( FTr::tr( "Supplier no."), "_ILiefNr", 11, 11, "", FTr::tr( "HELP-SupplierNo"), "", "screenCurrent.showSelSupplier() ;") ;
	rowEditDblBR( FTr::tr( "Company / Institution"), "_IFirmaName1", 48, 64, "", "_IFirmaName2", 48, 64, "", FTr::tr( "HELP-Company")) ;
	rowEdit( FTr::tr( "Addition"), "_IFirmaName3", 48, 64, "", "") ;
	rowEditDbl( FTr::tr( "Street / No."), "_IStrasse", 24, 32, "", "_IHausnummer", 6, 12, "", FTr::tr( "HELP-Street")) ;
	rowEditDbl( FTr::tr( "ZIP / City"), "_IPLZ", 6, 10, "", "_IOrt", 24, 32, "", FTr::tr( "HELP-Address")) ;
	rowOption( FTr::tr( "Country"), "_ILand", Opt::getRLaender(), "de", FTr::tr( "HELP-Country")) ;
	rowEdit( FTr::tr( "Phone"), "_ITelefon", 24, 32, "", FTr::tr( "HELP-Phone")) ;
	rowEdit( FTr::tr( "FAX"), "_IFAX", 24, 32, "", FTr::tr( "HELP-FAX")) ;
	rowEdit( FTr::tr( "E-Mail"), "_IeMail", 32, 64, "", FTr::tr( "HELP-EMail")) ;
}
/**
 * produces the standard I/O block for supplier contact data
 * @param string JavaScript-code to attach to the supplier contact no. field
 */
function	liefKontaktBlock( $_js="") {
	rowEdit( FTr::tr( "Supplier contact no."), "_DLiefKontaktNr", 10, 10, "", FTr::tr( "HELP-SupplierContactNo"), "", $_js) ;
	rowOption( FTr::tr( "Anrede"), "_IAnrede", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Salutation'"), "Herr", FTr::tr( "HELP-Salutation")) ;
	rowOption( FTr::tr( "Title"), "_ITitel", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Title'"), "", FTr::tr( "HELP-Titel")) ;
	rowEdit( FTr::tr( "First name"), "_IVorname", 24, 64, "", FTr::tr( "HELP-FirstName")) ;
	rowEdit( FTr::tr( "Last name"), "_IName", 24, 64, "", FTr::tr( "HELP-LastName")) ;
	rowEdit( FTr::tr( "additional address"), "_IAdrZusatz", 16, 16, "", FTr::tr( "HELP-AddressAdd")) ;
	rowEdit( FTr::tr( "Phone"), "_ITelefon", 24, 32, "", FTr::tr( "HELP-Phone")) ;
	rowEdit( FTr::tr( "FAX"), "_IFAX", 24, 32, "", FTr::tr( "HELP-FAX")) ;
	rowEdit( FTr::tr( "E-Mail"), "_IeMail", 32, 64, "", FTr::tr( "HELP-EMail")) ;
}
/**
 *
 * @param $_nameCP      name of the dijit content pane
 * @param $_title       displayed name of the div
 * @param $_cuObj
 */
function	compCust( $_nameCP, $_title, $_cuObj, $_prefix, $_pref2, $_pref3) {
	?>
	<div id="<?php echo $_nameCP ; ?>" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( $_title) ; ?>">
		<div id="content">
			<div id="maindata">
				<table><tr><td>
					<form id="form<?php echo $_cuObj.$_prefix ; ?>Kunde" name="form<?php echo $_cuObj.$_prefix ; ?>Kunde" enctype="multipart/form-data" onsubmit="return false ;" >
						<fieldset style="border: 2px solid; -moz-border-radius: 8px; -webkit-border-radius: 8px;">
							<legend><?php echo FTr::tr( "Customer") ; ?>
								<input type="image" src="/Rsrc/licon/Green/18/zoom.png" name=""
									onclick="screenLinkTo( 'Kunde', document.forms['form<?php echo $_cuObj.$_prefix ; ?>Kunde']._DKundeNr.value) ;" />
							</legend>
							<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
							<table><?php
								kundeBlock( "selKunde( 'Base', '".$_cuObj."', document.forms['".$_cuObj."KeyData']._I".$_cuObj."Nr.value, 'setKundeFromKKId', show".$_cuObj."Kunde)") ;
								?></table>
							<button data-dojo-type="dijit/form/Button" name="actionCreateKunde1"
								onclick="requestUniA( 'Base', '<?php echo $_cuObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_prefix ; ?>Kunde', document.forms['<?php echo $_cuObj ; ?>KeyData']._I<?php echo $_cuObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_cuObj.$_prefix ; ?>Kunde', 'form<?php echo $_cuObj.$_prefix ; ?>KundeKontakt'),
														show<?php echo $_cuObj ; ?>All) ;">
								<?php echo FTr::tr( "Create Customer") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button" name="actionCreateKunde1"
								onclick="requestUniA( 'Base', '<?php echo $_cuObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_prefix ; ?>AddKunde', document.forms['<?php echo $_cuObj ; ?>KeyData']._I<?php echo $_cuObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_cuObj.$_prefix ; ?>Kunde', 'form<?php echo $_cuObj.$_prefix ; ?>KundeKontakt'),
														show<?php echo $_cuObj ; ?>All) ;">
								<?php echo FTr::tr( "Create 'Other' Customer") ; ?>
							</button>
<?php  if ( strlen( $_pref2) > 0) {					     ?>
							<button data-dojo-type="dijit/form/Button" name="actionCreateKunde2"
								onclick="requestUniA( 'Base', '<?php echo $_cuObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_pref2 ; ?>Kunde', document.forms['<?php echo $_cuObj ; ?>KeyData']._I<?php echo $_cuObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_cuObj ; ?>Kunde', 'form<?php echo $_cuObj ; ?>KundeKontakt'),
														show<?php echo $_cuObj ; ?>All) ;">
								<?php echo FTr::tr( "Create as Delivery Address") ; ?>
							</button>
<?php  }												?>
<?php  if ( strlen( $_pref3) > 0) {					     ?>
							<button data-dojo-type="dijit/form/Button" name="actionCreateKunde3"
								onclick="requestUniA( 'Base', '<?php echo $_cuObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_pref3 ; ?>Kunde', document.forms['<?php echo $_cuObj ; ?>KeyData']._I<?php echo $_cuObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_cuObj ; ?>Kunde', 'form<?php echo $_cuObj ; ?>KundeKontakt'),
														show<?php echo $_cuObj ; ?>All) ;">
								<?php echo FTr::tr( "Create as Invoicing Address") ; ?>
							</button>
<?php  }												?>
						</fieldset>
					</form></td><!--</tr><tr>--><td valign="top">
					<form id="form<?php echo $_cuObj.$_prefix ; ?>KundeKontakt" name="form<?php echo $_cuObj.$_prefix ; ?>KundeKontakt" enctype="multipart/form-data" onsubmit="return false ;" >
						<fieldset style="border: 2px solid; -moz-border-radius: 8px; -webkit-border-radius: 8px;">
							<legend><?php echo FTr::tr( "Customer Contact") ; ?></legend>
							<table><?php
								kundeKontaktBlock() ;
						       ?></table>
							<button data-dojo-type="dijit/form/Button" name="actionCreateKundeKontakt"
								onclick="requestUniA( 'Base', '<?php echo $_cuObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_prefix ; ?>KundeKontakt', document.forms['<?php echo $_cuObj ; ?>KeyData']._I<?php echo $_cuObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_cuObj.$_prefix ; ?>Kunde', 'form<?php echo $_cuObj.$_prefix ; ?>KundeKontakt'),
														show<?php echo $_cuObj ; ?>All) ;">
								<?php echo FTr::tr( "Create Customer Contact") ; ?>
							</button>
						</fieldset>
					</form></td></tr>
				</table>
			</div>
		</div>
	</div>
<?php
}
/**
 *
 * @param $_nameCP      name of the dijit content pane
 * @param $_title       displayed name of the div
 * @param $_suObj
 */
function	compSupp( $_nameCP, $_title, $_suObj, $_prefix, $_pref2, $_pref3) {
	?>
	<div id="<?php echo $_nameCP ; ?>" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( $_title) ; ?>">
		<div id="content">
			<div id="maindata">
				<table><tr><td>
					<form id="form<?php echo $_suObj.$_prefix ; ?>Lief" name="form<?php echo $_suObj.$_prefix ; ?>Lief" enctype="multipart/form-data" onsubmit="return false ;" >
						<fieldset style="border: 2px solid; -moz-border-radius: 8px; -webkit-border-radius: 8px;">
							<legend><?php echo FTr::tr( "Supplier") ; ?>
								<input type="image" src="/Rsrc/licon/Green/18/zoom.png" name=""
									onclick="screenLinkTo( 'Lief', document.forms['form<?php echo $_suObj.$_prefix ; ?>Lief']._DLiefNr.value) ;" />
							</legend>
							<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
							<table><?php
								liefBlock( "selLief( 'Base', '".$_suObj."', document.forms['".$_suObj."KeyData']._I".$_suObj."Nr.value, 'setLiefFromKKId', show".$_suObj."Lief)") ;
								?></table>
							<button data-dojo-type="dijit/form/Button" name="actionCreateLief1"
								onclick="requestUniA( 'Base', '<?php echo $_suObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_prefix ; ?>Lief', document.forms['<?php echo $_suObj ; ?>KeyData']._I<?php echo $_suObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_suObj.$_prefix ; ?>Lief', 'form<?php echo $_suObj.$_prefix ; ?>LiefKontakt'),
														show<?php echo $_suObj ; ?>All) ;">
								<?php echo FTr::tr( "Create Supplier") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button" name="actionCreateLief1"
								onclick="requestUniA( 'Base', '<?php echo $_suObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_prefix ; ?>AddLief', document.forms['<?php echo $_suObj ; ?>KeyData']._I<?php echo $_suObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_suObj.$_prefix ; ?>Lief', 'form<?php echo $_suObj.$_prefix ; ?>LiefKontakt'),
														show<?php echo $_suObj ; ?>All) ;">
								<?php echo FTr::tr( "Create 'Other' Supplier") ; ?>
							</button>
<?php  if ( strlen( $_pref2) > 0) {					     ?>
							<button data-dojo-type="dijit/form/Button" name="actionCreateLief2"
								onclick="requestUniA( 'Base', '<?php echo $_suObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_pref2 ; ?>Lief', document.forms['<?php echo $_suObj ; ?>KeyData']._I<?php echo $_suObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_suObj ; ?>Lief', 'form<?php echo $_suObj ; ?>LiefKontakt'),
														show<?php echo $_suObj ; ?>All) ;">
								<?php echo FTr::tr( "Create as Delivery Address") ; ?>
							</button>
<?php  }												?>
<?php  if ( strlen( $_pref3) > 0) {					     ?>
							<button data-dojo-type="dijit/form/Button" name="actionCreateLief3"
								onclick="requestUniA( 'Base', '<?php echo $_suObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_pref3 ; ?>Lief', document.forms['<?php echo $_suObj ; ?>KeyData']._I<?php echo $_suObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_suObj ; ?>Lief', 'form<?php echo $_suObj ; ?>LiefKontakt'),
														show<?php echo $_suObj ; ?>All) ;">
								<?php echo FTr::tr( "Create as Invoicing Address") ; ?>
							</button>
<?php  }												?>
						</fieldset>
					</form></td></tr><tr><td>
					<form id="form<?php echo $_suObj.$_prefix ; ?>LiefKontakt" name="form<?php echo $_suObj.$_prefix ; ?>LiefKontakt" enctype="multipart/form-data" onsubmit="return false ;" >
						<fieldset style="border: 2px solid; -moz-border-radius: 8px; -webkit-border-radius: 8px;">
							<legend><?php echo FTr::tr( "Supplier Contact") ; ?></legend>
							<table><?php
								liefKontaktBlock() ;
						       ?></table>
							<button data-dojo-type="dijit/form/Button" name="actionCreateLiefKontakt"
								onclick="requestUniA( 'Base', '<?php echo $_suObj ; ?>', '/Common/hdlObject.php',
														'new<?php echo $_prefix ; ?>LiefKontakt', document.forms['<?php echo $_suObj ; ?>KeyData']._I<?php echo $_suObj ; ?>Nr.value, '', '',
														new Array( 'form<?php echo $_suObj.$_prefix ; ?>Lief', 'form<?php echo $_suObj.$_prefix ; ?>LiefKontakt'),
														show<?php echo $_suObj ; ?>All) ;">
								<?php echo FTr::tr( "Create Supplier Contact") ; ?>
							</button>
						</fieldset>
					</form></td></tr>
				</table>
			</div>
		</div>
	</div>
<?php
}
?>
