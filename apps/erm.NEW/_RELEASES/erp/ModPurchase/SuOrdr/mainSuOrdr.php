<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="SuOrdrC3s1" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="SuOrdrC3s1c1" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="SuOrdrKeyData" id="SuOrdrKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Order no.") ; ?>:&nbsp;</th>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_ISuOrdrNo" id="_ISuOrdrNo" value=""
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" border="0"
									onclick="screenCurrent.select.show( '', -1, '') ;">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
							<td>
								<div id="lockStateSuOrdr"></div>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/Blue/32/object_04.png"
									onclick="screenCurrent.newSuDlvr() ;"
									title="Wareneingang anlegen" />
							</td>
 							<td class="image">
								<div id="pdfSuOrdr">
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="SuOrdrC3s1c2" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="SuOrdrC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer">
			<div id="SuOrdrC3s1c2tc1cp1" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formSuOrdrMain" id="formSuOrdrMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEditDbl( FTr::tr( "Supplier no."), "_ILiefNr", 11, 11, "", "_ILiefKontaktNr", 3, 3, "", "", "",
											"screenCurrent.showSelSupplier() ;") ;
								rowDate( FTr::tr( "Date"), "_IDatumSuOrdr", 11, 11, "", "HELP-DateSuOrdr") ;
								rowEdit( FTr::tr( "Ref. to cust.order"), "_ICuOrdrNo", 24, 32, "", "HELP-CuOrdrNop_as_ref") ;
								rowEdit( FTr::tr( "Ref. to cust.delivery"), "_ICuDlvrNo", 24, 32, "", "HELP-CuDlvrNo_as_ref") ;
								rowEdit( FTr::tr( "Ref. no."), "_IRefNr", 24, 32, "", "HELP-RefNo") ;
								rowDate( FTr::tr( "Ref. date"), "_IRefDatumSuOrdr", 11, 11, "", "HELP-RefDate") ;
								rowEdit( FTr::tr( "Itemcount"), "_IPositionen", 4, 4, "", "HELP-Itemcount") ;
								rowEdit( FTr::tr( "Total net"), "_FGesamtPreisSuOrdr", 10, 10, "", "HELP-TotalNet") ;
								rowOption( FTr::tr( "Status"), "_IStatus", SuOrdr::getRStatus(), "0", "", "HELP-Status") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'upd', 'formSuOrdrMain') ;">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<?php
				compSupp( "SuOrdrLief", FTr::tr( "Supplier"), "SuOrdr", "", "", "") ;
			?>
			<div id="SuOrdrC3s1c2tc1cp3" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "Texts") ; ?>">
				<div id="content">
					<div id="maindata">
						<form name="formSuOrdrModi" id="formSuOrdrModi" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowHTMLEdit2( FTr::tr( "Prefix"), "_RPrefixSuOrdr", 64, 5, "", "", "onkeypress='mark( _IPrefix) ;'") ;
								rowHTMLEdit2( FTr::tr( "Postfix"), "_RPostfixSuOrdr", 64, 5, "", "", "onkeypress='mark( _IPostfix) ;'") ;
								rowTextEdit( FTr::tr( "Remarks"), "_IRem1SuOrdr", 64, 3, "", "", "onkeypress='mark( _IRem1) ;'") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'upd', 'formSuOrdrModi') ;">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.dDispatch( true, 'setTexte') ;">
								<?php echo FTr::tr( "Set Default Text") ; ?>
							</button>
						</form> 
						<form name="formSuOrdrAddRem" id="formSuOrdrAddRem" onsubmit="return false ;" >
							<table><?php
								rowTextEdit( FTr::tr( "Remark to add"), "_IRem", 64, 2, "", "") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'addRem', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', document.forms['formSuOrdrAddRem']._IRem.value, null, showSuOrdr) ; return false ; ">
								<?php echo FTr::tr( "Add remark") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="SuOrdrC3s1c2tc1cp4" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "Items") ; ?>">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.showSelArticlePP() ;">
							<?php echo FTr::tr( "New item") ; ?>
						</button>
 						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.qDispatch( true, 'updPrices') ; ">
							<?php echo FTr::tr( "Update all prices") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.qDispatch( true, 'buche') ; ">
							<?php echo FTr::tr( "Book") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.qDispatch( true, 'unbuche') ; ">
							<?php echo FTr::tr( "Un-book") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.qDispatch( true, 'cons') ; ">
							<?php echo FTr::tr( "Consolidate") ; ?>
						</button>
					</div>
					<div id="depdata">
						<div id="SuOrdrItemsRoot">
							<?php tableBlock( "itemViews['dtvSuOrdrItems']", "formSuOrdrItemsTop") ;		?>
							<table id="TableSuOrdrItems" eissClass="SuOrdrItem" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="ItemNo">Item</th>
										<th eissAttribute="ArtikelNr" eissLinkTo="Artikel" colspan="2"><?php echo FTr::tr( "Article no.") ; ?></th>
										<th eissAttribute="ArtikelBez1"><?php echo FTr::tr( "Description") ; ?></th>
										<th eissAttribute="Menge" eissAlign="right" eissFunctions="step,input" colspan="3"><?php echo FTr::tr( "Qty. ordered") ; ?></th>
										<th eissAttribute="MengeGebucht" ><?php echo FTr::tr( "Qty. booked") ; ?></th>
										<th eissAttribute="MengeBereitsEmpfangen"><?php echo FTr::tr( "Qty. received already") ; ?></th>
										<th eissAttribute="Preis" eissFunctions="input" eissAlign="right"><?php echo FTr::tr( "Price") ; ?></th>
										<th eissAttribute="GesamtPreis"><?php echo FTr::tr( "Total") ; ?></th>
										<th colspan="5" eissFunctions="edit,move,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="SuOrdrC3s1c2tc1cp6" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "Transmission") ; ?>">
				<div id="SuOrdrC3s1c2tc1cp6SuOrdrC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="SuOrdrC3s1c2tc1cp6SuOrdrC3s1c2tc1cp1" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "by E-Mail") ; ?>">
						<div id="content">
							<div id="maindata">
								<form action="VOID" method="post" id="formSuOrdrDocEMail" name="formSuOrdrDocEMail" enctype="multipart/form-data" onsubmit="return false ;" >
									<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
									<table><?php
										rowEdit( "E-Mail:", "_IBestellEMail", 32, 64, "", "") ;
										rowOption( "Status:", "_IStatus", SuOrdr::getRStatus(), "") ;
										rowOption( "Versand per:", "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
//										rowHTMLEdit( "Anschreiben:", "_RAnschreibenSuOrdr", 64, 5, "",
//														FTr::tr( "HELP-LFBEST-EMAIL"),
//														"showEMailSuOrdr()", "hideEMailSuOrdr()") ;
										?></table> 
									<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'updAnschreiben', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', '', 'formSuOrdrDocEMail', showSuOrdrAll) ; return false ; ">
										<?php echo FTr::tr( "Update") ; ?>
									</button>
									<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'sendByEMail', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', '', 'formSuOrdrDocEMail', showSuOrdrAll) ; return false ; ">
										<?php echo FTr::tr( "Send E-Mail") ; ?>
									</button>
									<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'setAnschreiben', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', '', 'formSuOrdrDocEMail', showSuOrdrAll) ; return false ; ">
										<?php echo FTr::tr( "Default text") ; ?>
									</button>
								</form> 
							</div>
						</div>
					</div>
					<div id="SuOrdrC3s1c2tc1cp6SuOrdrC3s1c2tc1cp2" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "by Fax") ; ?>">
						<div id="content">
							<div id="maindata">
								<form action="LiefBearb.php" method="post" name="formSuOrdrDocFAX" id="formSuOrdrDocFAX" enctype="multipart/form-data">
									<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
									<table><?php
										rowEdit( "Fax Nr:", "_IBestellFAX", 24, 32, "", "") ;
										rowOption( "Status:", "_IStatus", SuOrdr::getRStatus(), "") ;
										rowOption( "Versand per:", "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										rowTextEdit( "FAX-Server Befehl:", "_IPlainText", 64, 5,
														"<request>\n".
														"<auth>\n".
														"<account>10663</account>\n".
														"<password>s4dwe84e</password>\n".
														"</auth>\n".
														"<fax>\n".
														"<fax-id>SuOrdr </fax-id>\n".
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
									<button data-dojo-type="dijit/form/Button" tabindex="14" border="0"
										onclick="requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'sendByFax', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', '', 'formSuOrdrDocFAX', showSuOrdr) ; return false ; ">
										<?php echo FTr::tr( "Send ...") ; ?>
									</button>
								</form> 
							</div>
						</div>
					</div>
					<div id="SuOrdrC3s1c2tc1cp6SuOrdrC3s1c2tc1cp3" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "as PDF") ; ?>">
						<div id="content">
							<div id="maindata">
								<form action="LiefBearb.php" method="post" name="formSuOrdrDocPDF" id="formSuOrdrDocPDF" enctype="multipart/form-data">
									<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
									<table><?php
										rowOption( "Status:", "_IStatus", SuOrdr::getRStatus(), "") ;
										rowOption( "Versand per:", "_IDocVerschVia", Opt::getRVersandUeber(), "") ;
										?></table> 
									<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'upd', _ISuOrdrNo.value, '', '', 'formSuOrdrDocPDF', showSuOrdr) ; return false ; ">
										<?php echo FTr::tr( "Update") ; ?>
									</button>
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="TabSuOrdrDoc" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "Documents") ; ?>">
				<div id="TabSuOrdrDocCont" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="TabSuOrdrDocTable" data-dojo-type="dijit/layout/ContentPane"" title="&Uuml;bersicht" onShow="requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'getDocListAsXML', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', '', null, showSuOrdrDocList) ; return false ; ">
						<div id="TableSuOrdrDocs">
						</div>
					</div>
					<div id="TabSuOrdrDocUpload" data-dojo-type="dijit/layout/ContentPane"" title="Upload">
						<div id="content">
							<div id="maindata">
								<form action="/Common/uploadSupportDoc.php" method="post" name="formSuOrdrDocUpload" id="formSuOrdrDocUpload" target="_result" enctype="multipart/form-data">
									<table><?php
										rowOption( "Dokument Typ:", "_IDocType", SuOrdr::getRDocType(), "AB", "") ;
										rowDisplay( FTr::tr( "Path"), "_DSubPath", 32, 64, "SuOrdr/", "") ;
										rowEdit( FTr::tr( "Ref. no"), "_DRefNr", 32, 64, "", "") ;
										rowFile( FTr::tr( "Filename"), "_IFilename", 24, 32, "", "") ;										
										?></table> 
									<input type="submit" value="Upload" tabindex="14" border="0" >
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="SuOrdrDataMiner" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "Datamining") ; ?>">
				<div id="SuOrdrDataMinerTC" data-dojo-type="dijit/layout/TabContainer">
					<div id="SuOrdrDataMinerSuDlvr" data-dojo-type="dijit/layout/ContentPane"" title="Lieferungen"
							onShow="requestDataMiner( 'Base', 'DataMinerSuOrdr', '/Common/hdlObject.php', 'getTableSuDlvrForSuOrdr', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, 'SuDlvrForSuOrdr', 'SuDlvrNo', 'screenSuDlvr', 'retToSuOrdr', document.forms['SuOrdrKeyData']._ISuOrdrNo.value) ; return false ; ">
						<button data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerSuOrdr', '/Common/hdlObject.php', 'getTableSuDlvrForSuOrdr', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, 'SuDlvrForSuOrdr', 'SuDlvrNo', 'screenSuDlvr', 'retToSuOrdr', document.forms['SuOrdrKeyData']._ISuOrdrNo.value) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="SuDlvrForSuOrdr"></div>
					</div>
				</div>
			</div>
			<div id="SuOrdrC3s1c2tc1cp7" data-dojo-type="dijit/layout/ContentPane"" title="<?php echo FTr::tr( "Functions") ; ?>">
				<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'bucheAll', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', '0', null, showTableSuOrdrItem) ; return false ; ">
					<?php echo FTr::tr( "Book all") ; ?>
				</button>
				<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'unbucheAll', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', '0', null, showTableSuOrdrItem) ; return false ; ">
					<?php echo FTr::tr( "Un-book all") ; ?>
				</button>
			</div>
		</div>
	</div>
</div>
