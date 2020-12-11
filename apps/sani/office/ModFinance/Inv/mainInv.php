<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="InvBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="InvCPKey" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="InvKeyData" id="InvKeyData" onsubmit="return false ;">  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Inv no.") ; ?>:&nbsp;</th>
							<td><input type="image" src="/Rsrc/licon/yellow/18/left.png"
								onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IInvNo" id="_IInvNo"
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" name="selInvNo"
									onclick="screenCurrent.select.show( '', -1, '') ;">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button>
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Description") ;?>:</th>
							<td colspan="4">
								<input type="text" name="_DDescription" id="VOID" size="64" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="InvCPMain" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="InvC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<!-- 									 						 -->
			<!-- Overview over all existing Artikel Stock Corrections		 -->
			<!-- 															 -->
			<div id="InvOverview" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "List") ; ?>">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button"
							onClick="requestUni( 'Base', 'DataMinerInv', '/Common/hdlObject.php', 'getTableAll', '', '', '', null, showTableInvOV) ;">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="TableInvOV">
						</div>
					</div>
				</div>
			</div>
			<!-- 															 -->
			<!-- INdividual Article Stock Correction, Main Data				 -->
			<!-- 															 -->
			<div id="InvMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formInvMain" id="formInvMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowDate( FTr::tr( "Date"), "_IDate", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Description"), "_IDescription", 48, 128, "", "") ;
								rowDate( FTr::tr( "Based on date"), "_IBaseDate", 10, 10, "", "") ;
								rowDate( FTr::tr( "Key date"), "_IKeyDate", 10, 10, "", "") ;
								rowOption( FTr::tr( "Type"), "_IType", Inv::getRType(), "0") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModFinance', 'Inv', '/Common/hdlObject.php', 'upd', document.forms['InvKeyData']._IInvNo.value, '', '', 'formInvMain', showInv) ;">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<!-- 															 -->
			<!-- Individual Article Stock Correction, Main Data				 -->
			<!-- 															 -->
			<div id="InvItem" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Items") ; ?>">
				<div id="content">
					<div id="maindata">
						<form name="formInvItemsTop" id="formInvItemsTop">
							<button data-dojo-type="dijit/form/Button" name="addPos"
								onclick="selArtikelById( 'ModFinance', 'Inv', document.forms['InvKeyData']._IInvNo.value, 'addPos', showTableInvItem) ;" />
								<?php echo FTr::tr( "New item ...") ; ?>
							</button>
						</form>
					</div>
					<div id="depdata">
						<div id="TableInvItemRoot">
							<?php tableBlock( "itemViews['dtvInvItems']", "formInvItemsTop") ; ?>
							<table id="TableInvItems" eissClass="InvItem" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="ItemNo">Item</th>
										<th eissAttribute="ArticleNo" eissLinkTo="Artikel" colspan="2">Article no.</th>
										<th eissAttribute="ERPNo" eissLinkTo="Artikel" colspan="2">ERP no.</th>
										<th eissAttribute="ArtikelBez1">Description</th>
										<th eissAttribute="QtyOut" eissMod="ModFinance" eissFunctions="step" colspan="3">Qty. old</th>
										<th eissAttribute="QtyIn" eissMod="ModFinance" eissFunctions="step,input" colspan="3">Qty. new</th>
										<th eissAttribute="QtyPerPack" >Qty. / pack</th>
										<th eissAttribute="QtyBooked">Qty. booked</th>
										<th colspan="4" eissMod="ModFinance" eissFunctions="edit,move,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="InvFunctions" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Functions") ; ?>">
				<div id="InvFunctionsTC00" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="InvFunctionsTC00CP00" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Specific") ; ?>">
						<div id="content">
							<div id="maindata">
								<button type="button" data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.qDispatch( false, 'reCreate') ;">
									<?php echo FTr::tr( "Re-Create Inv") ; ?>
								</button><br />
								<button type="button" data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.qDispatch( false, 'apply') ;">
									<?php echo FTr::tr( "Apply Inv") ; ?>
								</button><br />
								<button data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.qDispatch( false, 'book') ;">
									<?php echo FTr::tr( "Book all SuDlvr, CuDlvr after this keydate") ; ?>
								</button><br />
								<button data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.qDispatch( false, 'mark') ;">
									<?php echo FTr::tr( "Mark all SuOrdr, CuOrdr, CuComm") ; ?>
								</button><br />
								<button data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.qDispatch( false, 'applyBookMark') ;">
									<?php echo FTr::tr( "Apply Inv and book all SuDlvr, CuDlvr, SuOrdr, CuOrdr, CuComm") ; ?>
								</button><br />
							</div>
						</div>
					</div>
					<div id="InvFunctionsTC00CP01" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?>">
						<div id="content">
							<div id="maindata">
								All filter criteria are AND concatenated!<br/>
								No wildcards ware added by the system, so provide as you need!<br/>
								<form name="formInvCreate" id="formInvCreate" onsubmit="return false ;">
									<table>
									<?php
										rowDate( FTr::tr( "Key date"), "_IDate", 10, 10, "", FTr::tr( "ERP-HELP-FIELD-FiltDesc2")) ;
										rowEdit( FTr::tr( "Warehouse id."), "_IWarehouseIdCrit", 32, 32, "%", FTr::tr( "ERP-HELP-FIELD-FiltArticleNo")) ;
										rowEdit( FTr::tr( "Stock id."), "_IStockIdCrit", 32, 32, "%", FTr::tr( "ERP-HELP-FIELD-FiltDescription")) ;
										rowEdit( FTr::tr( "Shelf id."), "_IShelfIdCrit", 32, 32, "%", FTr::tr( "ERP-HELP-FIELD-FiltDesc1")) ;
										?>
									</table>
								</form>
								<button data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.sDispatch( true, 'create', 'formInvCreate') ;">
									<?php echo FTr::tr( "Create Inv") ; ?>
								</button>
								<button data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.qDispatch( false, 'zero') ;">
									<?php echo FTr::tr( "Zero all stock data") ; ?>
								</button>
							</div>
						</div>
					</div>
					<div id="InvFunctionsTC00CP02" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Report") ; ?>">
						<div id="content">
							<div id="maindata">
								<fieldset>
									<legend>Create Inv Printout</legend>
										<form name="invReport" id="invReport"><table>
											<table><?php
												rowEdit( FTr::tr( "Article no."), "_SArticleNo", 16, 16, "", "") ;
												rowEdit( FTr::tr( "Description"), "_SArticleDescr", 48, 128, "", "") ;
												rowEdit( FTr::tr( "Supplier no."), "_SSuppNo", 8, 12, "", "") ;
												rowOption( FTr::tr( "Order by"), "_SOrder", Inv::getROrder(), "0") ;
												?></table> 
											<button data-dojo-type="dijit/form/Button"
												onclick="dispatchForm( 'xml', true, 'report', 'invReport') ;">
												<?php echo FTr::tr( "Create PDF Report (Archive)") ; ?>
											</button>
											<button data-dojo-type="dijit/form/Button"
												onclick="dispatchForm( 'xml', true, 'reportCSV', 'invReport') ;">
												<?php echo FTr::tr( "Create CSV Report (Archive)") ; ?>
											</button>
										</table>
									</form>
								</fieldset>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
