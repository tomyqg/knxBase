<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="KdMahnRoot" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="KdMahnKey" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="KdMahnKeyData" id="KdMahnKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Payment reminder") ; ?>:&nbsp;</th>
							<td>
								<input type="image" src="/licon/yellow/18/left.png" onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IKdMahnNr" id="MainInputField" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'getXMLComplete', MainInputField.value, '', '', null, showKdMahnAll) ;}else{return true ;} return false ; "/>
							</td>
							<td>
								<input type="image" src="/licon/yellow/18/right.png" onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<input type="button" name="btnSelKdMahn" value="Select ..." border="0" onclick="selKdMahn( 'Base', 'KdMahn', document.forms['KdMahnKeyData']._IKdMahnNr.value, 'getXMLComplete', showKdMahnAll) ; return false ; "/> 
							</td>
							<td>
								<div id="lockStateKdMahn"></div>
							</td>
							<td class="image">
								<div id="pdfKdMahn">
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="KdMahnData" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="KdMahnDataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="KdMahnDataTCMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formKdMahnMain" id="formKdMahnMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEditDbl( FTr::tr( "Customer no."), "_IKundeNr", 11, 11, "", "_IKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'KdMahn', document.forms['KdMahnKeyData']._IKdMahnNr.value, 'setKundeFromKKId', showKdMahnAll)") ;
								rowDate( FTr::tr( "Date"), "_IDatumKdMahn", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Invoice no."), "_IKdRechNr", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Total net"), "_FPreis", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Total taxes"), "_FMwst", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Total value"), "_FGesamtPreis", 10, 10, "", "") ;
								rowOption( FTr::tr( "Total status"), "_IStatus", KdMahn::getRStatus(), "", "") ;
							?></table>
							<p> 
							<input type="button" value="aktualisieren" tabindex="14" border="0" onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'upd', _IKdMahnNr.value, '', '', 'formKdMahnMain', showKdMahn) ; return false ; ">
							</p> 
						</form> 
					</div>
				</div>
			</div>
			<div id="KdMahnDataTCKunde" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Addresses") ; ?>">
				<div id="content">
					<div id="maindata">
						<?php
							compCust( "KdMahnCPKunde", "Invoice-", "KdMahn", "", "", "") ;
						?>
					</div>
				</div>
			</div>
			<div id="KdMahnDataTCModi" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Modi") ; ?>">
				<div id="content">
					<div id="maindata">
						<form action="KundeBearb.php" method="post" name="formKdMahnModi" id="formKdMahnModi" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowHTMLEdit2( "Prefix:", "_RPrefixKdMahn", 64, 5, "", "") ;
								rowHTMLEdit2( "Postfix:", "_RPostfixKdMahn", 64, 5, "", "") ;
								rowTextEdit( "Bemerkungen:", "_IRem1", 64, 3, "", "", "") ;
								?></table> 
							<input type="button" value="aktualisieren" tabindex="14" border="0" onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'upd', _IKdMahnNr.value, '', '', 'formKdMahnModi', showKdMahn) ; return false ; ">
							<input type="button" value="Standardtext" tabindex="14" border="0"
								onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'setTexte', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '', 'formKdMahnModi', showKdMahn) ; return false ; ">
						</form> 
					</div>
				</div>
			</div>
			<div id="KdMahnDataTCPosten" data-dojo-type="dijit/layout/ContentPane" title="Posten">
<!--
	tell the artikel selector to:
	use an object of classe "KdMahn" with the key of the "MainInputField" (KdMahnNr)
	with this run the script "/Common/hdlObject.php" to call the method "KdMahn".addPos and when done
	run the javascript function "showKdMahnPosten"
-->
				<div id="content">
					<div id="maindata">
						<input type="button" name="addPos" value="Neue Position" onclick="selArtikelVK( 'Base', 'KdMahn', document.forms['KdMahnKeyData']._IKdMahnNr.value, 'addPos', showTableKdMahnPosten) ;" />
						<input type="button" value="Reservieren" onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'buche', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '0', null, showTableKdMahnPosten) ; return false ; ">
						<input type="button" value="Un-Reservieren" onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'unbuche', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '0', null, showTableKdMahnPosten) ; return false ; ">
						<input type="button" value="Konsolidieren" onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'consolidate', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '0', null, showTableKdMahnPosten) ; return false ; ">
					</div>
				</div>
				<div id="TableKdMahnPosten">
				</div>
			</div>
			<div id="KdMahnDataTCVersand" data-dojo-type="dijit/layout/ContentPane" title="Versand">
				<div id="KdMahnDataTCVersandTC" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="KdMahnDataTCVersandTCEMail" data-dojo-type="dijit/layout/ContentPane" title="eMail">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formKdMahnDocEMail" id="formKdMahnDocEMail" onsubmit="return false ;" >
									<table><?php
										rowEdit( "E-Mail:", "_IeMail", 32, 64, "", "") ;
										rowOption( "Status:", "_IStatus", KdMahn::getRStatus(), "") ;
										rowOption( "Versand per:", "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowHTMLEdit2( "Anschreiben:", "_RAnschreibenKdMahn", 64, 5, "",
														FTr::tr( "HELP-KdMahn-EMAIL"),
														"showEMailKdMahn()", "hideEMailKdMahn()") ;
										?></table> 
									<input type="button" value="Anschreiben aktualisieren" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'updAnschreiben', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '', 'formKdMahnDocEMail', showKdMahn) ; return false ; ">
									<input type="button" value="E-Mail schicken" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'sendByEMail', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '', 'formKdMahnDocEMail', showKdMahn) ; return false ; ">
									<input type="button" value="Standardtext" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'setAnschreiben', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '', 'formKdMahnDocEMail', showKdMahn) ; return false ; ">
								</form> 
							</div>
						</div>
					</div>
					<div id="KdMahnDataTCVersandTCFax" data-dojo-type="dijit/layout/ContentPane" title="FAX">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formKdMahnDocFAX" id="formKdMahnDocFAX" onsubmit="return false ;" >
									<table><?php
										rowEdit( "Fax:", "_IFAX", 32, 64, "", "") ;
										rowOption( "Status:", "_IStatus", KdMahn::getRStatus(), "") ;
										rowOption( "Versand per:", "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowTextEdit( "FAX-Server Befehl:", "_IPlainText", 64, 5,
														"<request>\n".
														"<auth>\n".
														"<account>10663</account>\n".
														"<password>s4dwe84e</password>\n".
														"</auth>\n".
														"<fax>\n".
														"<fax-id>KdMahn </fax-id>\n".
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
										onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'sendByFax', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '', 'formKdMahnDocFAX', showKdMahn) ; return false ; ">
								</form> 
							</div>
						</div>
					</div>
					<div id="KdMahnDataTCVersandTCPDF" data-dojo-type="dijit/layout/ContentPane" title="als PDF">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formKdMahnDocPDF" id="formKdMahnDocPDF" enctype="multipart/form-data">
									<table><?php
										rowDisplay( "Auftrags Nr.:", "_IKdMahnNr", 6, 6, "", "") ;
										rowOption( "Status:", "_IStatus", KdMahn::getRStatus(), "") ;
										rowOption( "Versand per:", "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										?></table> 
									<input type="button" value="Fax schicken" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'sendByFax', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '', 'formKdMahnDocFAX', showKdMahn) ; return false ; ">
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="KdMahnDataTCFunktionen" data-dojo-type="dijit/layout/ContentPane" title="Funktionen">
			</div>
		</div>
	</div>
</div>
