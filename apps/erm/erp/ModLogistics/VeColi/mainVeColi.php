<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="VeColiBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="VeColiCPKey" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="VeColiKeyData" id="VeColiKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Colli Nr.") ; ?>:</th>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IVeColiNr" id="MainInputField"
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" name="btnSelVeColi"
									onclick="screenCurrent.selVeColi.show( '', -1, '') ; ">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
							<td>
								<div id="lockStateVeColi"></div>
							</td>
							<td class="image">
								<input type="image" src="/Rsrc/licon/Blue/32/object_04.png"
									onclick="screenCurrent.qDispatch( true, 'schedule') ;"
									title="<?php echo FTr::tr( "Register packages at Carrier") ; ?>"/>
							</td>
							<td class="image">
								<div id="pdfVeColi">
								</div>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="VeColiCPData" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="VeColiDataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="VeColiDataTCMain" data-dojo-type="dijit/layout/ContentPane" title="Allgemein">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formVeColiMain" id="formVeColiMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowDate( FTr::tr( "Date"), "_IDatumVeColi", 10, 10, "", "") ;
								rowOption( FTr::tr( "Type"), "_IVeColiTyp", VeColi::getRVeColiTyp(), "", "") ;
								rowEdit( FTr::tr( "Ref. no."), "_IRefNr", 24, 32, "", "") ;
								rowOption( FTr::tr( "Carrier"), "_ICarrier", Carr::getRCarrier(), "", "") ;
								rowEdit( FTr::tr( "Package count"), "_IAnzahlPakete", 6, 6, "", "") ;
								rowEdit( FTr::tr( "Total value"), "_FGesamtWert", 8, 8, "", "") ;
								rowEdit( FTr::tr( "Total insurance"), "_FGesamtVersKost", 8, 8, "", "") ;
								rowOption( FTr::tr( "Status"), "_IStatus", VeColi::getRStatus(), "", "") ;
							?></table>
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'VeColi', '/Common/hdlObject.php', 'upd', document.forms['VeColiKeyData']._IVeColiNr.value, '', '', 'formVeColiMain', showVeColi) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button" onclick="clrFields() ;">
								<?php echo FTr::tr( "Clear") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="VeColiDataTCKunde" data-dojo-type="dijit/layout/ContentPane" title="Anschriften">
				<div id="content">
					<div id="maindata">
						<?php
							compCust( "VeColiCPKunde", "Receiver-", "VeColi", "", "", "") ;
						?>
					</div>
				</div>
			</div>
			<div id="VeColiDataTCModi" data-dojo-type="dijit/layout/ContentPane" title="Verkaufsmodalit&auml;ten">
				<div id="content">
					<div id="maindata">
						<form action="KundeBearb.php" method="post" name="formVeColiModi" id="formVeColiModi" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowTextEdit( FTr::tr( "Remarks"), "_IRem1", 64, 3, "", "", "onkeypress='mark( _IRem1) ;'") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'VeColi', '/Common/hdlObject.php', 'upd', document.forms['VeColiKeyData']._IVeColiNr.value, '', '', 'formVeColiModi', showVeColi) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="VeColiDataTCPosten" data-dojo-type="dijit/layout/ContentPane" title="Posten">
				<div id="content">
					<div id="maindata">
					</div>
					<div id="depdata">
						<button data-dojo-type="dijit/form/Button"
							onclick="requestUni( 'Base', 'VeColi', '/Common/hdlObject.php', 'getShipFee', document.forms['VeColiKeyData']._IVeColiNr.value, '', '', null, showVeColiAll) ; return false ;">
							<?php echo FTr::tr( "Get shipment cost") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button"
							onclick="requestUni( 'Base', 'VeColi', '/Common/hdlObject.php', 'getInsFee', document.forms['VeColiKeyData']._IVeColiNr.value, '', '', null, showVeColiAll) ; return false ;">
							<?php echo FTr::tr( "Get insurance") ; ?>
						</button>
						<div id="TableVeColiPostenRoot">
							<?php tableBlock( "itemViews['dtvVeColiItems']", "formVeColiItemsTop") ; ?>
							<table id="TableVeColiItems" eissClass="VeColiPos" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id"><?php echo FTr::tr( "Id") ; ?></th>
										<th eissAttribute="PosNr"><?php echo FTr::tr( "Item no.") ; ?></th>
										<th eissAttribute="Gewicht"><?php echo FTr::tr( "Weight") ; ?></th>
										<th eissAttribute="EinzelDimL"><?php echo FTr::tr( "Length") ; ?></th>
										<th eissAttribute="EinzelDimB"><?php echo FTr::tr( "Width") ; ?></th>
										<th eissAttribute="EinzelDimH"><?php echo FTr::tr( "Height") ; ?></th>
										<th eissAttribute="Wert" ><?php echo FTr::tr( "Value") ; ?></th>
										<th eissAttribute="VrsndKost"><?php echo FTr::tr( "Shipping cost") ; ?></th>
										<th eissAttribute="VrschngKost"><?php echo FTr::tr( "Insurance cost") ; ?></th>
										<th colspan="4" eissFunctions="edit,move,delete"><?php echo FTr::tr( "Functions") ; ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<?php tableBlockBot( "itemViews['dtvVeColiItems']", "formVeColiItemsBot") ; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
