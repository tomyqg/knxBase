<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
?>
<div id="MerchantBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="MerchantCPKey" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="MerchantKeyData" id="MerchantKeyData" onsubmit="return false ;" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Merchant id.") ; ?>:</th>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/left.png" name="prevMerchant" onclick="hookPrevObject() ; return false ;" /></td>
							<td>
								<input type="text" name="_IMerchantId" id="_IMerchantId"
									onkeypress="enter( event) ;"/>
							</td>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/right.png" name="nextMerchant"
										onclick="hookNextObject() ; return false ;" /></td>
							<td>
								<button data-dojo-type="dijit/form/Button" name="btnSelMerchantId"
										onclick="screenCurrent.selMerchant.show( '', -1, '') ;"/>
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Name") ; ?></th>
							<td colspan="4">
								<input type="text" name="_DName1" id="VOID" size="35" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="MerchantCPData" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="MerchantTc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="MerchantTc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formMerchantMain" id="formMerchantMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Marketplace"), "_IMarketplace", 8, 8, "", "") ;
								rowEdit( FTr::tr( "Url for XML calls"), "_IUrlXML", 64, 128, "", "") ;
								rowEdit( FTr::tr( "Url for SOAP calls"), "_IUrlSOAP", 64, 128, "", "") ;
								rowEdit( FTr::tr( "Last 'GetOrders' date/time"), "_ILastGetOrderTime", 24, 24, "", "") ;
							?>
							</table> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModMerchant', 'Merchant', '/Common/hdlObject.php', 'upd', document.forms['MerchantKeyData']._IMerchantId.value, '', '', 'formMerchantMain', showMerchant) ; return false ; ">
								<?php echo FTr::tr( "Update ...") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button" onclick="dispatchForm( 'xml', true, 'receiveOrders', 'formMerchantMain') ; return false ;">
								<?php echo FTr::tr( "Import new/updated data ...") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="MerchantTc1cp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Terms of sale") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formMerchantInfo" id="formMerchantInfo" enctype="multipart/form-data">
							<table><?php
								rowEdit( FTr::tr( "Misc. 1"), "_IMisc1", 64, 128, "", "HELP-Misc1") ;
								rowEdit( FTr::tr( "Misc. 2"), "_IMisc2", 64, 128, "", "HELP-Misc2") ;
								rowEdit( FTr::tr( "Misc. 3"), "_IMisc3", 64, 128, "", "HELP-Misc3") ;
								rowTextEdit( FTr::tr( "Misc. 4"), "_IMisc4", 64, 8, "", "HELP-Misc4") ;
								rowTextEdit( FTr::tr( "Remark(s)"), "_IRem", 64, 6, "", "") ;
								?>
							</table> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModMerchant', 'Merchant', '/Common/hdlObject.php', 'upd', document.forms['MerchantKeyData']._IMerchantId.value, '', '', 'formMerchantModi', showMerchant) ; return false ; ">
								<?php echo FTr::tr( "Update ...") ; ?>
							</button>
							<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
						</form> 
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

