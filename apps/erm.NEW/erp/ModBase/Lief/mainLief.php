<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="LiefC3s1" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="LiefC3s1c1" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="LiefKeyData" id="LiefKeyData" onsubmit="return false ;">
					<table>
						<tr>
							<th><?php echo FTr::tr( "Supplier no.") ; ?>:&nbsp;</th>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" /></td>
							<td>
								<input type="text" size="10" maxlength="8" name="_ILiefNr" id="_ILiefNr"
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/right.png"
								onclick="hookNextObject() ; return false ;" /></td>
							<td>
								<input type="button" name="sel_ArtikelNr" value="Select ..."
									onclick="screenCurrent.selLief.show( '', -1, '') ; "/>
							</td>
							<td class="image">
								<div id="pdfLief">
								</div>
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Name") . ":" ; ?></th>
							<td colspan="4">
								<input type="text" name="_DFirmaName1" id="VOID" size="35" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="LiefC3s1c2" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="LiefTc1" data-dojo-type="dijit/layout/TabContainer">
			<div id="LiefTc1cp1" data-dojo-type="dijit/layout/ContentPane" title="Allgemein (*)">
				<div id="content">
					<div id="maindata">
						<form name="formLiefMain" id="formLiefMain" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Nickname"), "_INickname", 20, 20, "", "") ;
								rowEdit( FTr::tr( "Company name 1"), "_IFirmaName1", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Company name 2"), "_IFirmaName2", 32, 64, "", "") ;
								rowEditDbl( FTr::tr( "Street / No."), "_IStrasse", 20, 32, "", "_IHausnummer", 6, 10, "", "") ;
								rowEditDbl( FTr::tr( "ZIP / City"), "_IPLZ", 6, 8, "", "_IOrt", 20, 32, "", "") ;
								rowOption( FTr::tr( "Country"), "_ILand", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Country'"), "de", "") ;
								rowEdit( FTr::tr( "Phone"), "_ITelefon", 20, 32, "", "") ;
								rowEdit( FTr::tr( "Fax"), "_IFAX", 20, 32, "", "") ;
								rowEditDbl( FTr::tr( "Ordering fax (# of tries)"), "_IBestellFAX", 20, 32, "", "_IFAXTries", 2, 2, "", "") ;
								rowEdit( FTr::tr( "Cellphone"), "_IMobil", 20, 32, "", "") ;
								rowEdit( FTr::tr( "eMail"), "_IeMail", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Ordering eMail"), "_IBestellEMail", 32, 64, "", "") ;
								?></table>
							<button data-dojo-type="dijit/form/Button"
								onclick="dispatchForm( 'xml', true, 'upd', 'formLiefMain') ; return false ;">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button" onclick="clrForm( 'formLiefMain') ;">
								<?php echo FTr::tr( "Clear fields") ; ?>
							</button>
						</form>
					</div>
				</div>
			</div>
			<div id="LiefTc1cp2" data-dojo-type="dijit/layout/ContentPane" title="Sonstiges (*)">
				<div id="content">
					<div id="maindata">
						<form name="formLiefSonstiges" id="formLiefSonstiges" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Prefix"), "_ILiefPrefix", 3, 3, "",
												"Prefix f&uuml;r die MODIS Artikelnummer dieses Herstellers<br/>" .
												"(z.B. MUN = Macherey und Nagel, GRT = Gratnell)") ;
								rowDisplay( FTr::tr( "Nickname"), "_DNickname", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Ordering Contact"), "_IOrderingContact", 3, 3, "", "") ;
								rowEdit( FTr::tr( "Ordering E-Mail"), "_IBestellEMail", 24, 32, "", "") ;
								rowEdit( FTr::tr( "URL"), "_IURL", 24, 32, "", "") ;
								rowDate( FTr::tr( "Prices valid from"), "_IPricesValidFrom", 10, 10, "", "") ;
								rowDate( FTr::tr( "Prices valid to"), "_IPricesValidTo", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Min. order value"), "_IMinOrderValue", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Limit S&H free of charge"), "_IOrderValueVKF", 24, 32, "", "") ;
								rowOption( FTr::tr( "Calculation mode"), "_IAutoPrice", Artikel::getRAutoPrice(), "1", "") ;
								rowEdit( FTr::tr( "Auto article check"), "_IAutoArticleCheck", 1, 1, "", FTr::tr( "HELP-AutoArticleCheck")) ;
								rowEdit( FTr::tr( "Markup"), "_FMarge", 8, 8, "", FTr::tr( "HELP-Markup")) ;
								rowOption( FTr::tr( "Tax"), "_ITax", Opt::getRFlagNeinJa(), "1", FTr::tr( "HELP-Tax")) ;
								rowOption( FTr::tr( "Currency"), "_ICurrency", Opt::getRCurrCodes(), "EUR", FTr::tr( "HELP-Currency")) ;
								rowOption( FTr::tr( "Language"), "_ISprache", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Language'"), "de_DE", FTr::tr( "HELP-Language")) ;
								rowEdit( FTr::tr( "Own custoemr no."), "_IEigeneKundenNr", 12, 12, "", FTr::tr( "HELP-OwnCustNo")) ;
								rowEdit( FTr::tr( "ERP No. range start"), "_IERPNoStart", 8, 8, "", "") ;
								rowEdit( FTr::tr( "ERP No. range end"), "_IERPNoEnd", 8, 8, "", "") ;
								rowTextEdit( FTr::tr( "Remarks"), "_DRem1Lief", 64, 6, "", FTr::tr( "HELP-Rem")) ;
								?>
							</table>
							<button data-dojo-type="dijit/form/Button"
								onclick="dispatchForm( 'xml', true, 'upd', 'formLiefSonstiges') ; return false ;">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form>
						<form name="formLiefAddRem" id="formLiefAddRem" onsubmit="return false ;" >
							<table><?php
								rowTextEdit( FTr::tr( "Remark to add"), "_IRem", 64, 2, "", "") ;
								?></table>
							<button data-dojo-type="dijit/form/Button"
								onclick="dispatchForm( 'xml', true, 'addRem', 'formLiefAddRem') ; return false ;">
								<?php echo FTr::tr( "Add remark") ; ?>
							</button>
						</form>
					</div>
				</div>
			</div>
			<div id="LiefTc1cp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( 'Purchase conditions') ; ?>">
				<div id="content">
					<div id="maindata">
					</div>
					<div id="depdata">
						<div id="TableSuppDiscountRoot">
							<?php tableBlock( "itemViews['dtvSuppDiscounts']", "formSuppDiscountsTop") ; ?>
							<table id="TableSuppDiscounts" eissClass="LiefRabatt" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="HKRabKlasse">Contact<br/>no.</th>
										<th eissAttribute="Menge">Salutation</th>
										<th eissAttribute="Rabatt">Title</th>
										<th colspan="2" eissFunctions="edit,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<?php tableBlockBot( "itemViews['dtvSuppDiscounts']", "formSuppDiscountsBot") ; ?>
						</div>
					</div>
				</div>
			</div>
			<div id="LiefTc1cp4" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( 'Contacts') ; ?>">
				<div id="content">
					<div id="depdata">
						<div id="TableSuppContactRoot">
							<?php tableBlock( "itemViews['dtvSuppContacts']", "formSuppContactsTop") ; ?>
							<table id="TableSuppContacts" eissClass="LiefKontakt" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="LiefKontaktNr">Contact<br/>no.</th>
										<th eissAttribute="Anrede">Salutation</th>
										<th eissAttribute="Titel">Title</th>
										<th eissAttribute="Vorname">Given name</th>
										<th eissAttribute="Name" eissFunctions="input">Last name</th>
										<th eissAttribute="eMail" eissMailTo="true" colspan="2">eMail</th>
										<th eissAttribute="Telefon">Phone</th>
										<th eissAttribute="Mobil">Cellphone</th>
										<th colspan="2" eissFunctions="edit,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<?php tableBlockBot( "itemViews['dtvSuppContacts']", "formSuppContactsBot") ; ?>
						</div>
					</div>
				</div>
			</div>
			<div id="LiefTc1cp5" data-dojo-type="dijit/layout/ContentPane" title="Datamining">
				<div id="LiefTc1cp5tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="LiefTc1cp5tc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Orders") ; ?>">
						<button data-dojo-type="dijit/form/Button" onClick="screenSupp.dmSuOrdrForSupp.request() ; return false ;">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<?php tableBlock( "screenSupp.dmSuOrdrForSupp.request", "formSuOrdrForSuppTop") ; ?>
						<div id="divSuOrdrForSupp"></div>
					</div>
					<div id="LiefTc1cp5tc1cp2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Goods Receivable") ; ?>">
						<button data-dojo-type="dijit/form/Button" onClick="screenSupp.dmSuDlvrForSupp.request().dmSuDlvrForArtikel.request() ; return false ;">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<?php tableBlock( "screenSupp.dmSuDlvrForSupp.request", "formSuDlvrForSuppTop") ; ?>
						<div id="divSuDlvrForSupp"></div>
					</div>
					<div id="LiefEKPreisR" data-dojo-type="dijit/layout/ContentPane" title="EKPreisR Daten">
						<div id="content">
							<div id="maindata">
								<form name="formLiefEKPreisRTop" id="formLiefEKPreisRTop" onsubmit="return false ;">
									<input type="text" name="_ILiefEKPreisRCrit" value="">
									<button data-dojo-type="dijit/form/Button" onclick="liefGetEKPreisR( '0') ; return false ; "
										title="<?php echo FTr::tr( "ERP-HELP-LIEF-SHOW-UNRELATED-ARTICLES") ; ?>">
										<?php echo FTr::tr( "Unrelated") ; ?>
									</button>
									<button data-dojo-type="dijit/form/Button" onclick="liefGetEKPreisR( '1') ; return false ; "
										title="<?php echo FTr::tr( "ERP-HELP-LIEF-SHOW-UNLISTED-ARTICLES") ; ?>">
										<?php echo FTr::tr( "Unlisted") ; ?>
									</button>
									<button data-dojo-type="dijit/form/Button" onclick="liefGetEKPreisR( '2') ; return false ; "
										title="<?php echo FTr::tr( "ERP-HELP-LIEF-SHOW-LISTED-ARTICLES") ; ?>">
										<?php echo FTr::tr( "Listed") ; ?>
									</button>
								</form>
							</div>
							<center id="sloganLiefEKPreisR">slogan</center>
							<div id="maindata">
								<?php tableBlockNF( "_liefGetEKPreisRTop", "formLiefEKPreisRTop") ; ?>
							</div>
							<div id="depdata">
								<div id="tableLiefEKPreisR"></div>
							</div>
							<div id="maindata">
								<?php tableBlock( "_liefGetEKPreisRBot", "formLiefEKPreisRBot") ; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="TabSuppDoc" data-dojo-type="dijit/layout/ContentPane" title="Dokumente">
				<div id="TabSuppDocCont" data-dojo-type="dijit/layout/TabContainer" tabposition="top">
					<div id="TabSuppDocTable" data-dojo-type="dijit/layout/ContentPane" title="&Uuml;bersicht">
						<button data-dojo-type="dijit/form/Button" onClick="requestUni( 'Base', 'Lief', '/Common/hdlObject.php', 'getDocListAsXML', document.forms['LiefKeyData']._ILiefNr.value, '', '', null, showLiefDocList) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="TableLiefDocs">
						</div>
					</div>
					<div id="TabLiefDocUpload" data-dojo-type="dijit/layout/ContentPane" title="Upload">
						<div id="content">
							<div id="maindata">
								<form action="/ModBase/Lief/upLiefDoc.php" method="post" name="formLiefDocUpload" id="formLiefDocUpload" target="_result" enctype="multipart/form-data">
									<input type="text" name="_DLiefNr" value="" />
									<table><?php
										rowOption( FTr::tr( "Document type"), "_IDocTypeLief", Lief::$rDocType, "PL", "") ;
										rowFile( FTr::tr( "Filename"), "_IFilename", 24,32, "", "") ;
										?></table>
									<button type="submit" data-dojo-type="dijit/form/Button" tabindex="14" border="0">
										<?php echo FTr::tr( "Upload") ; ?>
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="ERPFunc" data-dojo-type="dijit/layout/ContentPane" title="Funktionen">
				<button type="button" data-dojo-type="dijit/form/Button" border="0"
					onclick="requestUni( 'Base', 'Lief', '/Common/hdlObject.php', 'assignERPNos', document.forms['LiefKeyData']._ILiefNr.value, '', '', null, showLief) ; return false ; "
					title="<?php echo FTr::tr( "ERP-HELP-LIEF-ASSIGN_ERPNOS") ; ?>">
					<?php echo FTr::tr( "Assign ERP numbers to all articles") ; ?>
				</button><br/>
			</div>
			<div id="SuppFunc" data-dojo-type="dijit/layout/ContentPane" title="Funktionen">
				<button type="button" data-dojo-type="dijit/form/Button" border="0"
					onclick="screenCurrent.qDispatch( true, 'calcPP') ;"
					title="<?php echo FTr::tr( "ERP-HELP-LIEF-CALC-ALL-SELLING-PRICES") ; ?>">
					<?php echo FTr::tr( "Calculate all purchasing prices with discount classes") ; ?>
				</button><br/>
				<button type="button" data-dojo-type="dijit/form/Button" border="0"
					onclick="screenCurrent.qDispatch( true, 'calcSP') ;">
					<?php echo FTr::tr( "Calculate all selling prices") ; ?>
				</button><br/>
				<button type="button" data-dojo-type="dijit/form/Button" border="0"
					onclick="screenCurrent.qDispatch( true, 'cacheSP') ;">
					<?php echo FTr::tr( "Update all cached prices") ; ?>
				</button><br/>
				<button type="button" data-dojo-type="dijit/form/Button" border="0"
					onclick="screenCurrent.qDispatch( true, 'calcCacheSP') ;">
					<?php echo FTr::tr( "Calculate and Update all sales prices") ; ?>
				</button><br/>
				<button type="button" data-dojo-type="dijit/form/Button" border="0"
					onclick="screenCurrent.qDispatch( true, 'checkArticles') ;">
					<?php echo FTr::tr( "Check if articles exist for all available purchase prices") ; ?>
				</button><br/>
			</div>
		</div>
	</div>
</div>
