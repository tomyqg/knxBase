<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="PSuOrdrBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="PSuOrdrC3s1c1" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="PSuOrdrKeyData" id="PSuOrdrKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Order proposal no.") ; ?>:&nbsp;</th>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IPSuOrdrNo" id="_IPSuOrdrNo"
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.selPSuOrdr.show( '', -1, '') ;">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
							<td>
								<div id="lockStatePSuOrdr"></div>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/Blue/32/object_04.png"
									onclick="screenCurrent.newSuOrdr() ;" />
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
	<div id="PSuOrdrC3s1c2" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="PSuOrdrC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer">
			<div id="PSuOrdrOverview" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Articles to be ordered") ; ?>">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onClick="requestUni( 'Base', 'ArtikelBestand', '/Common/hdlObject.php', 'getTableItemsToOrderAsXML', '', '', '', null, showTablePSuOrdrItemOV) ; return false ; " />
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onClick="requestUni( 'Base', 'PSuOrdr', '/Common/hdlObject.php', 'create', '', '', '', null, showTablePSuOrdrItemOV) ; return false ; " />
							<?php echo FTr::tr( "Create proposals") ; ?>
						</button>
						<div id="TablePSuOrdrItemOV">
						</div>
					</div>
				</div>
			</div>
 			<div id="PSuOrdrC3s1c2tc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formPSuOrdrMain" id="formPSuOrdrMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEditDbl( FTr::tr( "Lieferant Nr."), "_ILiefNr", 11, 11, "", "_ILiefKontaktNr", 3, 3, "", "", "",
											"selLief( 'Base', 'PSuOrdr', document.forms['PSuOrdrKeyData']._IPSuOrdrNo.value, 'setLiefFromLKId', showPSuOrdrAll)") ;
								rowDate( FTr::tr( "Datum"), "_IDatumPSuOrdr", 11, 11, "", "") ;
//								rowEdit( FTr::tr( "Ref. Nr."), "_IRefNr", 24, 32, "", "") ;
//								rowDate( FTr::tr( "Ref. Datum"), "_IRefDatumPSuOrdr", 11, 11, "", "") ;
								rowEdit( FTr::tr( "Positionen"), "_IPositionen", 4, 4, "", "") ;
								rowEdit( FTr::tr( "Gesamtpreis"), "_FGesamtPreisPSuOrdr", 10, 10, "", "") ;
								rowOption( FTr::tr( "Status"), "_IStatus", PSuOrdr::getRStatus(), "0", "") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'upd', 'formPSuOrdrMain') ;">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<?php
				compSupp( "PSuOrdrLief", FTr::tr( "Supplier"), "PSuOrdr", "", "", "") ;
			?>
			<div id="PSuOrdrC3s1c2tc1cp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Text") ; ?>">
				<div id="content">
					<div id="maindata">
						<form name="formPSuOrdrModi" id="formPSuOrdrModi" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowHTMLEdit2( FTr::tr( "Prefix"), "_RPrefixPSuOrdr", 64, 5, "", "", "") ;
								rowHTMLEdit2( FTr::tr( "Postfix"), "_RPostfixPSuOrdr", 64, 5, "", "", "onkeypress='mark( _IPostfix) ;'") ;
								rowTextEdit( FTr::tr( "Bemerkungen"), "_IRem1PSuOrdr", 64, 3, "", "", "onkeypress='mark( _IRem1) ;'") ;
								rowTextEdit( FTr::tr( "Bemerkung"), "_IAddRem", 64, 2, "", "", "onkeypress='mark( _IAddRem) ;'") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'upd', 'formPSuOrdrModi') ;">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="PSuOrdrC3s1c2tc1cp4" data-dojo-type="dijit/layout/ContentPane" title="Posten">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.showSelArticlePP() ;">
							<?php echo FTr::tr( "New item") ; ?>
						</button>
					</div>
					<div id="depdata">
						<div id="PSuOrdrItemsRoot">
							<?php tableBlock( "itemViews['dtvPSuOrdrItems']", "formPSuOrdrItemsTop") ;		?>
							<table id="TablePSuOrdrItems" eissClass="PSuOrdrItem" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="ItemNo">Item</th>
										<th eissAttribute="ArtikelNr" eissLinkTo="Artikel" colspan="2">Article no.</th>
										<th eissAttribute="ArtikelBez1">Description</th>
										<th eissAttribute="Menge" eissAlign="right" eissFunctions="step,input" colspan="3">Qty. ordered</th>
										<th eissAttribute="MengeProVPE"><?php echo FTr::tr( "Qty. / pack") ; ?></th>
										<th eissAttribute="Preis">Price</th>
										<th eissAttribute="GesamtPreis">Price</th>
										<th colspan="5" eissFunctions="edit,move,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<?php tableBlockBot( "itemViews['dtvPSuOrdrItems']", "formPSuOrdrItemsBot") ;		?>
						</div>
					</div>
				</div>
			</div>
			<div id="PSuOrdrC3s1c2tc1cp7" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Functions") ; ?>">
			</div>
		</div>
	</div>
</div>