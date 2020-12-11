<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="JournalTmplRoot" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="JournalTmplKey" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="JournalTmplKeyData" id="JournalTmplKeyData" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Template no.") ; ?>:&nbsp;</th>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IJournalTmplNo" id="_IJournalTmplNo" value=""
									onkeypress="enterKey( event) ; return false ;"/>
							</td>
							<td class="space">
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" onclick="selJournalTmpl( 'ModFinance', 'JournalTmpl', '', 'getXMLComplete', showJournalTmplAll) ;"> 
									<?php echo FTr::tr( "Select ...") ; ?>
								</button>
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Description") ; ?></th>
							<td colspan="4">
								<input type="text" name="_DDescription" id="VOID" size="35" value="" />
							</td>
						</tr>
						</table>
				</form>
			</div>
		</div>
	</div>
	<div id="JournalTmplData" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="JournalTmplDataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="JournalTmplDataTCMain" data-dojo-type="dijit/layout/ContentPane" title="Main">
				<div id="content">
					<div id="maindata">
						<form name="formJournalTmplMain" id="formJournalTmplMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Journal template no."), "_IJournalTmplNo", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Description"), "_IDescription", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Total amount in %"), "_FAmountTotal", 8, 8, "", "") ;
								?></table>
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModMisc', 'JournalTmpl', '/Common/hdlObject.php', 'upd', document.forms['JournalTmplKeyData']._IJournalTmplNo.value, '', '', 'formJournalTmplMain', showJournalTmpl) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form>
					</div>
				</div>
			</div>
			<div id="JournalTmplDataTCItems" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Booking Set") ; ?>">
				<div id="content">
					<div id="depdata">
						<div id="TableJournalTmplItemRoot">
							<?php tableBlock( "itemViews['dtvJournalTmplItems']", "formJournalTmplItemsTop") ; ?>
							<table id="TableJournalTmplItems" eissClass="JournalTmplItem" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="JournalTmplNo"><?php echo FTr::tr( "Template no.") ; ?></th>
										<th eissAttribute="ItemNo"><?php echo FTr::tr( "Item no.") ; ?></th>
										<th eissAttribute="Description"><?php echo FTr::tr( "Description") ; ?></th>
										<th eissAttribute="AccountDebit"><?php echo FTr::tr( "Account debit") ; ?></th>
										<th eissAttribute="AccountCredit"><?php echo FTr::tr( "Account credit") ; ?></th>
										<th eissAttribute="CrAcc"><?php echo FTr::tr( "Description") ; ?></th>
										<th eissAttribute="DeAcc"><?php echo FTr::tr( "Description") ; ?></th>
										<th eissAttribute="AmountDebit"><?php echo FTr::tr( "Amount debit") ; ?></th>
										<th eissAttribute="AmountCredit"><?php echo FTr::tr( "Amount credit") ; ?></th>
										<th colspan="2" eissFunctions="edit,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<?php tableBlockBot( "itemViews['dtvJournalTmplItems']", "formJournalTmplItemsBot") ; ?>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>
