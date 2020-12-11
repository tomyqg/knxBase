<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
?>
<div id="AccountBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="AccountCPKey" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form name="AccountKeyData" id="AccountKeyData" onsubmit="return false ;" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Account no.") ; ?>:</th>
							<td class="space">
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IAccountNo" id="_IAccountNo" value=""
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td class="space">
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" 
									onclick="screenCurrent.select.show( '', -1, '') ; "/>
									<?php echo FTr::tr( "Select") ; ?>
								</button>
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Description 1") ; ?></th>
							<td colspan="4">
								<input type="text" name="_DDescription1" id="VOID" size="35" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="AccountCPMain" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="AccountData_TC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="AccountDataGeneral_CP" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formAccountMain" id="formAccountMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Account no."), "_IAccountNo", 6, 6, "", "") ;
								rowEdit( FTr::tr( "Sub-Account no."), "_ISubAccountNo", 6, 6, "", "") ;
								rowEdit( FTr::tr( "Description 1"), "_IDescription1", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Description 2"), "_IDescription2", 32, 64, "", "") ;
								?>
							</table> 
							<button type="button" data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'add', 'formAccountMain') ; return false ;">
								<?php echo FTr::tr( "Create") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'upd', 'formAccountMain') ; return false ;">
								<?php echo FTr::tr( "Update ...") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="AccountDataRemarks_CP" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Balances") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formAccountModi" id="formAccountModi" enctype="multipart/form-data">
							<table><?php
								rowTextEdit( FTr::tr( "Remark(s)"), "_DRem1", 64, 6, "", "") ;
								?>
							</table> 
							<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'Account', '/Common/hdlObject.php', 'upd', document.forms['AccountKeyData']._IAccountNo.value, '', '', 'formAccountModi', showAccount) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
							<button type="reset" data-dojo-type="dijit/form/Button">
								<?php echo FTr::tr( "Clear") ; ?>
							</button>
						</form> 
					</div>
					<div id="maindata">
						<form method="post" name="formAccountModiRem" id="formAccountModiRem" enctype="multipart/form-data">
							<table><?php
								rowTextEdit( FTr::tr( "Remark to add"), "_IRem", 64, 3, "", "") ;
								?>
							</table> 
							<button type="button" data-dojo-type="dijit/form/Button" tabindex="14" border="0" onclick="requestUni( 'Base', 'Account', '/Common/hdlObject.php', 'addRem', document.forms['AccountKeyData']._IAccountNo.value, '', '', 'formAccountModiRem', showAccount) ; return false ; ">
								<?php echo FTr::tr( "Add remark") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

