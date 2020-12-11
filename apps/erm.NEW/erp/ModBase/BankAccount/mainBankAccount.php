<?php
require_once( "config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "option.php") ;
?>
<div id="BankAccountSC1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="BankAccountSC1CP1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="BankAccountKeyData" id="BankAccountKeyData" onsubmit="return false ;">  
					<table>
						<tr>
							<th><?php echo FTr::tr( "ERP No.") ;?>:</th>
							<td class="space"><input type="image" src="/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IERPNo" id="_IERPNo" size="10" maxlength="8" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'BankAccount', '/Common/hdlObject.php', 'getXMLComplete', document.forms['BankAccountKeyData']._IERPNo.value, '', '', null, showBankAccountAll) ;}else{return true ;} return false ; " />
							</td>
								<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button type="button" data-dojo-type="dijit/form/Button" name="sel_BankAccountNr" border="0" onclick="selBankAccount( 'Base', 'BankAccount', 'getXMLComplete', document.forms['BankAccountKeyData']._IERPNo.value, showBankAccountAll) ; return false ; ">
									<?php echo FTr::tr( "Select ...")?>
								</button>
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Name") ; ?>:</th>
							<td colspan="4">
								<input type="text" name="_DFullName" id="VOID" size="64" value="" /><br/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="BankAccountSC1CP2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="BankAccountTc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="BankAccountTc1cp1" data-dojo-type="dijit/layout/ContentPane" title="Allgemein">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formBankAccountMain" id="formBankAccountMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Owner name"), "_IFullName", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Bank name 1"), "_IBankName1", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Bank name 2"), "_IBankName2", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Bank address"), "_IAddress", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Bank code"), "_IBankCode", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Account no."), "_IAccountNo", 32, 32, "", "") ;
								rowEdit( FTr::tr( "BICC"), "_IBICC", 32, 32, "", "") ;
								rowEdit( FTr::tr( "IBAN"), "_IIBAN", 32, 32, "", "") ;
								rowEdit( FTr::tr( "SWIFT"), "_ISWIFT", 32, 32, "", "") ;
								rowEdit( FTr::tr( "ABA"), "_IABA", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Miscellaneous 1"), "_IMisc1", 64, 128, "", "") ;
								rowEdit( FTr::tr( "Miscellaneous 2"), "_IMisc2", 64, 128, "", "") ;
								?></table> 
							<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'BankAccount', '/Common/hdlObject.php', 'add', document.forms['BankAccountKeyData']._IERPNo.value, '', '', null, showBankAccount) ; return false ; ">
								<?php echo FTr::tr( "Create") ; ?>
							</button>
							<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'BankAccount', '/Common/hdlObject.php', 'upd', document.forms['BankAccountKeyData']._IERPNo.value, '', '', 'formBankAccountMain', showBankAccount) ; return false ; ">
								<?php echo FTr::tr( "Update") ;?>
							</button>
						</form> 
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
</script>
