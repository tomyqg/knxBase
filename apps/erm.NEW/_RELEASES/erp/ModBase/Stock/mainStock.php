<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="StockC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="StockC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="StockKeyData" id="StockKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Stock no.") ; ?>:&nbsp;</th>
							<td class="space"><input type="image" src="/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IStockId" id="_IStockId" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'Stock', '/Common/hdlObject.php', 'getXMLComplete', document.forms['StockKeyData']._IStockId.value, '', '', null, showStockAll) ;}else{return true ;} return false ; "/>
							</td>
							<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button"
									onclick="selStock( 'Base', 'Stock', 'getXMLComplete', document.forms['StockKeyData']._IStockId, showStockAll) ; return false ; ">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="StockC3s1c2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="StockC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<!-- 									 						 -->
			<!-- Overview over all existing Artikel Stock Corrections		 -->
			<!-- 															 -->
			<div id="StockOverview" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "List") ; ?>">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button"
							onClick="requestUni( 'Base', 'DataMinerStock', '/Common/hdlObject.php', 'getTableAll', '', '', '', null, showTableStockOV) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="TableStockOV">
						</div>
					</div>
				</div>
			</div>
			<!-- 															 -->
			<!-- Individual Article Stock Correction, Main Data				 -->
			<!-- 															 -->
			<div id="StockMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formStockMain" id="formStockMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Warehouse id."), "_IWarehouseId", 16, 16, "", "") ;
								rowEdit( FTr::tr( "Stock id."), "_IStockId", 20, 20, "", "") ;
								rowTextEdit( FTr::tr( "Description"), "_IDescription", 64, 4, "",
											"HELP_STOCK_DESCRIPTION") ;
								rowTextEdit( FTr::tr( "Location"), "_ILocation", 64, 4, "",
											"HELP_STOCK_LOCATION") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button"
								onclick="requestUni( 'Base', 'Stock', '/Common/hdlObject.php', 'add', _IStockId.value, '', '', 'formStockMain', showStock) ; return false ; ">
								<?php echo FTr::tr( "Create") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button"
								onclick="requestUni( 'Base', 'Stock', '/Common/hdlObject.php', 'upd', document.forms['StockKeyData']._IStockId.value, '', '', 'formStockMain', showStock) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<!-- 															 -->
			<!-- Individual Article Stock Correction, Main Data				 -->
			<!-- 															 -->
			<div id="StockLocation" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Locations") ; ?>">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button"
							onclick="editorAdd( 'Base', 'Stock', '/Base/Stock/editorStockLocation.php', 'getStockLocation/AsXML', document.forms['StockKeyData']._IStockId.value, '', '', null, showTableStockLocation, 'StockLocation') ; ">
							<?php echo FTr::tr( "New") ; ?>
						</button>
						<?php tableBlock( "refStockLocation", "formStockLocationTop") ; ?>
					</div>
					<div id="depdata">
						<div id="StockLocationRoot">
							<table id="TableStockLocation" eissClass="StockLocation">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="WarehouseId">Warehouse id.</th>
										<th eissAttribute="StockId">Stock id.</th>
										<th eissAttribute="ShelfId">Shelf id.</th>
										<th eissAttribute="Locatiopn">Location</th>
										<th eissAttribute="Description">Description</th>
										<th colspan="5" eissFunctions="edit,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
					<div id="maindata">
						<?php tableBlock( "refStockLocation", "formStockLocationBot") ; ?>
					</div>
				</div>
			</div>
			<div id="StockFunctionsCP" data-dojo-type="dijit/layout/ContentPane" title="Funktionen">
				<div id="StockFunctionsTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
<!-- 
 					<div id="StockFunctionPrintLabels" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Print Labels") ; ?>">
						<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'ArticleLblDoc', '/Common/hdlObject.php', 'createPDF55x25', document.forms['ArtikelKeyData']._IArtikelNr.value, '', '', null, showArtikelAll) ; return false ; ">
							<?php echo FTr::tr( "Create Label (55x25mm; 2(1/4)x1in)") ; ?>
						</button><br />
						<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'ArticleLblDoc', '/Common/hdlObject.php', 'createPDF75x25', document.forms['ArtikelKeyData']._IArtikelNr.value, '', '', null, showArtikelAll) ; return false ; ">
							<?php echo FTr::tr( "Create Label (75x25mm; 3x1in)") ; ?>
						</button><br />
						<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'ArticleLblDoc', '/Common/hdlObject.php', 'createPDF100x50', document.forms['ArtikelKeyData']._IArtikelNr.value, '', '', null, showArtikelAll) ; return false ; ">
							<?php echo FTr::tr( "Create Label (100x50mm; 3x2in)") ; ?>
						</button><br />
						<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'ArticleLblDoc', '/Common/hdlObject.php', 'createPDF100x55', document.forms['ArtikelKeyData']._IArtikelNr.value, '', '', null, showArtikelAll) ; return false ; ">
							<?php echo FTr::tr( "Create Label (100x55mm; 4x2(1/4)in)") ; ?>
						</button><br />
					</div>
 -->
					<div id="StockFunctionsReport" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Reports") ; ?>">
						<div id="content">
							<div id="maindata">
								All filter criteria are AND concatenated!<br/>
								<form name="formStockReport" onsubmit="return false ;">
									<table>
									<?php
										rowEdit( FTr::tr( "Warehouse id."), "_IFiltWarehouseId", 32, 32, "", FTr::tr( "ERP-HELP-FIELD-FiltArticleNo")) ;
										rowEdit( FTr::tr( "Stock id."), "_IFiltStockId", 32, 32, "", FTr::tr( "ERP-HELP-FIELD-FiltArticleNo")) ;
										rowEdit( FTr::tr( "Shelf id."), "_IFiltShelfId", 32, 32, "", FTr::tr( "ERP-HELP-FIELD-FiltArticleNo")) ;
										rowEdit( FTr::tr( "Article no."), "_IFiltArticleNo", 32, 32, "", FTr::tr( "ERP-HELP-FIELD-FiltArticleNo")) ;
										rowEdit( FTr::tr( "Description"), "_IFiltDescription", 32, 32, "", FTr::tr( "ERP-HELP-FIELD-FiltDescription")) ;
										rowEdit( FTr::tr( "Description 1"), "_IFiltDesc1", 32, 32, "", FTr::tr( "ERP-HELP-FIELD-FiltDesc1")) ;
										rowEdit( FTr::tr( "Description 2"), "_IFiltDesc2", 32, 32, "", FTr::tr( "ERP-HELP-FIELD-FiltDesc2")) ;
										?>
									</table>
								</form>
							</div>
						</div>
						<button data-dojo-type="dijit/form/Button" onclick="requestUniXML( 'Base', 'Stock', '/Common/hdlObjectXML.php', 'report', 'halfstatic', -1, '', 'formStockReport', null) ; return false ; ">
							<?php echo FTr::tr( "Create Report for Warehouse") ; ?>
						</button><br />
						<button data-dojo-type="dijit/form/Button" onclick="requestUniXML( 'Base', 'Stock', '/Common/hdlObjectXML.php', 'report', 'halfstatic', -1, document.forms['StockKeyData']._IStockId.value, 'formStockReport', null) ; return false ; ">
							<?php echo FTr::tr( "Create Report for Stock") ; ?>
						</button><br />
						<button data-dojo-type="dijit/form/Button" onclick="requestUniXML( 'Base', 'Stock', '/Common/hdlObjectXML.php', 'labels', 'halfstatic', -1, '', 'formStockReport', null) ; return false ; ">
							<?php echo FTr::tr( "Print all stocklabels") ; ?>
						</button><br />
 					</div>
					<div id="StockDocuments" data-dojo-type="dijit/layout/ContentPane" title="Documents" onShow="requestUni( 'Base', 'Stock', '/Common/hdlObject.php', 'getTableDocumentsAsXML', 'halfstatic', '', '', null, showStockDocList) ; return false ; ">
						<div id="tableStockDocs">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
