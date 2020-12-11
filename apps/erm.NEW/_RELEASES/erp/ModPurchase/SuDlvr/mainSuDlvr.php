<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="SuDlvrC3s1" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="SuDlvrC3s1c1" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="SuDlvrKeyData" id="SuDlvrKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Goods receivable no.") ; ?>:&nbsp;</th>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_ISuDlvrNo" id="_ISuDlvrNo"
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button" data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.selSuDlvr.show( '', -1, '') ;"/>
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
							<td>
								<div id="lockStateSuDlvr"></div>
							</td>
							<td class="image">
								<input type="image" src="/Rsrc/licon/Blue/32/object_04.png"
									onclick="screenCurrent.newSuOrdr() ;"
									title="<?php echo FTr::tr( "Create new Supplier order from Goods Receivable") ; ?>" />
							</td>
							<td class="image">
								<div id="pdfDocument">
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="SuDlvrC3s1c2" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="SuDlvrC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="SuDlvrC3s1c2tc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formSuDlvrMain" id="formSuDlvrMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEditDbl( FTr::tr( "Supplier no."), "_ILiefNr", 11, 11, "", "_ILiefKontaktNr", 3, 3, "", "", "",
										"screenCurrent.showSelSupplier() ;") ;
								rowDate( FTr::tr( "Date"), "_IDatumSuDlvr", 11, 11, "", "") ;
								rowEdit( FTr::tr( "Ref. no."), "_IRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Ref. date"), "_IRefDatumSuDlvr", 11, 11, "", "") ;
								rowDisplay( FTr::tr( "SuOrdr. no."), "_DSuOrdrNo", 10, 10, "", "",
										"screenLinkTo( 'screenSuOrdr', document.forms['formSuDlvrMain']._DSuOrdrNo.value)") ;
								rowEdit( FTr::tr( "Items"), "_IPositionen", 4, 4, "", "") ;
								rowEdit( FTr::tr( "Total price"), "_FGesamtPreisSuDlvr", 10, 10, "", "") ;
								rowOption( FTr::tr( "Status"), "_IStatus", SuDlvr::getRStatus(), "0", "") ;
								?></table> 
						</form> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuDlvr', '/Common/hdlObject.php', 'upd', document.forms['SuDlvrKeyData']._ISuDlvrNo.value, '', '', 'formSuDlvrMain', showSuDlvr) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
					</div>
				</div>
			</div>
			<div id="SuDlvrC3s1c2tc1cp2SuDlvrC3s1c2tc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Supplier") ; ?>">
				<div id="content">
					<div id="maindata">
						<table><tr><td>
							<form action="LiefBearb.php" method="post" id="formSuDlvrLief" name="formSuDlvrLief" enctype="multipart/form-data" onsubmit="return false ;" >
								<input type="hidden" name="_HId" readonly maxlength="8" value=""></input>
								<table><?php
									liefBlock( "selLief( 'Base', 'SuDlvr', document.forms['SuDlvrKeyData']._ISuDlvrNo.value, 'setLiefFromLKId', showSuDlvrAll)") ;
									?></table>
								<input type="submit" name="actionUpdateLief" value="eradresse anlegen" tabindex="14" border="0">
								<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
							</form></td><td>
							<form action="LiefBearb.php" method="post" id="formSuDlvrLiefKontakt" name="formSuDlvrLiefKontakt" enctype="multipart/form-data" onsubmit="return false ;" >
								<table><?php
									liefKontaktBlock() ;
									?></table>
								<input type="submit" name="actionUpdateLief" value="<?php echo FTr::tr( "Create supplier contact") ; ?> tabindex="14" border="0">
								<input type="reset" value="<?php echo FTr::tr( "Reset input fields") ; ?> tabindex="15" border="0"> 
							</form></td></tr>
						</table>
					</div>
				</div>
			</div>
			<div id="SuDlvrC3s1c2tc1cp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Terms") ; ?>">
				<div id="content">
					<div id="maindata">
						<form action="LiefBearb.php" method="post" name="formSuDlvrModi" id="formSuDlvrModi" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
//								rowHTMLEdit( FTr::tr( "Prefix"), "_RPrefixSuDlvr", 64, 5, "", "", "onkeypress='mark( _IPrefix) ;'") ;
//								rowHTMLEdit( FTr::tr( "Postfix"), "_RPostfixSuDlvr", 64, 5, "", "", "onkeypress='mark( _IPostfix) ;'") ;
								rowTextEdit( FTr::tr( "Remarks"), "_IRem1SuDlvr", 64, 3, "", "", "onkeypress='mark( _IRem1) ;'") ;
								rowTextEdit( FTr::tr( "Remark"), "_IAddRemSuDlvr", 64, 2, "", "", "onkeypress='mark( _IAddRem) ;'") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button"  onclick="requestUni( 'Base', 'SuDlvr', '/Common/hdlObject.php', 'upd', document.forms['SuDlvrKeyData']._ISuDlvrNo.value, '', '', 'formSuDlvrModi', showSuDlvr) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
							<button type="reset" data-dojo-type="dijit/form/Button" >
								<?php echo FTr::tr( "Clear form") ; ?>
							</button> 
						</form> 
					</div>
				</div>
			</div>
			<div id="SuDlvrC3s1c2tc1cp41" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Items") ; ?>">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.showSelArticlePP() ;" >
							<?php echo FTr::tr( "New item") ; ?>
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
							onclick="screenCurrent.qDispatch( true, 'setAllRcvd') ; ">
							<?php echo FTr::tr( "Received everything") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.qDispatch( true, 'setNoneRcvd') ; ">
							<?php echo FTr::tr( "Received nothing") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.qDispatch( true, 'restate') ; ">
							<?php echo FTr::tr( "Consolidate") ; ?>
						</button>
					</div>
					<div id="depdata">
						<div id="SuDlvrItemsRoot">
							<?php tableBlock( "itemViews['dtvSuDlvrItems']", "formSuDlvrItemsTop") ;		?>
							<table id="TableSuDlvrItems" eissClass="SuDlvrItem" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="ItemNo">Item no.</th>
										<th eissAttribute="ArtikelNr" eissLinkTo="Artikel" colspan="2">Article no.</th>
										<th eissAttribute="ArtikelBez1">Description</th>
										<th eissAttribute="Menge" eissAlign="right" eissFunctions="step,input" colspan="3">Qty. ordered</th>
										<th eissAttribute="MengeEmpfangen" eissAlign="right" eissFunctions="step,input" colspan="3">Menge Empfangen</th>
										<th eissAttribute="MengeGebucht">Qty. booked</th>
										<th colspan="5" eissFunctions="edit,shift,expand,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="TabSuDlvrDoc" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Documents") ; ?>">
				<div id="TabSuDlvrDocCont" data-dojo-type="dijit/layout/TabContainer" tabposition="left">
					<div id="TabSuDlvrDocTable" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Overview") ; ?>" onShow="requestUni( 'Base', 'SuDlvr', '/Common/hdlObject.php', 'getDocListAsXML', document.forms['SuDlvrKeyData']._ISuDlvrNo.value, '', '', null, showSuDlvrDocList) ; return false ; ">
						<div id="TableSuDlvrDocs">
						</div>
					</div>
					<div id="TabSuDlvrDocUpload" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Upload") ; ?>">
						<div id="content">
							<div id="maindata">
								<form action="/Common/uploadSupportDoc.php" method="post" name="formSuDlvrDocUpload" id="formSuDlvrDocUpload" target="_result" enctype="multipart/form-data">
 									<table><?php
										rowOption( FTr::tr( "Document type"), "_IDocType", SuDlvr::getRDocType(), "LS", "") ;
										rowDisplay( FTr::tr( "Path"), "_DSubPath", 32, 64, "SuDlvr/", "") ;
										rowEdit( FTr::tr( "Ref. no."), "_DRefNr", 32, 64, "", "") ;
										rowFile( FTr::tr( "Filename"), "_IFilename", 24, 32, "", "") ;										
										?></table> 
									<button type="submit" data-dojo-type="dijit/form/Button">
										<?php echo FTr::tr( "Upload") ; ?>
									</button>
								</form> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="SuDlvrC3s1c2tc1cp7" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Functions") ; ?>">
				<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuDlvr', '/Common/hdlObject.php', 'bucheAll', document.forms['SuDlvrKeyData']._ISuDlvrNo.value, '', '0', null, showTableSuDlvrItem) ; return false ; ">
					<?php echo FTr::tr( "Book all") ; ?>
				</button>
				<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'SuDlvr', '/Common/hdlObject.php', 'unbucheAll', document.forms['SuDlvrKeyData']._ISuDlvrNo.value, '', '0', null, showTableSuDlvrItem) ; return false ; ">
					<?php echo FTr::tr( "Un-book all") ; ?>
				</button>
			</div>
		</div>
	</div>
</div>
