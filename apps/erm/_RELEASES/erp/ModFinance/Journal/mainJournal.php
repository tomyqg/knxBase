<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="JournalBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="JournalCPMain" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="JournalKeyData" id="JournalKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Journal no.") ; ?>:&nbsp;</th>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IJournalNo" id="_IJournalNo" value=""
										onkeypress="return enterKey( event) ;"/>
							</td>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button name="selJournalNr" data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.select.show( '', -1, '') ;">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="JournalCPData" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="JournalTCData" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<!-- JournalePaneMain											-->
			<!-- This pane shows an overview about all existing Journals	-->
			<div id="JournalPaneOverview" data-dojo-type="dijit/layout/ContentPane" style="" title="<?php echo FTr::tr( "Overview") ; ?>">
				<div id="content">
					<div id="depdata">
						<div id="JournalOvRoot">
							<?php tableBlock( "itemViews['dmJournalOv']", "formJournalOvTop") ;		?>
							<table id="TableJournalOv" eissClass="Journal" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id" eissLinkTo="Journal" colspan="2">Id</th>
										<th eissAttribute="Name"><?php echo FTr::tr( "Name") ; ?></th>
										<th eissAttribute="Sprache"><?php echo FTr::tr( "Language") ; ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="JournalPaneMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formJournalMain" id="formJournalMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Journal No."), "_IJournalNo", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Description"), "_IDescription", 24, 32, "", "") ;
								rowTextEdit( FTr::tr( "Remark"), "_IRemark", 64, 5, "", "", "") ;
								?></table> 
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
			<div id="JournalPaneLines" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Book") ; ?>">
				<div id="content">
					<div id="depdata">
						<div id="JournalLineItemsRoot">
							<?php tableBlock( "itemViews['dtvJournalLineItems']", "formJournalLineItemsTop") ;		?>
							<table id="TableJournalLineItems" eissClass="JournalLineItem" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="LineNo">Line</th>
										<th eissAttribute="ItemNo">Item</th>
										<th eissAttribute="Date">Date</th>
										<th eissAttribute="DeAcc">(S) Debit acc.</th>
										<th eissAttribute="CrAcc">(H) Crebit acc.</th>
										<th eissAttribute="AccountDebit">(S) Debit acc.</th>
										<th eissAttribute="AccountCredit">(H) Crebit acc.</th>
										<th eissAttribute="AmountDebit" eissVT="float">(S) Amount</th>
										<th eissAttribute="AmountCredit" eissVT="float">(H) Amount</th>
										<th colspan="5" eissFunctions="edit,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<?php tableBlockBot( "itemViews['dtvJournalLineItems']", "dtvJournalLineItemsBot") ; ?>
						</div>
					</div>
				</div>
			</div>
			<div id="JournalPaneHelp" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Help") ; ?>">
				<div id="JournalTCHelp" data-dojo-type="dijit/layout/TabContainer">
					<div id="JournalHelpDE" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "German") ; ?>">
<h3>Grundbuch</h3>
					</div>
					<div id="JournalHelpEN" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "English") ; ?>">
					</div>
					<div id="JournalHelpFR" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "French") ; ?>">
					</div>
					<div id="JournalHelpES" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Spanish") ; ?>">
					</div>
				</div>
			</div>
			<div id="JournalTc1cp11" data-dojo-type="dijit/layout/ContentPane"  title="Reports">
				<div id="JournalTc1cp11tc1" data-dojo-type="dijit/layout/TabContainer" >
					<div id="JournalTc1cp11tc1cp00" data-dojo-type="dijit/layout/ContentPane"
						title="<?php echo FTr::tr( "Journal") ; ?>">
						<div id="content">
							<div id="maindata">
								<form name="formJournalReport" onsubmit="return false ;">
									<table>
									<?php
										rowDate( FTr::tr( "Start date"), "_IFilterDateFrom", 10, 10, "", "") ;
										rowDate( FTr::tr( "End date"), "_IFilterDateTo", 10, 10, "", "") ;
										rowEdit( FTr::tr( "Account no."), "_IFilterAccountNo", 10, 10, "", "") ;
									?>
									</table>
								</form>
								<button data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.sDispatchCO( true, 'createPDF', 'formJournalReport', 'JournalReport') ;">
									<?php echo FTr::tr( "Create report") ; ?>
								</button>
							</div>
						</div>
					</div>
					<div id="JournalTc1cp11tc1cp01" data-dojo-type="dijit/layout/ContentPane"  title="<?php echo FTr::tr( "Accounts") ; ?>">
					</div>
					<div id="JournalTc1cp11tc1cp02" data-dojo-type="dijit/layout/ContentPane"  title="<?php echo FTr::tr( "Other") ; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
