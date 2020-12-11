<?php
require_once( "config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "option.php") ;
require_once( "CuRMA.php") ;
require_once( "common.php") ;
?>
<div id="CuRMARoot" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="CuRMAKey" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="CuRMAKeyData" id="CuRMAKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th>RMA no.:&nbsp;</th>
							<td><input type="image" src="/licon/yellow/18/left.png" name="prevCuRMA" onclick="prevCuRMA( MainInputField.value, '') ; return false ;" /></td>
							<td>
								<input type="text" name="_ICuRMANr" id="MainInputField" value="" onkeypress="if(event.keyCode==13){requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'getXMLComplete', MainInputField.value, '', '', null, showCuRMAAll) ;}else{return true ;} return false ; "/>
							</td>
							<td><input type="image" src="/licon/yellow/18/right.png" name="nextCuRMA" onclick="nextCuRMA( MainInputField.value, '') ; return false ;" /></td>
							<td>
								<input type="button" name="btnSelCuRMA" value="Select ..." border="0" onclick="selCuRMA( 'ModRMA', 'CuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'getXMLComplete', showCuRMAAll) ; return false ; "/> 
							</td>
							<td>
								<div id="lockStateCuRMA"></div>
							</td>
							<td class="space" width="100"></td>
							<td class="image">
								<input type="image" src="/rsrc/gif/back-icon.png" onclick="back( '', '') ; return false ;" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/new-icon.png" onclick="newCuRMA( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/copy-icon.png" disabled="1" onclick="newCuRMA( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/save-icon.png" onclick="saveCuRMA( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" title="Alle Daten speichern" />
							</td>
							<td class="image">
								<input type="image" src="/licon/Blue/32/feed.png" onclick="createDirBest( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" title="Direktbestellungen erzeugen" />
							</td>
							<td class="image">
								<input type="image" src="/licon/Blue/32/object_04.png" onclick="newKdKommFromCuRMA( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" title="Kommission erstellen" />
							</td>
							<td class="image">
								<input type="image" src="/licon/Blue/32/object_04.png" onclick="newKdRechFromCuRMAOL( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" title="Rechnung für alle offenen Lieferungen" />
							</td>
							<td class="image">
								<input type="image" src="/licon/Blue/32/object_04.png" onclick="newKdRechFromCuRMAA( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" title="Rechnung für alle (offenen) Positionen" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/druckenicon_large.jpg" onclick="createPdfCuRMA( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" title="PDF erzeugen" />
							</td>
							<td class="image">
								<div id="pdfCuRMA">
								</div>
							</td>
							<td class="image">
								<input type="image" src="/licon/yellow/32/Recycle.png" onclick="delCuRMA( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" title="Alle Daten l&ouml;schen"/>
							</td>
							<td class="image">
								<input type="image" src="/licon/Green/32/refresh.png" onclick="reloadScreenCuRMA( document.forms['CuRMAKeyData']._ICuRMANr.value, '') ; return false ;" title="Maske neu laden (DEBUG)" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="CuRMAData" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="CuRMADataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="CuRMADataTCMain" data-dojo-type="dijit/layout/ContentPane" title="Allgemein">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formCuRMAMain" id="formCuRMAMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEditDbl( FTr::tr( "Customer no."), "_IKundeNr", 11, 11, "", "_IKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'ModRMA', 'CuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'setKundeFromKKId', showCuRMAAll)") ;
								rowEditDbl( FTr::tr( "Dlvr. customer no."), "_ILiefKundeNr", 11, 11, "", "_ILiefKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'ModRMA', 'CuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'setLiefKundeFromKKId',showCuRMAAll)") ;
								rowEditDbl( FTr::tr( "Invc. customer no."), "_IRechKundeNr", 11, 11, "", "_IRechKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'ModRMA', 'CuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'setRechKundeFromKKId',showCuRMAAll)") ;
								rowDate( FTr::tr( "Date"), "_IDatumCuRMA", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Ref. no."), "_IRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Ref. date"), "_IRefDatumCuRMA", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Customer ref. no."), "_IKdRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Customer ref. Date"), "_IKdRefDatumCuRMA", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Items"), "_IItems", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Total value"), "_FGesamtPreis", 10, 10, "", "") ;
								rowEdit( FTr::tr( "S&H flatrate"), "_FVersPausch", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Total tax"), "_FGesamtMwst", 10, 10, "", "") ;
								rowOption( FTr::tr( "Discount mode"), "_IDiscountMode", Opt::getRDMLevel(), "", "") ;
								rowEdit( FTr::tr( "Discount"), "_FRabatt", 10, 10, "", "") ;
								rowOption( FTr::tr( "Total status"), "_IStatus", CuRMA::getRStatus(), "", "") ;
								rowOption( FTr::tr( "Delivery status"), "_IStatLief", CuRMA::getRStatLief(), "", "") ;
								rowOption( FTr::tr( "Invocing status"), "_IStatRech", CuRMA::getRStatRech(), "", "") ;
							?></table>
							<p> 
							<input type="button" value="aktualisieren" tabindex="14" border="0" onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'upd', _ICuRMANr.value, '', '', 'formCuRMAMain', showCuRMA) ; return false ; ">
							</p> 
						</form> 
					</div>
				</div>
			</div>
			<div id="CuRMADataTCKunde" data-dojo-type="dijit/layout/ContentPane" title="Anschriften">
				<div id="CuRMADataTCKundeTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="CuRMADataTCKundeTCKunde" data-dojo-type="dijit/layout/ContentPane" title="Auftrag">
						<div id="content">
							<div id="maindata">
								<table><tr><td>
									<form action="KundeBearb.php" method="post" id="formCuRMAKunde" name="formCuRMAKunde" enctype="multipart/form-data" onsubmit="return false ;" >
										<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
										<input type="image" src="/licon/Green/18/zoom.png" name=""
											onclick="gotoKunde( document.forms['formCuRMAMain']._IKundeNr.value, retToCuRMA, document.forms['CuRMAKeyData']._ICuRMANr.value) ;" />
										<table><?php
											kundeBlock( "selKunde( 'ModRMA', 'CuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'setKundeFromKKId', showCuRMAKunde)") ;
											?></table>
										<input type="submit" name="actionUpdateKunde" value="eradresse anlegen" tabindex="14" border="0">
										<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
									</form></td><td>
									<form action="KundeBearb.php" method="post" id="formCuRMAKundeKontakt" name="formCuRMAKundeKontakt" enctype="multipart/form-data" onsubmit="return false ;" >
										<table><?php
											kundeKontaktBlock() ;
											?></table>
										<input type="submit" name="actionUpdateKunde" value="erkontakt anlegen" tabindex="14" border="0">
										<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
									</form></td></tr>
								</table>
							</div>
						</div>
					</div>
					<div id="CuRMADataTCKundeTCLief" data-dojo-type="dijit/layout/ContentPane" title="Liefer">
						<div id="content">
							<div id="maindata">
								<table><tr><td>
									<form action="KundeBearb.php" method="post" id="formCuRMALiefKunde" name="formCuRMALiefKunde" enctype="multipart/form-data" onsubmit="return false ;" >
										<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
										<table><?php
											kundeBlock( "selKunde( 'ModRMA', 'CuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'setLiefKundeFromKKId', showCuRMAKunde)") ;
											?></table>
										<input type="submit" name="actionUpdateKunde" value="Lieferadresse anlegen" tabindex="14" border="0">
										<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
									</form></td><td>
									<form action="KundeBearb.php" method="post" id="formCuRMALiefKundeKontakt" name="formCuRMALiefKundeKontakt" enctype="multipart/form-data" onsubmit="return false ;" >
										<table><?php
											kundeKontaktBlock() ;
											?></table>
										<input type="submit" name="actionUpdateKunde" value="Lieferkontakt anlegen" tabindex="14" border="0">
										<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
									</form></td></tr>
								</table>
							</div>
						</div>
					</div>
					<div id="CuRMADataTCkundeTCRech" data-dojo-type="dijit/layout/ContentPane" title="Rechnung">
						<div id="content">
							<div id="maindata">
								<table><tr><td>
									<form action="KundeBearb.php" method="post" id="formCuRMARechKunde" name="formCuRMARechKunde" enctype="multipart/form-data" onsubmit="return false ;" >
										<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
										<table><?php
											kundeBlock( "selKunde( 'ModRMA', 'CuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'setRechKundeFromKKId', showCuRMAKunde)") ;
											?></table>
										<input type="submit" name="actionUpdateKunde" value="Rechnungsadresse anlegen" tabindex="14" border="0">
										<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
									</form></td><td>
									<form action="KundeBearb.php" method="post" id="formCuRMARechKundeKontakt" name="formCuRMARechKundeKontakt" enctype="multipart/form-data" onsubmit="return false ;" >
										<table><?php
											kundeKontaktBlock() ;
											?></table>
										<input type="submit" name="actionUpdateKunde" value="Rechnungskontakt anlegen" tabindex="14" border="0">
										<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
									</form></td></tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="CuRMADataTCModi" data-dojo-type="dijit/layout/ContentPane" title="Verkaufsmodalit&auml;ten">
				<div id="content">
					<div id="maindata">
						<form action="KundeBearb.php" method="post" name="formCuRMAModi" id="formCuRMAModi" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( "Versandpausch.:", "_IVersPausch", 10, 10, "", "") ;
								rowOption( "Status:", "_IStatus", CuRMA::getRStatus(), "0") ;
								rowOption( "Skonto Modus:", "_IModusSkonto", Opt::getRModusSkonto(), "") ;
								rowOption( "Liefermodus:", "_IModusLief", Opt::getRModusLief(), "") ;
								rowOption( "Rechungsrodus:", "_IModusRech", Opt::getRModusRech(), "") ;
								rowOption( "Rabattmodus:", "_IDiscountMode", Opt::getRDMLevel(), "") ;
								rowEdit( "Rabatt:", "_FRabatt", 10, 10, "", "") ;
								rowHTMLEdit( "Prefix:", "_RPrefixCuRMA", 64, 5, "", "") ;
								rowHTMLEdit( "Postfix:", "_RPostfixCuRMA", 64, 5, "", "") ;
								rowTextEdit( "Bemerkungen:", "_IRem1CuRMA", 64, 3, "", "", "") ;
								?></table> 
							<input type="button" value="aktualisieren" tabindex="14" border="0" onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'upd', _ICuRMANr.value, '', '', 'formCuRMAModi', showCuRMA) ; return false ; ">
							<input type="button" value="Standardtext" tabindex="14" border="0"
								onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'setTexte', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '', 'formCuRMAModi', showCuRMA) ; return false ; ">
						</form> 
					</div>
				</div>
			</div>
			<div id="CuRMADataTCPosten" data-dojo-type="dijit/layout/ContentPane" title="Posten">
				<div id="content">
					<div id="maindata">
						<input type="button" name="addPos" value="Neue Position" onclick="selArtikelVK( 'ModRMA', 'CuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'addPos', showTableCuRMAPosten) ;" />
						<input type="button" value="Reservieren" onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'buche', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '0', null, showCuRMAAll) ; return false ; ">
						<input type="button" value="Un-Reservieren" onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'unbuche', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '0', null, showCuRMAAll) ; return false ; ">
						<input type="button" value="Konsolidieren" onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'consolidate', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '0', null, showTableCuRMAPosten) ; return false ; ">
						<input type="button" value="Alle Reservieren" onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'bucheAll', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '0', null, showTableCuRMAPosten) ; return false ; ">
						<input type="button" value="Alle Un-Reservieren" onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'unbucheAll', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '0', null, showTableCuRMAPosten) ; return false ; ">
					</div>
				</div>
				<div id="tableCuRMAItems">
				</div>
			</div>
			<div id="CuRMADataTCKalkulation" data-dojo-type="dijit/layout/ContentPane" title="Kalkulation">
				<div id="content">
					<div id="maindata">
						<input type="button" value="Rabatt V1" onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'setCalcMode', document.forms['CuRMAKeyData']._ICuRMANr.value, '', 'v1', null, showCuRMAAll) ; return false ;" />
						<input type="button" value="Rabatt V2" onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'setCalcMode', document.forms['CuRMAKeyData']._ICuRMANr.value, '', 'v2', null, showCuRMAAll) ; return false ;" />
						<form action="KundeBearb.php" method="post" name="formCuRMACalc" enctype="multipart/form-data">
							<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
							<table><?php
								rowDispOption( "Rabattmodus:", "_IDiscountMode", Opt::getRDMLevel(), "") ;
								rowDisplay( "Gesamt netto:", "_FGesamtPreis", 12, 12, "","") ;
								rowDisplay( "Rabatt:", "_CRabatt", 12, 12, "", "") ;
								rowDisplay( "Gesamt netto nach Rabatt:", "_CNettoNachRabatt", 12, 12, "", "") ;
		//						rowDisplay( "MwSt. gesamt:", "_IGesamtMwst", 12, 12, "","") ;
		//						rowDisplay( "Gesamt brutto:", "VOID", 12, 12, "", "") ;
								rowDisplay( "Gesamt Einkauf:", "_FGesamtEKPreis", 12, 12, "", "") ;
								rowDisplay( "Rohmarge:", "_CRohmarge", 12, 12, "", "") ;
								?></table>
						</form>
					</div>
				</div>
			</div>
			<div id="CuRMADataTCVersand" data-dojo-type="dijit/layout/ContentPane" title="Versand">
				<div id="CuRMADataTCVersandTC" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="CuRMADataTCVersandTCEMail" data-dojo-type="dijit/layout/ContentPane" title="eMail">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formCuRMADocEMail" id="formCuRMADocEMail" onsubmit="return false ;" >
									<table><?php
										rowEdit( "E-Mail:", "_IeMail", 32, 64, "", "") ;
										rowOption( "Status:", "_IStatus", CuRMA::getRStatus(), "") ;
										rowOption( "Versand per:", "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowHTMLEdit( "Anschreiben:", "_RAnschreibenCuRMA", 64, 5, "",
														FTr::tr( "HELP-KDBEST-EMAIL"),
														"showEMailCuRMA()", "hideEMailCuRMA()") ;
										?></table> 
									<input type="button" value="Anschreiben aktualisieren" tabindex="14" border="0"
										onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'updAnschreiben', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '', 'formCuRMADocEMail', showCuRMA) ; return false ; ">
									<input type="button" value="E-Mail schicken" tabindex="14" border="0"
										onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'sendByEMail', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '', 'formCuRMADocEMail', showCuRMA) ; return false ; ">
									<input type="button" value="Standardtext" tabindex="14" border="0"
										onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'setAnschreiben', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '', 'formCuRMADocEMail', showCuRMA) ; return false ; ">
								</form> 
							</div>
						</div>
					</div>
					<div id="CuRMADataTCVersandTCFax" data-dojo-type="dijit/layout/ContentPane" title="FAX">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formCuRMADocFAX" id="formCuRMADocFAX" onsubmit="return false ;" >
									<table><?php
										rowEdit( "Fax:", "_IFAX", 32, 64, "", "") ;
										rowOption( "Status:", "_IStatus", CuRMA::getRStatus(), "") ;
										rowOption( "Versand per:", "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowTextEdit( "FAX-Server Befehl:", "_IPlainText", 64, 5,
														"<request>\n".
														"<auth>\n".
														"<account>10663</account>\n".
														"<password>s4dwe84e</password>\n".
														"</auth>\n".
														"<fax>\n".
														"<fax-id>CuRMA </fax-id>\n".
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
										rowEdit( "An FAX Nr.:", "_IFaxNr", 24, 32, "", "", "") ;
										rowEdit( "Via E-Mail:", "VOID", 24, 32, "", "", "") ;
										rowEdit( "FAX Server E-Mail:", "_IfaxServerEMail", 24, 32, "", "", "") ;
										?></table> 
									<input type="button" value="Fax schicken" tabindex="14" border="0"
										onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'sendByFax', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '', 'formCuRMADocFAX', showCuRMA) ; return false ; ">
								</form> 
							</div>
						</div>
					</div>
					<div id="CuRMADataTCVersandTCPDF" data-dojo-type="dijit/layout/ContentPane" title="als PDF">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formCuRMADocPDF" id="formCuRMADocPDF" enctype="multipart/form-data">
									<table><?php
										rowDisplay( "Auftrags Nr.:", "_ICuRMANr", 6, 6, "", "") ;
										rowOption( "Status:", "_IStatus", CuRMA::getRStatus(), "") ;
										rowOption( "Versand per:", "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										?></table> 
									<input type="button" value="Fax schicken" tabindex="14" border="0"
										onclick="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'sendByFax', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '', 'formCuRMADocFAX', showCuRMA) ; return false ; ">
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="TabCuRMADM" data-dojo-type="dijit/layout/ContentPane" title="Datamining">
				<div id="TabCuRMADMtc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="TabCuRMADMComm" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Commission") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerCuRMA', '/Common/hdlObject.php', 'getTableKdKommForCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'KdKommForCuRMA', 'KdKommNr', 'gotoKdKomm', 'retToCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerCuRMA', '/Common/hdlObject.php', 'getTableKdKommForCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'KdKommForCuRMA', 'KdKommNr', 'gotoKdKomm', 'retToCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value) ; return false ; " />
						<div id="KdKommForCuRMA"></div>
					</div>
					<div id="TabCuRMADMDlrv" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Deliveries") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerCuRMA', '/Common/hdlObject.php', 'getTableKdLiefForCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'KdLiefForCuRMA', 'KdLiefNr', 'gotoKdLief', 'retToCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerCuRMA', '/Common/hdlObject.php', 'getTableKdLiefForCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'KdLiefForCuRMA', 'KdLiefNr', 'gotoKdLief', 'retToCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value) ; return false ; " />
						<div id="KdLiefForCuRMA"></div>
					</div>
					<div id="TabCuRMADMInvc" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Invoices") ; ?>"
							onShow="requestDataMiner( 'Base', 'DataMinerCuRMA', '/Common/hdlObject.php', 'getTableKdRechForCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'KdRechForCuRMA', 'KdRechNr', 'gotoKdRech', 'retToCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value) ; return false ; ">
						<input type="button" value="Refresh ..." onClick="requestDataMiner( 'Base', 'DataMinerCuRMA', '/Common/hdlObject.php', 'getTableKdRechForCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value, 'KdRechForCuRMA', 'KdRechNr', 'gotoKdRech', 'retToCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value) ; return false ; " />
						<div id="KdRechForCuRMA"></div>
					</div>
				</div>
			</div>
			<div id="TabCuRMADoc" data-dojo-type="dijit/layout/ContentPane" title="Dokumente">
				<div id="TabCuRMADocCont" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="TabCuRMADocTable" data-dojo-type="dijit/layout/ContentPane" title="&Uuml;bersicht" onShow="requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'getDocListAsXML', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '', null, showCuRMADocList) ; return false ; ">
						<div id="TableCuRMADocs">
						</div>
					</div>
					<div id="TabCuRMADocUpload" data-dojo-type="dijit/layout/ContentPane" title="Upload">
						<div id="content">
							<div id="maindata">
								<form action="/ModRMA/CuRMA/upCuRMADoc.php" method="post" name="formCuRMADocUpload" id="formCuRMADocUpload" target="_result" enctype="multipart/form-data">
									<input type="text" name="_DCuRMANr" value="" />
									<table><?php
										rowOption( "Dokument Typ:", "_IDocTypeCuRMA", CuRMA::getRDocType(), "AA", "") ;
										rowFile( "Dateiname:", "_IFilename", 24,32, "", "") ;
										?></table> 
									<input type="submit" value="Upload" tabindex="14" border="0" >
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="CuRMADataTCFunktionen" data-dojo-type="dijit/layout/ContentPane" title="Funktionen">
			</div>
		</div>
	</div>
</div>
