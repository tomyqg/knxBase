<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
?>
<div id="ConfigObjC3s1" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="ConfigObjC3s1c1" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="ConfigObjKeyData" id="ConfigObjKeyData" onsubmit="return false ;" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "ConfigObj id") ; ?>:</th>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IId" id="_IId" value=""
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.selConfigObj.show( '', -1, '') ;">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Name") ; ?></th>
							<td colspan="4">
								<input type="text" name="_DParameter" id="VOID" size="35" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="ConfigObjC3s1c2" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="ConfigObjTc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="ConfigObjTc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formConfigObjMain" id="formConfigObjMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Class"), "_IClass", 64, 64, "", "") ;
								rowEdit( FTr::tr( "Block"), "_IBlock", 64, 128, "", "") ;
								rowEdit( FTr::tr( "Parameter"), "_IParameter", 64, 64, "", "") ;
								rowEdit( FTr::tr( "Value"), "_IValue", 64, 64, "", "") ;
								rowEdit( FTr::tr( "Help"), "_IHelp", 64, 64, "", "") ;
								?>
							</table> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModSys', 'ConfigObj', '/Common/hdlObject.php', 'add', '', '', '', 'formConfigObjMain', showConfigObj) ; return false ; ">
								<?php echo FTr::tr( "Create") ?>
							</button>
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModSys', 'ConfigObj', '/Common/hdlObject.php', 'upd', '', document.forms['ConfigObjKeyData']._IId.value, '', 'formConfigObjMain', showConfigObj) ; return false ; ">
								<?php echo FTr::tr( "Update") ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="ConfigObjTabFunc" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Reports") ; ?>">
				<div id="ConfigObjTabFuncTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="ConfigObjTabFuncReports" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Config Reports") ; ?>">
						<div id="content">
							<div id="maindata">
								All filter criteria are AND concatenated!<br/>
								<form name="formConfigObjReport" onsubmit="return false ;">
									<table>
									<?php
										rowEdit( FTr::tr( "Class"), "_IFiltClass", 32, 32, "%", FTr::tr( "ERP-HELP-FIELD-FiltClass")) ;
										rowEdit( FTr::tr( "Block"), "_IFiltBlock", 32, 32, "%", FTr::tr( "ERP-HELP-FIELD-FiltBlock")) ;
										rowEdit( FTr::tr( "Parameter"), "_IFiltParameter", 32, 32, "%", FTr::tr( "ERP-HELP-FIELD-FiltParameter")) ;
										rowEdit( FTr::tr( "Value"), "_IFiltValue", 32, 32, "%", FTr::tr( "ERP-HELP-FIELD-FiltValue")) ;
										?>
									</table>
								</form>
							</div>
						</div>
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.sDispatchCO( true, 'createPDF', 'formConfigObjReport', 'ConfigReport') ;">
							<?php echo FTr::tr( "Create report") ; ?>
						</button>
					</div>
					<div id="ConfigObjTabFuncReportDbTable" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Database Tables Reports") ; ?>">
						<div id="content">
							<div id="maindata">
								<form name="formDbTableReport" onsubmit="return false ;">
									<table>
									<?php
										rowEdit( FTr::tr( "Table Name"), "_IFiltTableName", 32, 32, "", FTr::tr( "ERP-HELP-FIELD-DbTableName")) ;
										?>
									</table>
								</form>
							</div>
						</div>
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.sDispatchCO( true, 'createPDF', 'formDbTableReport', 'DbTableReport') ;">
							<?php echo FTr::tr( "Create report") ; ?>
						</button>
					</div>
					<div id="ConfigObjTabFuncReportDictionary" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Database Dictionary Reports") ; ?>">
						<div id="content">
							<div id="maindata">
								<form name="formDictionaryReport" onsubmit="return false ;">
									<table>
									<?php
										rowEdit( FTr::tr( "Table Name"), "_IFiltTableName", 32, 32, "", FTr::tr( "ERP-HELP-FIELD-DbTableName")) ;
										?>
									</table>
								</form>
							</div>
						</div>
						<button data-dojo-type="dijit/form/Button"
							onclick="screenCurrent.sDispatchCO( true, 'createPDF', 'formDictionaryReport', 'DbDictionaryReport') ;">
							<?php echo FTr::tr( "Create report") ; ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>