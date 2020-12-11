<?php
require_once( "config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "option.php") ;
require_once( "common.php") ;
?>
<div id="TabProjMain" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="TabProjCPKeyData" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="ProjKeyData" id="ProjKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Project no.") ; ?></th>
							<td><input type="image" src="/licon/yellow/18/left.png" name="prevProj" onclick="prevProj( MainInputField.value, '') ; return false ;" /></td>
							<td>
								<input type="text" name="_IProjNr" id="MainInputField" value="" onkeypress="if(event.keyCode==13){requestUniXML( 'ModProj', 'Proj', '/Common/hdlObjectXML.php', 'getXMLComplete', MainInputField.value, '', '', null, showProjAll) ;}else{return true ;} return false ; "/>
							</td>
							<td><input type="image" src="/licon/yellow/18/right.png" name="nextProj" onclick="nextProj( MainInputField.value, '') ; return false ;" /></td>
							<td>
								<input type="button" name="btnSelProj" value="<?php echo FTr::tr( "Select ...") ; ?>" border="0" onclick="selProj( 'ModProj', 'Proj', document.forms['ProjKeyData']._IProjNr.value, 'getXMLComplete', showProjAll) ; return false ; "/> 
							</td>
							<td>
								<div id="lockStateProj"></div>
							</td>
							<td class="space" width="200"></td>
							<td class="image">
								<input type="image" src="/rsrc/gif/back-icon.png" onclick="back( '', '') ; return false ;" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/new-icon.png" onclick="newProj( '', '') ; return false ;" title="<?php echo FTr::tr( "New project, EMPTY") ; ?>"/>
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/copy-icon.png" onclick="newProjFromProj( document.forms['ProjKeyData']._IProjNr.value, '') ; return false ;" title="<?php echo FTr::tr( "New project, COPY") ; ?>"/>
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/save-icon.png" onclick="saveProj( document.forms['ProjKeyData']._IProjNr.value, '') ; return false ;" title="<?php echo FTr::tr( "Save all") ; ?>"/>
							</td>
							<td class="image">
								<input type="image" src="/licon/Blue/32/object_04.png" onclick="newKdBestFromProj( document.forms['ProjKeyData']._IProjNr.value, '') ; return false ;" title="<?php echo FTr::tr( "Convert project to order") ; ?>" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/druckenicon_large.jpg" onclick="createPdfProj( document.forms['ProjKeyData']._IProjNr.value, '') ; return false ;" title="<?php echo FTr::tr( "Create PDF") ; ?>" />
							</td>
							<td class="image">
								<div id="pdfProj">
								</div>
							</td>
							<td class="image">
								<input type="image" src="/licon/yellow/32/Recycle.png" onclick="delProj( document.forms['ProjKeyData']._IProjNr.value, '') ; return false ;" title="<?php echo FTr::tr( "Delete") ; ?>" />
							</td>
							<td class="image">
								<input type="image" src="/licon/Green/32/refresh.png" onclick="reloadScreenProj( document.forms['ProjKeyData']._IProjNr.value, '') ; return false ;" title="<?php echo FTr::tr( "Reload screen (DEBUG)") ; ?>" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="TabProjCPMainData" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="TabProjCPMainDataTCMain" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="TabProjCPMainDataCPMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formProjMain" id="formProjMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowDisplay( FTr::tr( "Id"), "_HId", 6, 6, "", "") ;
								rowDisplay( FTr::tr( "Project no."), "_IProjNr", 6, 6, "", "") ;
								rowEditDbl( FTr::tr( "Customer no."), "_IKundeNr", 11, 11, "", "_IKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'Proj', document.forms['ProjKeyData']._IProjNr.value, 'setKundeFromKKId', showProjAll)") ;
								rowEditDbl( FTr::tr( "Delivery cust. no."), "_ILiefKundeNr", 11, 11, "", "_ILiefKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'Proj', document.forms['ProjKeyData']._IProjNr.value, 'setLiefKundeFromKKId',showProjAll)") ;
								rowEditDbl( FTr::tr( "Invoice cust. no."), "_IRechKundeNr", 11, 11, "", "_IRechKundeKontaktNr", 3, 3, "", "", "",
											"selKunde( 'Base', 'Proj', document.forms['ProjKeyData']._IProjNr.value, 'setRechKundeFromKKId',showProjAll)") ;
								?></table> 
							<p> 
							<input type="button" value="<?php echo FTr::tr( "Create") ; ?>" tabindex="14" border="0" title="Ein neues Angebot wird angelegt mit Eingabewerten" onclick="return newProj( 'Base', 'Proj', 'newProj', _IProjNr.value, 'formProjMain') ; return false ; ">
							<input type="button" value="<?php echo FTr::tr( "Update") ; ?>" tabindex="14" border="0" onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'upd', _IProjNr.value, '', '', 'formProjMain', showProj) ; return false ; ">
							<input type="button" value="<?php echo FTr::tr( "Delete") ; ?>" tabindex="15" border="0" onclick="clrFields() ;" /> 
							</p> 
						</form> 
					</div>
				</div>
			</div>
			<div id="TabProjCPMainDataCPAdr" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Addresses") ; ?>">
				<div id="TabProjCPMainDataCPAdrTCAdr" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="TabProjCPMainDataCPAdrCPOffr" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Offer") ; ?>">
						<div id="content">
							<div id="maindata">
								<table><tr><td>
									<form action="KundeBearb.php" method="post" id="formProjKunde" name="formProjKunde" enctype="multipart/form-data" onsubmit="return false ;" >
										<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
										<table><?php
											kundeBlock( "selKunde( 'Base', 'Proj', document.forms['ProjKeyData']._IProjNr.value, 'setKundeFromKKId', showProjKunde)") ;
											?></table>
									</form></td><td>
									<form action="KundeBearb.php" method="post" id="formProjKundeKontakt" name="formProjKundeKontakt" enctype="multipart/form-data" onsubmit="return false ;" >
										<table><?php
											kundeKontaktBlock() ;
											?></table>
									</form></td></tr>
								</table>
								<input type="submit" name="actionUpdateKunde" value="<?php echo FTr::tr( "Create customer") ; ?>" tabindex="14" border="0"
									onclick="requestUniA( 'Base', 'Proj', '/Common/hdlObject.php', 'newKunde', document.forms['ProjKeyData']._IProjNr.value, '', '', new Array( 'formProjKunde', 'formProjKundeKontakt'), showProjAll) ; "><br/>
								<input type="submit" name="actionUpdateKunde" value="<?php echo FTr::tr( "Create contact") ; ?>" tabindex="14" border="0"
									onclick="requestUniA( 'Base', 'Proj', '/Common/hdlObject.php', 'newKundeKontakt', document.forms['ProjKeyData']._IProjNr.value, '', '', new Array( 'formProjKunde', 'formProjKundeKontakt'), showProjAll) ; ">
							</div>
						</div>
					</div>
					<div id="TabProjCPMainDataCPAdrCPDlvr" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Delivery") ; ?>">
						<div id="content">
							<div id="maindata">
								<table><tr><td>
									<form action="KundeBearb.php" method="post" id="formProjLiefKunde" name="formProjLiefKunde" enctype="multipart/form-data" onsubmit="return false ;" >
										<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
										<table><?php
											kundeBlock( "selKunde( 'Base', 'Proj', document.forms['ProjKeyData']._IProjNr.value, 'setLiefKundeFromKKId', showProjKunde)") ;
											?></table>
									</form></td><td>
									<form action="KundeBearb.php" method="post" id="formProjLiefKundeKontakt" name="formProjLiefKundeKontakt" enctype="multipart/form-data" onsubmit="return false ;" >
										<table><?php
											kundeKontaktBlock() ;
											?></table>
									</form></td></tr>
								</table>
								<input type="submit" name="actionUpdateKunde" value="<?php echo FTr::tr( "Create customer") ; ?>" tabindex="14" border="0"
									onclick="requestUniA( 'Base', 'Proj', '/Common/hdlObject.php', 'newLiefKunde', document.forms['ProjKeyData']._IProjNr.value, '', '', new Array('formProjLiefKunde', 'formProjLiefKundeKontakt'), showProjAll) ; "><br/>
								<input type="submit" name="actionUpdateKunde" value="<?php echo FTr::tr( "Create contact") ; ?>" tabindex="14" border="0"
									onclick="requestUniA( 'Base', 'Proj', '/Common/hdlObject.php', 'newLiefKundeKontakt', document.forms['ProjKeyData']._IProjNr.value, '', '', new Array('formProjLiefKunde', 'formProjLiefKundeKontakt'), showProjAll) ; ">
							</div>
						</div>
					</div>
					<div id="TabProjCPMainDataCPAdrCPInvc" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Invoice") ; ?>">
						<div id="content">
							<div id="maindata">
								<table><tr><td>
									<form action="KundeBearb.php" method="post" id="formProjRechKunde" name="formProjRechKunde" enctype="multipart/form-data" onsubmit="return false ;" >
										<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
										<table><?php
											kundeBlock( "selKunde( 'Base', 'Proj', document.forms['ProjKeyData']._IProjNr.value, 'setRechKundeFromKKId', showProjKunde)") ;
											?></table>
									</form></td><td>
									<form action="KundeBearb.php" method="post" id="formProjRechKundeKontakt" name="formProjRechKundeKontakt" enctype="multipart/form-data" onsubmit="return false ;" >
										<table><?php
											kundeKontaktBlock() ;
											?></table>
									</form></td></tr>
								</table>
								<input type="submit" name="actionUpdateKunde" value="<?php echo FTr::tr( "Create customer") ; ?>" tabindex="14" border="0"
									onclick="requestUniA( 'Base', 'Proj', '/Common/hdlObject.php', 'newRechKunde', document.forms['ProjKeyData']._IProjNr.value, '', '', new Array('formProjRechKunde', 'formProjRechKundeKontakt'), showProjAll) ; "><br/>
								<input type="submit" name="actionUpdateKunde" value="<?php echo FTr::tr( "Create contact") ; ?>" tabindex="14" border="0"
									onclick="requestUniA( 'Base', 'Proj', '/Common/hdlObject.php', 'newRechKundeKontakt', document.forms['ProjKeyData']._IProjNr.value, '', '', new Array('formProjRechKunde', 'formProjRechKundeKontakt'), showProjAll) ; ">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="TabProjCPMainDataCPMods" data-dojo-type="dijit/layout/ContentPane" title="Verkaufsmodalit&auml;ten">
				<div id="content">
					<div id="maindata">
						<form action="KundeBearb.php" method="post" name="formProjModi" id="formProjModi" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowDate( FTr::tr( "Date"), "_IDatumProj", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Cust. ref. no."), "_IKdRefNr", 16, 32, "", "") ;
								rowDate( FTr::tr( "Cust. ref. date"), "_IKdRefDatumProj", 10, 10, "", "") ;
								rowOption( FTr::tr( "Status"), "_IStatus", Proj::getRStatus(), "0") ;
								rowOption( FTr::tr( "Skonto mode"), "_IModusSkonto", Opt::getRModusSkonto(), "") ;
								rowOption( FTr::tr( "Delivery mode"), "_IModusLief", Opt::getRModusLief(), "") ;
								rowOption( FTr::tr( "Invoicing mode"), "_IModusRech", Opt::getRModusRech(), "") ;
								rowOption( FTr::tr( "Discount mode"), "_IDiscountMode", Opt::getRDMLevel(), "") ;
								rowEdit( FTr::tr( "Discount"), "_IRabatt", 10, 10, "", "") ;
								rowHTMLEdit( FTr::tr( "Prefix"), "_RPrefixProj", 64, 5, "", "", "onkeypress='mark( _RPrefixProj) ;'") ;
								rowHTMLEdit( FTr::tr( "Postfix"), "_RPostfixProj", 64, 5, "", "", "onkeypress='mark( _RPostfixProj) ;'") ;
								rowTextEdit( FTr::tr( "Remarks"), "_IRem1Proj", 64, 3, "", "", "onkeypress='mark( _IRem1Proj) ;'") ;
								?></table> 
							<input type="button" value="aktualisieren" tabindex="14" border="0" onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'upd', _IProjNr.value, '', '', 'formProjModi', showProj) ; return false ; ">
							<input type="button" value="Standardtext" tabindex="14" border="0"
								onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'setTexte', document.forms['ProjKeyData']._IProjNr.value, '', '', 'formProjModi', showProj) ; return false ; ">
						</form> 
					</div>
				</div>
			</div>
			<div id="TabProjCPMainDataCPItems" data-dojo-type="dijit/layout/ContentPane" title="Posten">
<!--
	tell the artikel selector to:
	use an object of classe "Proj" with the key of the "MainInputField" (ProjNr)
	with this run the script "/Common/hdlObject.php" to call the method "Proj".addPos and when done
	run the javascript function "showProjPosten"
-->
				<div id="content">
					<div id="maindata">
						<form method="post" name="formProjPos" id="formProjPos" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								?></table>
							<button type="button" data-dojo-type="dijit/form/Button" name="addPos" onclick="selKdAngById( 'ModProj', 'Proj', document.forms['ProjKeyData']._IProjNr.value, 'addPos', showTableProjPosten) ;">
								<?php echo FTr::tr( "New item") ; ?>
							</button>
						</form>
					</div>
				</div>
				<div id="TableProjPosten">
				</div>
			</div>
			<div id="TabProjCPMainDataCPCalc" data-dojo-type="dijit/layout/ContentPane" title="Kalkulation">
				<div id="content">
					<div id="maindata">
						<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'setCalcMode', document.forms['ProjKeyData']._IProjNr.value, '', 'v1', null, showProjAll) ; return false ;">
							<?php echo FTr::tr( "DIscount mode 1") ; ?>
						</button>
						<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'setCalcMode', document.forms['ProjKeyData']._IProjNr.value, '', 'v2', null, showProjAll) ; return false ;">
							<?php echo FTr::tr( "DIscount mode 2") ; ?>
						</button>
						<form action="KundeBearb.php" method="post" name="formProjCalc" enctype="multipart/form-data">
							<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
							<table><?php
								rowDisplay( FTr::tr( "Project no."), "_IProjNr", 6, 6, "", "") ;
								rowDispOption( FTr::tr( "Discount mode"), "_IDiscountMode", Opt::getRDMLevel(), "") ;
								rowDisplay( FTr::tr( "Total net"), "_FGesamtPreis", 12, 12, "","") ;
								rowDisplay( FTr::tr( "Discount"), "_FRabatt", 12, 12, "", "") ;
								rowDisplay( FTr::tr( "Total net after discount"), "_FNettoNachRabatt", 12, 12, "", "") ;
								rowDisplay( FTr::tr( "Total tax"), "_FGesamtMwst", 12, 12, "","") ;
								rowDisplay( FTr::tr( "Total gross"), "VOID", 12, 12, "", "") ;
								rowDisplay( FTr::tr( "Total purchase"), "_FGesamtEKPreis", 12, 12, "", "") ;
								rowDisplay( FTr::tr( "Gross margin"), "_FRohmarge", 12, 12, "", "") ;
								?></table>
						</form>
					</div>
				</div>
			</div>
			<div id="TabProjCPMainDataCPSubmit" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Versand") ; ?>">
				<div id="TabProjCPMainDataCPSubmitTCSubmit" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="TabProjCPMainDataCPSubmitCPEMail" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "eMail") ; ?>">
						<div id="content">
							<div id="maindata">
								<form method="post" name="formProjDocEMail" id="formProjDocEMail" onsubmit="return false ;" >
									<table><?php
										rowEdit( FTr::tr( "E-Mail"), "_IMail", 48, 128, "", "") ;
										rowEdit( FTr::tr( "E-Mail CC"), "_IMailCC", 48, 128, "", "") ;
										rowEdit( FTr::tr( "E-Mail BCC"), "_IMailBCC", 48, 128, "", "") ;
										rowOption( FTr::tr( "Status"), "_IStatus", Proj::getRStatus(), "") ;
										rowOption( FTr::tr( "Versand per"), "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowHTMLEdit( FTr::tr( "Anschreiben"), "_RAnschreibenProj", 64, 5, "",
													 "XML Text. Wird fuer die eigentliche E-Mail, Text und HTML Version,<br/>".
													"nach Plain Text uebersetzt und so wie hier geschrieben als HTML uebernommen.",
													"showEMailProj()", "hideEMailProj()") ;
										?></table> 
									<input type="button" value="Anschreiben aktualisieren" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'updAnschreiben', document.forms['ProjKeyData']._IProjNr.value, '', '', 'formProjDocEMail', showProj) ; return false ; ">
									<input type="button" value="E-Mail schicken (Anschreiben wird automatisch aktualisiert!)" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'sendByEMail', document.forms['ProjKeyData']._IProjNr.value, '', '', 'formProjDocEMail', showProj) ; return false ; ">
									<input type="button" value="Standardtext" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'setAnschreiben', document.forms['ProjKeyData']._IProjNr.value, '', '', 'formProjDocEMail', showProj) ; return false ; ">
									<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
								</form> 
							</div>
						</div>
					</div>
					<div id="TabProjCPMainDataCPSubmitCPFax" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "FAX") ; ?>">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formProjDocFAX" id="formProjDocFAX" onsubmit="return false ;" >
									<table><?php
										rowEdit( FTr::tr( "Fax"), "_IFAX", 32, 64, "", "") ;
										rowOption( FTr::tr( "Status"), "_IStatus", Proj::getRStatus(), "") ;
										rowOption( FTr::tr( "Send by"), "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowTextEdit( FTr::tr( "FAX server command"), "_IPlainText", 64, 5,
														"<request>\n".
														"<auth>\n".
														"<account>10663</account>\n".
														"<password>s4dwe84e</password>\n".
														"</auth>\n".
														"<fax>\n".
														"<fax-id>Proj </fax-id>\n".
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
										rowEdit( FTr::tr( "By E-Mail"), "VOID", 24, 32, "", "", "") ;
										rowEdit( FTr::tr( "Fax server E-Mail"), "_IfaxServerEMail", 24, 32, "", "", "") ;
										?></table> 
									<input type="button" value="<?php echo FTr::tr( "Fax schicken") ; ?>" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'sendByFax', document.forms['ProjKeyData']._IProjNr.value, '', '', 'formProjDocFAX', showProj) ; return false ; ">
								</form> 
							</div>
						</div>
					</div>
					<div id="TabProjCPMainDataCPSubmitCPPDF" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "as PDF") ; ?>">
						<div id="content">
							<div id="maindata">
								<form action="KundeBearb.php" method="post" name="formProjDocPDF" id="formProjDocPDF" enctype="multipart/form-data">
									<table><?php
										rowDisplay( FTr::tr( "Project no."), "_IProjNr", 6, 6, "", "") ;
										rowOption( FTr::tr( "Status"), "_IStatus", Proj::getRStatus(), "") ;
										rowOption( FTr::tr( "Send by"), "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										?></table> 
									<input type="button" value=<?php echo FTr::tr( "aktualisieren") ; ?>" tabindex="14" border="0" onclick="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'upd', _IProjNr.value, '', '', 'formProjDocPDF', showProj) ; return false ; ">
									<input type="reset" value=<?php echo FTr::tr( "Reset input fields") ; ?>" tabindex="15" border="0"> 
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="TabProjCPMainDataCPDocs" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Documents") ; ?>">
				<div id="TabProjCPMainDataCPDocsTCDocs" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="TabProjCPMainDataCPDocsCPOvvw" data-dojo-type="dijit/layout/ContentPane" title="&Uuml;bersicht" onShow="requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'getDocListAsXML', document.forms['ProjKeyData']._IProjNr.value, '', '', null, showProjDocList) ; return false ; ">
						<div id="TableProjDocs">
						</div>
					</div>
					<div id="TabProjCPMainDataCPDocsCPDtls" data-dojo-type="dijit/layout/ContentPane" title="Upload">
						<div id="content">
							<div id="maindata">
								<form action="/Base/Proj/upProjDoc.php" method="post" name="formProjDocUpload" id="formProjDocUpload" target="_result" enctype="multipart/form-data">
									<input type="text" name="_DProjNr" value="" />
									<table><?php
										rowOption( FTr::tr( "Document type"), "_IDocTypeProj", Proj::getRDocType(), "AA", "") ;
										rowFile( FTr::tr( "Filename"), "_IFilename", 24,32, "", "") ;
										?></table> 
									<input type="submit" value="Upload" tabindex="14" border="0" >
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="TabProjMainDataCPEval" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Evaluation") ; ?>"
					onShow="requestDataMiner( 'Base', 'DataMinerProj', '/Common/hdlObject.php', 'getTableProjPos', document.forms['ProjKeyData']._IProjNr.value, 'ProjPosEval', 'ProjNr', '', '', '') ; return false ; ">
				<input type="button" value="<?php echo FTr::tr( "Refresh ...") ; ?>" onClick="requestDataMiner( 'Base', 'DataMinerProj', '/Common/hdlObject.php', 'getTableProjPos', document.forms['ProjKeyData']._IProjNr.value, 'ProjPosEval', 'ProjNr', '', '', '') ; return false ; " />
				<div id="ProjPosEval"></div>
			</div>
			<div id="TabProjCPMainDataCPFuncs" data-dojo-type="dijit/layout/ContentPane" title="Funktionen">
			</div>
		</div>
	</div>
</div>
