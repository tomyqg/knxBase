<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="CustomerC3s1" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="CustomerC3s1c1" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form name="CustomerKeyData" id="CustomerKeyData" onsubmit="return false ;">
					<table>
						<tr>
							<th><?php echo FTr::tr( "Address no.") ; ?>:</th>
							<td class="space"><input type="image"
								src="/Rsrc/licon/yellow/18/left.png"
								onclick="hookPrevObject() ; return false ;" />
							</td>
							<td><input type="text" name="_ICustomerNo" id="_ICustomerNo" value=""
								onkeypress="return enterKey( event) ;" />
							</td>
							<td class="space"><input type="image"
								src="/Rsrc/licon/yellow/18/right.png"
								onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button"border="0"
									onclick="screenCurrent.select.show( '', -1, '') ;">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button>
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Company name") ; ?>:</th>
							<td colspan="4"><input type="text" name="_DFirmaName1" id="VOID"
								size="35" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="CustomerC3s1c2" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="CustomerTc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="CustomerTc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formCustomerMain" id="formCustomerMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Company name 1"), "_IFirmaName1", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Company name 2"), "_IFirmaName2", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Company name 3"), "_IFirmaName3", 32, 64, "", "") ;
								rowEditDbl( FTr::tr( "Street / No."), "_IStrasse", 20, 32, "", "_IHausnummer", 6, 10, "", "") ;
								rowEditDbl( FTr::tr( "ZIP / City"), "_IPLZ", 6, 8, "", "_IOrt", 20, 32, "", "") ;
								rowOption( FTr::tr( "Country"), "_ILand", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Country'"), "de", "") ;
								rowEdit( FTr::tr( "Phone"), "_ITelefon", 20, 32, "", "") ;
								rowEdit( FTr::tr( "Fax"), "_IFAX", 20, 32, "", "") ;
								rowEdit( FTr::tr( "Mobil"), "_IMobil", 20, 32, "", "") ;
								rowEdit( FTr::tr( "E-Mail"), "_IeMail", 20, 32, "", "") ;
								rowEdit( FTr::tr( "URL"), "_IURL", 24, 64, "", "") ;
								rowFlag( FTr::tr( "Tax"), "_ITax", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Flag'"), "", FTr::tr( "HELP-Cust-Tax")) ;
								rowOption( FTr::tr( "Language"), "_ISprache", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Language'"), "de_DE", "") ;
								rowOption( FTr::tr( "Currency"), "_ICurrency", Opt::getRCurrCodes(), "EUR", "") ;
								rowOption( FTr::tr( "Organisation"), "_IOrgType", Opt::getROrgType(), "", "") ;
								rowCB( FTr::tr( "ArticleRights"), "_IRights", Opt::getArray( "Options", "Key", "Value", "OptionName = 'ArticleRights'"), "", "") ;
								?>
							</table> 
							<button data-dojo-type="dijit/form/Button"
								onclick="dispatchForm( 'xml', true, 'upd', 'formCustomerMain') ; return false ; ">
								<?php echo FTr::tr( "Update ...") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="CustomerTc1cp2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Terms of sale") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formCustomerModi" id="formCustomerModi" enctype="multipart/form-data">
							<table><?php
								rowEdit( FTr::tr( "Tax Id. Nr."), "_IUStId", 20, 32, "", "HELP-TaxId") ;
								rowEdit( FTr::tr( "RMandant Nr."), "_IMandNr", 6, 6, "", "HELP-MandNr") ;
								rowOption( FTr::tr( "Internet order"), "_IModusBestInternet", opt::getRModusInternet(), "", "HELP-ModeInternetOrdr") ;
								rowFlag( FTr::tr( "Order confirmation"), "_IModusBestConf", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Flag'"), "", FTr::tr( "INFO-ACTUAL")) ;rowOption( FTr::tr( "Language"), "_ISprache", Opt::getRLangCodes(), "de_de", "") ;
								rowOption( FTr::tr( "Delivery"), "_IModusLief", Customer::getRModeDlvr(), "", "HELP-ModeDlvr") ;
								rowOption( FTr::tr( "Invoicing"), "_IModusRech", Customer::getRModeInvc(), "", "HELP-ModeInvc") ;
								rowDisplay( FTr::tr( "Payment"), "_DModusPay", 6, 6, "", "HELP-ModusZahl") ;
								rowCB( FTr::tr( "Payment"), "_IModusPay", Opt::getArray( "Options", "Key", "Value", "OptionName = 'ModePay'"), "", "HELP-ModePmnt") ;
								rowOption( FTr::tr( "Skonto"), "_IModusSkonto", opt::getRModusSkonto(), "", "") ;
								rowEdit( FTr::tr( "Discount"), "_IRabatt", 8, 8, "", "HELP-Discount") ;
								rowOption( FTr::tr( "Cusomer type"), "_ITypeCust", Customer::getRTypeCust(), Customer::TC_B, "HELP-TypeCust") ;
								rowTextEdit( FTr::tr( "Remark(s)"), "_DRem1Customer", 64, 6, "", "") ;
								?>
							</table> 
							<button data-dojo-type="dijit/form/Button"
								onclick="dispatchForm( 'xml', true, 'upd', 'formCustomerModi') ; return false ; ">
								<?php echo FTr::tr( "Update ...") ; ?>
							</button>
						</form> 
						<form name="formCustomerAddRem" id="formCustomerAddRem" onsubmit="return false ;" >
							<table><?php
								rowTextEdit( FTr::tr( "Remark to add"), "_IRem", 64, 2, "", "") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button" onclick="dispatchForm( 'xml', true, 'addRem', 'formCustomerAddRem') ; return false ;">
								<?php echo FTr::tr( "Add remark") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="CustomerTc1cp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Access") ; ?>">
				<div id="content">
					<div id="maindata">
						<form action="CustomerBearb.php" method="post" name="formCustomerZugriff" enctype="multipart/form-data">
							<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
							<table><?php
								rowEdit( FTr::tr( "Username"), "_IBenutzerName", 8, 8, "", "") ;
								rowDisplay( FTr::tr( "Password"), "_IPasswort", 24, 64, "", "") ;
								rowDisplay( FTr::tr( "Activationkey"), "_IActivationKey", 8, 8, "", "") ;
								?>
							</table> 
							<br/>
							<button data-dojo-type="dijit/form/Button"
								onclick="dispatchForm( 'xml', true, 'upd', 'formCustomerZugriff') ; return false ; ">
								<?php echo FTr::tr( "Update ...") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button" type="reset" value="Reset input fields"> 
						</form> 
					</div>
				</div>
			</div>
			<div id="CustomerTc1cp5" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Contacts") ; ?>">
				<div id="content">
					<div id="depdata">
						<div id="TableCustContactRoot">
							<?php tableBlock( "itemViews['dtvCustContacts']", "formCustContactsTop") ; ?>
							<table id="TableCustContacts" eissClass="CustomerKontakt" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="CustomerKontaktNr">Contact<br/>no.</th>
										<th eissAttribute="Anrede">Salutation</th>
										<th eissAttribute="Titel">Title</th>
										<th eissAttribute="Vorname">Given name</th>
										<th eissAttribute="Name" eissFunctions="input">Last name</th>
										<th eissAttribute="eMail" eissFunctions="input" eissSize="32" eissMailTo="true" colspan="2">eMail</th>
										<th eissAttribute="Telefon">Phone</th>
										<th eissAttribute="Mobil">Cellphone</th>
										<th colspan="2" eissFunctions="edit,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<?php tableBlockBot( "itemViews['dtvCustContacts']", "formCustContactsBot") ; ?>
						</div>
					</div>
				</div>
			</div>
			<div id="CustomerTc1cp6" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Addresses") ; ?>">
				<div id="CustomerTc1cp6tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="CustomerTc1cp6tc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Delivery-") ; ?>-">
						<div id="content">
							<div id="TableCustAddressesDeliverRoot">
								<?php tableBlock( "itemViews['dtvCustAddressesDeliver']", "formCustAddressesDeliverTop") ; ?>
								<table id="TableCustAddressesDeliver" eissClass="Customer" width="100%">
									<thead>
										<tr eissType="header">
											<th eissAttribute="Id">Id</th>
											<th eissAttribute="CustomerNo" eissLinkTo="Customer" colspan="2">Customer no.<br/>no.</th>
											<th eissAttribute="FirmaName1">Name 1</th>
											<th eissAttribute="FirmaName2">Name 2</th>
											<th eissAttribute="PLZ">ZIP</th>
											<th eissAttribute="Ort" eissFunctions="input">City</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
								<?php tableBlockBot( "itemViews['dtvCustAddressesDeliver']", "formCustAddressesDeliverBot") ; ?>
							</div>
						</div>
					</div>
					<div id="CustomerTc1cp6tc1cp2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Invoicing-") ; ?>">
						<div id="content">
							<div id="TableCustAddressesInvoiceRoot">
								<?php tableBlock( "itemViews['dtvCustAddressesInvoice']", "formCustAddressesInvoiceTop") ; ?>
								<table id="TableCustAddressesInvoice" eissClass="Customer" width="100%">
									<thead>
										<tr eissType="header">
											<th eissAttribute="Id">Id</th>
											<th eissAttribute="CustomerNo" eissLinkTo="Customer" colspan="2">Customer no.<br/>no.</th>
											<th eissAttribute="FirmaName1">Name 1</th>
											<th eissAttribute="FirmaName2">Name 2</th>
											<th eissAttribute="PLZ">ZIP</th>
											<th eissAttribute="Ort" eissFunctions="input">City</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
								<?php tableBlockBot( "itemViews['dtvCustAddressesInvoice']", "formCustAddressesInvoiceBot") ; ?>
							</div>
						</div>
					</div>
					<div id="CustomerTc1cp6tc1c32" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Additional-") ; ?>">
						<div id="content">
							<div id="TableAddCustomerRoot">
								<?php tableBlock( "itemViews['dtvCustAddressesOther']", "formCustAddressesAddTop") ; ?>
								<table id="TableAddCustomer" eissClass="Customer" width="100%">
									<thead>
										<tr eissType="header">
											<th eissAttribute="Id">Id</th>
											<th eissAttribute="CustomerNo" eissLinkTo="Customer" colspan="2">Customer no.<br/>no.</th>
											<th eissAttribute="FirmaName1">Name 1</th>
											<th eissAttribute="FirmaName2">Name 2</th>
											<th eissAttribute="PLZ">ZIP</th>
											<th eissAttribute="Ort" eissFunctions="input">City</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
								<?php tableBlockBot( "itemViews['dtvCustAddressesOther']", "formCustAddressesAddBot") ; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="CustomerTc1cp4" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Datamining") ; ?>">
				<div id="CustomerTc1cp4tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="CustomerTc1cp4tc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Wishlists") ; ?>">
						<div id="CustomerMerkzettel"></div>
					</div>
					<div id="CustomerTc1cp4tc1cp2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Enquiries") ; ?>">
						<div id="CustomerKdAnf"></div>
					</div>
					<div id="CustomerTc1cp4tc1cp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Offers") ; ?>"
							onShow="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdAngForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdAngForCustomer', 'KdAngNr', 'screenKdAng', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; ">
						<button data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdAngForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdAngForCustomer', 'KdAngNr', 'screenKdAng', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; " />
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="KdAngForCustomer"></div>
					</div>
					<div id="CustomerTc1cp4tc1cp4" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Orders") ; ?>"
							onShow="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdBestForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdBestForCustomer', 'KdBestNr', 'screenKdBest', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; ">
						<button data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdBestForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdBestForCustomer', 'KdBestNr', 'screenKdBest', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; " />
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="KdBestForCustomer"></div>
					</div>
					<div id="CustomerTc1cp4tc1cp5" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Commissions") ; ?>"
							onShow="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdKommForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdKommForCustomer', 'KdKommNr', 'screenKdKomm', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; " />
						<button data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdKommForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdKommForCustomer', 'KdKommNr', 'screenKdKomm', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; " />
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="KdKommForCustomer"></div>
					</div>
					<div id="CustomerTc1cp4tc1cp6" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Deliveries") ; ?>"
							onShow="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdLiefForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdLiefForCustomer', 'KdLiefNr', 'screenKdLief', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; ">
						<button data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdLiefForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdLiefForCustomer', 'KdLiefNr', 'screenKdLief', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; " />
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="KdLiefForCustomer"></div>
					</div>
					<div id="CustomerTc1cp4tc1cp7" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Invoices") ; ?>"
							onShow="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdRechForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdRechForCustomer', 'KdRechNr', 'screenKdRech', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; ">
						<button data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'ModBase', 'DataMinerCustomer', '/Common/hdlObject.php', 'getTableKdRechForCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, 'KdRechForCustomer', 'KdRechNr', 'screenKdRech', 'retToCustomer', document.forms['CustomerKeyData']._ICustomerNo.value, '', null) ; return false ; " />
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="KdRechForCustomer"></div>
					</div>
					<div id="CustomerTc1cp4tc1cp8" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Reminders") ; ?>">
						<div id="KdMahnForCustomer"></div>
					</div>
				</div>
			</div>
			<div id="CustomerDataTCVersandTCExport" data-dojo-type="dijit/layout/ContentPane" title="Export">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onclick="dispatchForm( 'xml', true, 'export', null) ;">
							<?php echo FTr::tr( "Export to archive") ; ?>
						</button>
					</div>
				</div>
			</div>
		</div>
		
