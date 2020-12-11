<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="CuCredC3s1" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="CuCredKey" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="KdGutsKeyData" id="KdGutsKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Credit note no.") ; ?></th>
							<td>
								<input type="image" src="/licon/yellow/18/left.png" onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IKdGutsNr" id="MainInputField" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'getXMLComplete', MainInputField.value, '', '', null, showKdGutsAll) ;}else{return true ;} return false ; "/>
							</td>
							<td>
								<input type="image" src="/licon/yellow/18/right.png" onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button type="button" data-dojo-type="dijit/form/Button" onclick="selKdGuts( 'Base', 'KdGuts', document.forms['KdGutsKeyData']._IKdGutsNr.value, 'getXMLComplete', showKdGutsAll) ; return false ; ">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
							<td>
								<div id="lockStateKdGuts"></div>
							</td>
							<td class="image">
								<div id="pdfKdGuts">
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="CuCredC3s1c2" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="KdGutsDataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="KdGutsDataTCMain" data-dojo-type="dijit/layout/ContentPane" title="Allgemein">
				<div id="content">
					<div id="maindata">
						<form name="formKdGutsMain" id="formKdGutsMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEditDbl( FTr::tr( "Customer no."), "_IKundeNr", 11, 11, "", "_IKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'KdGuts', document.forms['KdGutsKeyData']._IKdGutsNr.value, 'setKundeFromKKId', showKdGutsAll)") ;
								rowEditDbl( FTr::tr( "Dlvr. customer no."), "_ILiefKundeNr", 11, 11, "", "_ILiefKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'KdGuts', document.forms['KdGutsKeyData']._IKdGutsNr.value, 'setLiefKundeFromKKId',showKdGutsAll)") ;
								rowEditDbl( FTr::tr( "Invc. customer no."), "_IRechKundeNr", 11, 11, "", "_IRechKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'KdGuts', document.forms['KdGutsKeyData']._IKdGutsNr.value, 'setRechKundeFromKKId',showKdGutsAll)") ;
								rowDate( FTr::tr( "Date"), "_IDatumKdGuts", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Ref. no."), "_IRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Ref. date"), "_IRefDatumKdGuts", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Customer ref. no."), "_IKdRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Customer ref. Date"), "_IKdRefDatumKdGuts", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Delivery date"), "_ILieferTermin", 24, 32, "", "") ;
//								rowDate( "Latest Invoicing date:", "_ILatestKdRechDatum", 24, 32, "", "") ;
								rowOption( FTr::tr( "Delivery mode"), "_IModusLief", Opt::getRModusLief(), "", "") ;
								rowOption( FTr::tr( "Invoicing mode"), "_IModusRech", Opt::getRModusRech(), "", "") ;
								rowOption( FTr::tr( "Payment mode"), "_IModusZahl", Opt::getRModusZahl(), "", "") ;
								rowOption( FTr::tr( "Skonto mode"), "_IModusSkonto", Opt::getRModusSkonto(), "", "") ;
								rowOption( FTr::tr( "Skonto method"), "_IModusSkAbzug", Opt::getRModusSkAbzug(), "", "") ;
								rowEdit( FTr::tr( "Items"), "_IPositionen", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Items direct delivery"), "_IPosDirVers", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Total value"), "_FGesamtPreis", 10, 10, "", "") ;
								rowEdit( FTr::tr( "S&H flatrate"), "_FVersPausch", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Total tax"), "_FGesamtMwst", 10, 10, "", "") ;
								rowOption( FTr::tr( "Discount mode"), "_IDiscountMode", Opt::getRDMLevel(), "", "") ;
								rowEdit( FTr::tr( "Discount"), "_FRabatt", 10, 10, "", "") ;
								rowOption( FTr::tr( "Total status"), "_IStatus", KdGuts::getRStatus(), "", "") ;
							?></table>
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'upd', _IKdGutsNr.value, '', '', 'formKdGutsMain', showKdGuts) ; return false ; ">
								<?php echo FTr::tr( "Update ...") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="KdGutsDataTCKunde" data-dojo-type="dijit/layout/ContentPane" title="Anschriften">
				<div id="content">
					<div id="maindata">
						<?php
							compCust( "KdGutsCPKunde", "Credit Note-", "KdGuts", "", "", "") ;
						?>
					</div>
				</div>
			</div>
			<div id="KdGutsDataTCPosten" data-dojo-type="dijit/layout/ContentPane" title="Posten">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onclick="selArtikelVK( 'Base', 'KdGuts', document.forms['KdGutsKeyData']._IKdGutsNr.value, 'addPos', showTableKdGutsPosten) ;">
							<?php echo FTr::tr( "New item ...") ; ?>
						</button>
					</div>
				</div>
				<div id="TableKdGutsPosten">
				</div>
			</div>
			<div id="KdGutsDataTCVersand" data-dojo-type="dijit/layout/ContentPane" title="Versand">
				<div id="KdGutsDataTCVersandTC" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="KdGutsDataTCVersandTCEMail" data-dojo-type="dijit/layout/ContentPane" title="eMail">
						<div id="content">
							<div id="maindata">
								<form method="post" name="formKdGutsDocEMail" id="formKdGutsDocEMail" onsubmit="return false ;" >
									<table><?php
										rowEdit( FTr::tr( "E-Mail"), "_IeMail", 32, 64, "", "") ;
										rowOption( FTr::tr( "Status"), "_IStatus", KdGuts::getRStatus(), "") ;
										rowOption( FTr::tr( "Sent by"), "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowHTMLEdit2( FTr::tr( "Text"), "_RAnschreibenKdGuts", 64, 5, "",
														FTr::tr( "HELP-KDBEST-EMAIL"),
														"showEMailKdGuts()", "hideEMailKdGuts()") ;
										?></table> 
									<button data-dojo-type="dijit/form/Button" 
										onclick="requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'updAnschreiben', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', 'formKdGutsDocEMail', showKdGuts) ; return false ; ">
										<?php echo FTr::tr( "Update") ; ?>
									</button>
									<button data-dojo-type="dijit/form/Button"
										onclick="requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'sendByEMail', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', 'formKdGutsDocEMail', showKdGuts) ; return false ; ">
										<?php echo FTr::tr( "Send E-Mail") ; ?>
									</button>
									<button data-dojo-type="dijit/form/Button"
										onclick="requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'setAnschreiben', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', 'formKdGutsDocEMail', showKdGuts) ; return false ; ">
										<?php echo FTr::tr( "Default text") ; ?>
									</button>
								</form> 
							</div>
						</div>
					</div>
					<div id="KdGutsDataTCVersandTCFax" data-dojo-type="dijit/layout/ContentPane" title="FAX">
						<div id="content">
							<div id="maindata">
								<form method="post" name="formKdGutsDocFAX" id="formKdGutsDocFAX" onsubmit="return false ;" >
									<table><?php
										rowEdit( FTr::tr( "Fax"), "_IFAX", 32, 64, "", "") ;
										rowOption( FTr::tr( "Status"), "_IStatus", KdGuts::getRStatus(), "") ;
										rowOption( FTr::tr( "Sent by"), "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowTextEdit( FTr::tr( "FAX-Server command"), "_IPlainText", 64, 5,
														"<request>\n".
														"<auth>\n".
														"<account>10663</account>\n".
														"<password>s4dwe84e</password>\n".
														"</auth>\n".
														"<fax>\n".
														"<fax-id>KdGuts </fax-id>\n".
														"<to></to>\n".
														"<from>022359949230</from>\n".
														"<station-id>022359949230</station-id>\n".
														"<retry>3</retry>\n".
														"<header>MODIS GmbH</header>\n".
														"</fax>\n".
														"</request>\n",
														"XML Text. Wird fuer die eigentliche E-Mail, Text und HTML Version,<br/>".
														"nach Plain Text uebersetzt und so wie hier geschrieben als HTML uebernommen.",
														"onkeypress='mark( _IAnschreiben) ;'") ;
										rowEdit( FTr::tr( "To FAX no."), "_IFaxNr", 24, 32, "", "", "") ;
										rowEdit( FTr::tr( "Via E-Mail"), "VOID", 24, 32, "", "", "") ;
										rowEdit( FTr::tr( "FAX Server E-Mail"), "_IfaxServerEMail", 24, 32, "", "", "") ;
										?></table> 
									<button data-dojo-type="dijit/form/Button"
										onclick="requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'sendByFax', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', 'formKdGutsDocFAX', showKdGuts) ; return false ; ">
										<?php echo FTr::tr( "Send fax") ; ?>
									</button>
								</form> 
							</div>
						</div>
					</div>
					<div id="KdGutsDataTCVersandTCPDF" data-dojo-type="dijit/layout/ContentPane" title="als PDF">
						<div id="content">
							<div id="maindata">
								<form method="post" name="formKdGutsDocPDF" id="formKdGutsDocPDF" enctype="multipart/form-data">
									<table><?php
										rowOption( FTr::tr( "Status"), "_IStatus", KdGuts::getRStatus(), "") ;
										rowOption( FTr::tr( "Sent by"), "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										?></table> 
									<input type="button" value="Fax schicken" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'sendByFax', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', 'formKdGutsDocFAX', showKdGuts) ; return false ; ">
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="TabKdGutsDoc" data-dojo-type="dijit/layout/ContentPane" title="Dokumente">
				<div id="TabKdGutsDocCont" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="TabKdGutsDocTable" data-dojo-type="dijit/layout/ContentPane" title="&Uuml;bersicht" onShow="requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'getDocListAsXML', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', null, showKdGutsDocList) ; return false ; ">
						<div id="TableKdGutsDocs">
						</div>
					</div>
					<div id="TabKdGutsDocUpload" data-dojo-type="dijit/layout/ContentPane" title="Upload">
						<div id="content">
							<div id="maindata">
								<form action="/Base/KdGuts/upKdGutsDoc.php" method="post" name="formKdGutsDocUpload" id="formKdGutsDocUpload" target="_result" enctype="multipart/form-data">
									<input type="text" name="_DKdGutsNr" value="" />
									<table><?php
										rowOption( FTr::tr( "Dokument type"), "_IDocTypeKdGuts", KdGuts::getRDocType(), "AA", "") ;
										rowFile( FTr::tr( "FIlename"), "_IFilename", 24,32, "", "") ;
										?></table> 
									<button data-dojo-type="dijit/form/Button" type="submit">
										<?php echo FTr::tr( "Upload") ; ?>
									</button>
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="KdGutsDataTCFunktionen" data-dojo-type="dijit/layout/ContentPane" title="Funktionen">
			</div>
		</div>
	</div>
</div>