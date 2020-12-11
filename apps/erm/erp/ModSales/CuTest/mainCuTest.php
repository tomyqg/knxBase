<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="KdLeihRoot" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="KdLeihKey" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="KdLeihKeyData" id="KdLeihKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Testdrive no.") ; ?>:&nbsp;</th>
							<td>
								<input type="image" src="/licon/yellow/18/left.png" onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IKdLeihNr" id="_IKdLeihNr" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'getXMLComplete', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', null, showKdLeihAll) ;}else{return true ;} return false ; "/>
							</td>
							<td>
								<input type="image" src="/licon/yellow/18/right.png" onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" value="Select ..." onclick="selKdLeih( 'Base', 'KdLeih', document.forms['KdLeihKeyData']._IKdLeihNr.value, 'getXMLComplete', showKdLeihAll) ; return false ; ">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
							<td>
								<div id="lockStateKdLeih"></div>
							</td>
							<td class="image">
								<input type="image" src="/licon/Blue/32/object_04.png" onclick="newKdKommFromKdLeih( document.forms['KdLeihKeyData']._IKdLeihNr.value, '') ; return false ;" title="Kommission erstellen" />
							</td>
							<td class="image">
								<div id="pdfKdLeih">
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="KdLeihData" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="KdLeihDataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="KdLeihDataTCMain" data-dojo-type="dijit/layout/ContentPane" title="Allgemein">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formKdLeihMain" id="formKdLeihMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEditDbl( FTr::tr( "Customer no."), "_IKundeNr", 11, 11, "", "_IKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'KdLeih', document.forms['KdLeihKeyData']._IKdLeihNr.value, 'setKundeFromKKId', showKdLeihAll)") ;
								rowEditDbl( FTr::tr( "Delivery cust. no."), "_ILiefKundeNr", 11, 11, "", "_ILiefKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'KdLeih', document.forms['KdLeihKeyData']._IKdLeihNr.value, 'setLiefKundeFromKKId',showKdLeihAll)") ;
								rowEditDbl( FTr::tr( "Invoicing cust. no."), "_IRechKundeNr", 11, 11, "", "_IRechKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'KdLeih', document.forms['KdLeihKeyData']._IKdLeihNr.value, 'setRechKundeFromKKId',showKdLeihAll)") ;
								rowDate( FTr::tr( "Date"), "_IDatumKdLeih", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Ref. no."), "_IRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Ref. date"), "_IRefDatumKdLeih", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Customer ref. no."), "_IKdRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Customer ref. Date"), "_IKdRefDatumKdLeih", 10, 10, "", "") ;
								rowDate( FTr::tr( "Return date"), "_IDatumZurueck", 10, 10, "", "") ;
								rowDate( FTr::tr( "Latest Invoicing date"), "_ILatestKdRechDatum", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Packages"), "_IAnzahlPakete", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Items"), "_IPositionen", 10, 10, "", "") ;
								rowOption( FTr::tr( "Carrier"), "_ICarrier", Carr::getRCarrier(), "0") ;
								rowOption( FTr::tr( "Total status"), "_IStatus", KdLeih::getRStatus(), "", "") ;
							?></table>
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'upd', _IKdLeihNr.value, '', '', 'formKdLeihMain', showKdLeih) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="KdLeihDataTCKunde" data-dojo-type="dijit/layout/ContentPane" title="Anschriften">
				<div id="KdLeihDataTCKundeTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<?php
						compCust( "KdLeihCPKunde", "Order-", "KdLeih", "", "Lief", "Rech") ;
						compCust( "KdLeihCPLiefKunde", "Delivery-", "KdLeih", "Lief", "", "") ;
						compCust( "KdLeihCPRechKunde", "Invoicing-", "KdLeih", "Rech", "", "") ;
					?>
				</div>
			</div>
			<div id="KdLeihDataTCModi" data-dojo-type="dijit/layout/ContentPane" title="Verkaufsmodalit&auml;ten">
				<div id="content">
					<div id="maindata">
						<form action="KundeBearb.php" method="post" name="formKdLeihModi" id="formKdLeihModi" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Versandpausch."), "_IVersPausch", 10, 10, "", "") ;
								rowOption( FTr::tr( "Status"), "_IStatus", KdLeih::getRStatus(), "0") ;
								rowHTMLEdit2( FTr::tr( "Prefix"), "_RPrefixKdLeih", 64, 5, "", "") ;
								rowHTMLEdit2( FTr::tr( "Postfix"), "_RPostfixKdLeih", 64, 5, "", "") ;
								rowTextEdit( FTr::tr( "Remarks"), "_IRem1", 64, 3, "", "", "") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'upd', _IKdLeihNr.value, '', '', 'formKdLeihModi', showKdLeih) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button" 
								onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'setTexte', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', 'formKdLeihModi', showKdLeih) ; return false ; ">
								<?php echo FTr::tr( "Default text") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="KdLeihDataTCPosten" data-dojo-type="dijit/layout/ContentPane" title="Posten">
<!--
	tell the artikel selector to:
	use an object of classe "KdLeih" with the key of the "MainInputField" (KdLeihNr)
	with this run the script "/Common/hdlObject.php" to call the method "KdLeih".addPos and when done
	run the javascript function "showKdLeihPosten"
-->
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onclick="selArtikelVK( 'Base', 'KdLeih', document.forms['KdLeihKeyData']._IKdLeihNr.value, 'addPos', showTableKdLeihPosten) ;" />
							<?php echo FTr::tr( "New item ...") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'buche', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '0', null, showTableKdLeihPosten) ; return false ; ">
							<?php echo FTr::tr( "Book") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'unbuche', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '0', null, showTableKdLeihPosten) ; return false ; ">
							<?php echo FTr::tr( "Un-Book") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'consolidate', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '0', null, showTableKdLeihPosten) ; return false ; ">
							<?php echo FTr::tr( "Consolidate") ; ?>
						</button>
						<?php tableBlock( "refKdLeihPosten", "formKdLeihPostenTop") ; ?>
					</div>
						<div id="KdLeihPostenRoot">
							<table id="TableKdLeihPosten" eissClass="KdLeihPosten">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="PosNr">Item</th>
										<th eissAttribute="SubPosNr">Sub-Item</th>
										<th eissAttribute="ArtikelNr" eissLinkTo="screenArtikel" colspan="2">Article no.</th>
										<th eissAttribute="ERPNo" eissLinkTo="screenArtikel" colspan="2">ERP no.</th>
										<th eissAttribute="ArtikelBez1">Description</th>
										<th eissAttribute="Menge" eissFunctions="step,input" colspan="3">Qty. ordered</th>
										<th eissAttribute="MengeProVPE" >Qty. / pack</th>
										<th eissAttribute="MengeGebucht" >Qty. booked</th>
										<th eissAttribute="GelieferteMenge" eissFunctions="step,input" colspan="3">Qty. delivered</th>
										<th eissAttribute="MengeZurueck" eissFunctions="step,input" colspan="3">Qty. back</th>
										<th eissAttribute="Preis">Price / item</th>
										<th eissAttribute="RefPreis">Ref.Price / item</th>
										<th eissAttribute="GesamtPreis">Total Price</th>
										<th colspan="5" eissFunctions="edit,move,colex,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					<div id="maindata">
						<?php tableBlock( "refKdLeihPosten", "formKdLeihPostenBot") ; ?>
					</div>
				</div>
			</div>
			<div id="KdLeihDataTCVersand" data-dojo-type="dijit/layout/ContentPane" title="Versand">
				<div id="KdLeihDataTCVersandTC" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="KdLeihDataTCVersandTCEMail" data-dojo-type="dijit/layout/ContentPane" title="eMail">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formKdLeihDocEMail" id="formKdLeihDocEMail" onsubmit="return false ;" >
									<table><?php
										rowEdit( FTr::tr( "E-Mail"), "_IeMail", 32, 64, "", "") ;
										rowOption( FTr::tr( "Status"), "_IStatus", KdLeih::getRStatus(), "") ;
										rowOption( FTr::tr( "Sent by"), "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowHTMLEdit2( FTr::tr( "Text"), "_RAnschreibenKdLeih", 64, 5, "",
														FTr::tr( "HELP-KDBEST-EMAIL"),
														"showEMailKdLeih()", "hideEMailKdLeih()") ;
										?></table> 
									<button data-dojo-type="dijit/form/Button" 
										onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'updAnschreiben', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', 'formKdLeihDocEMail', showKdLeih) ; return false ; ">
										<?php echo FTr::tr( "Update") ; ?>
									</button>
									<button data-dojo-type="dijit/form/Button" 
										onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'sendByEMail', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', 'formKdLeihDocEMail', showKdLeih) ; return false ; ">
										<?php echo FTr::tr( "Send e-Mail") ; ?>
									</button>
									<button data-dojo-type="dijit/form/Button" 
										onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'setAnschreiben', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', 'formKdLeihDocEMail', showKdLeih) ; return false ; ">
										<?php echo FTr::tr( "Default text") ; ?>
									</button>
								</form> 
							</div>
						</div>
					</div>
					<div id="KdLeihDataTCVersandTCFax" data-dojo-type="dijit/layout/ContentPane" title="FAX">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formKdLeihDocFAX" id="formKdLeihDocFAX" onsubmit="return false ;" >
									<table><?php
										rowEdit( FTr::tr( "Fax"), "_IFAX", 32, 64, "", "") ;
										rowOption( FTr::tr( "Status"), "_IStatus", KdLeih::getRStatus(), "") ;
										rowOption( FTr::tr( "Sent by"), "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowTextEdit( FTr::tr( "FAX-Server command"), "_IPlainText", 64, 5,
														"<request>\n".
														"<auth>\n".
														"<account>10663</account>\n".
														"<password>s4dwe84e</password>\n".
														"</auth>\n".
														"<fax>\n".
														"<fax-id>KdLeih </fax-id>\n".
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
										rowEdit( FTr::tr( "To FAX Nr."), "_IFaxNr", 24, 32, "", "", "") ;
										rowEdit( FTr::tr( "Via E-Mail"), "VOID", 24, 32, "", "", "") ;
										rowEdit( FTr::tr( "FAX Server E-Mail"), "_IfaxServerEMail", 24, 32, "", "", "") ;
										?></table> 
									<button data-dojo-type="dijit/form/Button" 
										onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'sendByFax', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', 'formKdLeihDocFAX', showKdLeih) ; return false ; ">
										<?php echo FTr::tr( "Send FAX") ; ?>
									</button>
								</form> 
							</div>
						</div>
					</div>
					<div id="KdLeihDataTCVersandTCPDF" data-dojo-type="dijit/layout/ContentPane" title="als PDF">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formKdLeihDocPDF" id="formKdLeihDocPDF" enctype="multipart/form-data">
									<table><?php
										rowDisplay( FTr::tr( "Testdrive no."), "_IKdLeihNr", 6, 6, "", "") ;
										rowOption( FTr::tr( "Status"), "_IStatus", KdLeih::getRStatus(), "") ;
										rowOption( FTr::tr( "Sent by"), "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										?></table> 
									<button data-dojo-type="dijit/form/Button" 
										onclick="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'sendByFax', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', 'formKdLeihDocFAX', showKdLeih) ; return false ; ">
									</button>
										<?php echo FTr::tr( "Send FAX") ; ?>
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="TabKdLeihDoc" data-dojo-type="dijit/layout/ContentPane" title="Dokumente">
				<div id="TabKdLeihDocCont" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="TabKdLeihDocTable" data-dojo-type="dijit/layout/ContentPane" title="&Uuml;bersicht" onShow="requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'getDocListAsXML', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', null, showKdLeihDocList) ; return false ; ">
						<div id="TableKdLeihDocs">
						</div>
					</div>
					<div id="TabKdLeihDocUpload" data-dojo-type="dijit/layout/ContentPane" title="Upload">
						<div id="content">
							<div id="maindata">
								<form action="/Base/KdLeih/upKdLeihDoc.php" method="post" name="formKdLeihDocUpload" id="formKdLeihDocUpload" target="_result" enctype="multipart/form-data">
									<input type="text" name="_DKdLeihNr" value="" />
									<table><?php
										rowOption( FTr::tr( "Document type"), "_IDocTypeKdLeih", KdLeih::getRDocType(), "AA", "") ;
										rowFile( FTr::tr( "Filename"), "_IFilename", 24,32, "", "") ;
										?></table> 
									<input type="submit" value="Upload" tabindex="14" border="0" >
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="KdLeihDataTCFunktionen" data-dojo-type="dijit/layout/ContentPane" title="Funktionen">
			</div>
		</div>
	</div>
</div>
