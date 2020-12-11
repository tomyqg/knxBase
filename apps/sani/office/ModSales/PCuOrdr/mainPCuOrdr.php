<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="PCuOrdrBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="PCuOrdrCPKey" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="PCuOrdrKeyData" id="PCuOrdrKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "New order no.") ; ?>:&nbsp;</th>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IPCuOrdrNo" id="_IPCuOrdrNo"
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button type="button" data-dojo-type="dijit/form/Button" name="btnSelPCuOrdr"
										onclick="screenCurrent.selPCuOrdr.show( '', -1, '') ;"> 
									<?php echo FTr::tr( "Select ...") ; ?>
								</button>
							</td>
							<td class="image">
								<input type="image" src="/Rsrc/licon/Blue/32/object_04.png"
									onclick="screenCurrent.newCuOrdr() ;"
									title="<?php FTr::tr( "Accept order") ; ?>" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="PCuOrdrCPData" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="PCuOrdrDataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="PCuOrdrDataTCMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formPCuOrdrMain" id="formPCuOrdrMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEditDbl( FTr::tr( "Customer no."), "_IKundeNr", 11, 11, "", "_IKundeKontaktNr", 3, 3, "", "", "",
											"screenCurrent.showSelCustomer() ;") ;
								rowEditDbl( FTr::tr( "Dlvr. customer no."), "_ILiefKundeNr", 11, 11, "", "_ILiefKundeKontaktNr", 3, 3, "", "", "",
											"screenCurrent.showSelCustDlvr() ;") ;
								rowEditDbl( FTr::tr( "Invc. customer no."), "_IRechKundeNr", 11, 11, "", "_IRechKundeKontaktNr", 3, 3, "", "", "",
											"screenCurrent.showSelCustInvc() ;") ;
								rowDate( FTr::tr( "Date"), "_IDatumPCuOrdr", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Ref. no."), "_IRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Ref. date"), "_IRefDatumPCuOrdr", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Customer ref. no."), "_IKdRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Customer ref. date"), "_IKdRefDatumPCuOrdr", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Delivery date"), "_ILieferTermin", 24, 32, "", "") ;
								rowDate( FTr::tr( "Latest invoicing date"), "_ILatestCuInvcDatumPCuOrdr", 24, 32, "", "") ;
								rowOption( FTr::tr( "Delivery mode"), "_IModusLief", Opt::getRModusLief(), "", "") ;
								rowOption( FTr::tr( "Invoicing mode"), "_IModusRech", Opt::getRModusRech(), "", "") ;
//								rowOption( FTr::tr( "Zahlungs Modus"), "_IModusZahl", Opt::getRModusZahl(), "", "") ;
								rowOption( FTr::tr( "Payment terms"), "_IModusSkonto", Opt::getRModusSkonto(), "", "") ;
								rowOption( FTr::tr( "Skonto Abzug"), "_IModusSkAbzug", Opt::getRModusSkAbzug(), "", "") ;
								rowEdit( FTr::tr( "Item count"), "_IPositionen", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Item count for direkt shipment"), "_IPosDirVers", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Total net"), "_FGesamtPreis", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Versand Pauschale"), "_FVersPausch", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Total taxes"), "_FGesamtMwst", 10, 10, "", "") ;
								rowOption( FTr::tr( "Discount mode"), "_IDiscountMode", Opt::getRDMLevel(), "", "") ;
								rowEdit( FTr::tr( "Discount"), "_FRabatt", 10, 10, "", "") ;
							?></table>
							<button data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.sDispatch( true, 'upd', 'formPCuOrdrMain') ;">
									<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="PCuOrdrDataTCKunde" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Addresses") ; ?>">
				<div id="PCuOrdrDataTCKundeTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<?php
						compCust( "PCuOrdrCPKunde", "Order-", "PCuOrdr", "", "Lief", "Rech") ;
						compCust( "PCuOrdrCPLiefKunde", "Delivery-", "PCuOrdr", "Lief", "", "") ;
						compCust( "PCuOrdrCPRechKunde", "Invoicing-", "PCuOrdr", "Rech", "", "") ;
					?>
				</div>
			</div>
			<div id="PCuOrdrDataTCModi" data-dojo-type="dijit/layout/ContentPane" title="Verkaufsmodalit&auml;ten">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formPCuOrdrModi" id="formPCuOrdrModi" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Handling flat"), "_IVersPausch", 10, 10, "", "") ;
								rowOption( FTr::tr( "Payment terms"), "_IModusSkonto", Opt::getRModusSkonto(), "") ;
								rowOption( FTr::tr( "Liefermodus"), "_IModusLief", Opt::getRModusLief(), "") ;
								rowOption( FTr::tr( "Rechungsmodus"), "_IModusRech", Opt::getRModusRech(), "") ;
								rowOption( FTr::tr( "Rabattmodus"), "_IDiscountMode", Opt::getRDMLevel(), "") ;
								rowEdit( FTr::tr( "Rabatt"), "_FRabatt", 10, 10, "", "") ;
								rowTextEdit( FTr::tr( "Bemerkungen"), "_IRem1", 64, 3, "", "", "onkeypress='mark( _IRem1) ;'") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button"
								onclick="requestUni( 'Base', 'PCuOrdr', '/Common/hdlObject.php', 'upd', document.forms['PCuOrdrKeyData']._IPCuOrdrNo.value, '', '', 'formPCuOrdrModi', showPCuOrdr) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="PCuOrdrDataTCPosten" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Items") ; ?>">
<!--
	tell the artikel selector to:
	use an object of classe "PCuOrdr" with the key of the "MainInputField" (PCuOrdrNo)
	with this run the script "/Common/hdlObject.php" to call the method "PCuOrdr".addPos and when done
	run the javascript function "showPCuOrdrItem"
-->
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" 
							onclick="screenCurrent.showSelArticleSP() ;">
							<?php echo FTr::tr( "New item ...") ; ?>
						</button>
					</div>
					<div id="depdata">
						<div id="PCuOrdrItemsRoot">
							<?php tableBlock( "itemViews['dtvPCuOrdrItems']", "formPCuOrdrItemsTop") ;		?>
							<table id="TablePCuOrdrItems" eissClass="PCuOrdrItem" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="ItemNo">Item<br/><img src="/Rsrc/licon/s_reload.png" eissSortAttr="PosNo" eissSortMode="PNoSPNoA" onclick="return sortTableItems( this, 'formPCuOrdrItemTop', '_SOrdMode', refPCuOrdrItem) ;"/></th>
										<th eissAttribute="SubItemNo">Sub-Item</th>
										<th eissAttribute="ArtikelNr" eissLinkTo="Artikel" colspan="2">Article no.</th>
										<th eissAttribute="ERPNo" eissLinkTo="Artikel" colspan="2">ERP no.</th>
										<th eissAttribute="ArtikelBez1">Description</th>
										<th eissAttribute="Menge" eissFunctions="step" colspan="3">Qty. ordered</th>
										<th eissAttribute="MengeProVPE" >Qty. / pack</th>
										<th eissAttribute="Preis" eissFunctions="input">Price / item</th>
										<th eissAttribute="RefPreis">Ref.Price / item</th>
										<th eissAttribute="GesamtPreis">Total Price</th>
										<th colspan="5" eissFunctions="edit,move,colex,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="PCuOrdrDataTCKalkulation" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Calculation") ; ?>">
				<div id="content">
					<div id="maindata">
						<form action="KundeBearb.php" method="post" name="formPCuOrdrCalc" enctype="multipart/form-data">
							<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
							<table><?php
								rowDisplay( FTr::tr( "Angebot Nr."), "_IPCuOrdrNo", 6, 6, "", "") ;
								rowDispOption( FTr::tr( "Rabattmodus"), "_IDiscountMode", Opt::getRDMLevel(), "") ;
								rowDisplay( FTr::tr( "Gesamt netto"), "_FGesamtPreis", 12, 12, "","") ;
								rowDisplay( FTr::tr( "Rabatt"), "_CRabatt", 12, 12, "", "") ;
								rowDisplay( FTr::tr( "Gesamt netto nach Rabatt"), "_CNettoNachRabatt", 12, 12, "", "") ;
		//						rowDisplay( FTr::tr( "MwSt. gesamt"), "_IGesamtMwst", 12, 12, "","") ;
		//						rowDisplay( FTr::tr( "Gesamt brutto"), "VOID", 12, 12, "", "") ;
								rowDisplay( FTr::tr( "Gesamt Einkauf"), "_FGesamtEKPreis", 12, 12, "", "") ;
								rowDisplay( FTr::tr( "Rohmarge"), "_CRohmarge", 12, 12, "", "") ;
								?></table>
							<button data-dojo-type="dijit/form/Button"
								onclick="setPCuOrdrCalcMode( _IPCuOrdrNo.value, 'v1') ;">
								<?php echo FTr::tr( "Discount V1") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button"
								onclick="setPCuOrdrCalcMode( _IPCuOrdrNo.value, 'v2') ;" />
								<?php echo FTr::tr( "Discount V2") ; ?>
							</button>
						</form>
					</div>
				</div>
			</div>
			<div id="PCuOrdrDataTCFunktionen" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Functions") ; ?>">
			</div>
		</div>
	</div>
</div>
