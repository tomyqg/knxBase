<?php
require_once( "config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "option.php") ;
require_once( "AbKorr.php") ;
require_once( "common.php") ;
?>
<div id="AbKorrC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="AbKorrC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="AbKorrKeyData" id="AbKorrKeyData" onsubmit="return false ;">  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Stock correction no.") ; ?>:&nbsp;</th>
							<td class="space"><input type="image" src="/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IAbKorrNr" id="_IAbKorrNr" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'getXMLComplete', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', '', null, showAbKorrAll) ;}else{return true ;} return false ; "/>
							</td>
							<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" name="selAbKorrNr" onclick="selAbKorr( 'Base', 'AbKorr', 'getXMLComplete', document.forms['AbKorrKeyData']._IAbKorrNr, showAbKorrAll) ; return false ; ">
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
	<div id="AbKorrC3s1c2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="AbKorrC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<!-- 									 						 -->
			<!-- Overview over all existing Artikel Stock Corrections		 -->
			<!-- 															 -->
			<div id="AbKorrOverview" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "List") ; ?>">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onClick="requestUni( 'Base', 'DataMinerAbKorr', '/Common/hdlObject.php', 'getTableAll', '', '', '', null, showTableAbKorrOV) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="TableAbKorrOV">
						</div>
					</div>
				</div>
			</div>
			<!-- 															 -->
			<!-- INdividual Article Stock Correction, Main Data				 -->
			<!-- 															 -->
			<div id="AbKorrMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formAbKorrMain" id="formAbKorrMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowDate( FTr::tr( "Date"), "_IDatum", 11, 11, "", "") ;
								rowEdit( FTr::tr( "Description"), "_IDescription", 48, 128, "", "") ;
								rowOption( FTr::tr( "Type"), "_IType", AbKorr::getRType(), "0") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'upd', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', '', 'formAbKorrMain', showAbKorr) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<!-- 															 -->
			<!-- Individual Article Stock Correction, Main Data				 -->
			<!-- 															 -->
			<div id="AbKorrPosten" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Items") ; ?>">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" name="addPos" onclick="selArtikelById( 'Base', 'AbKorr', document.forms['AbKorrKeyData']._IAbKorrNr.value, 'addPos', showTableAbKorrPosten) ;" />
							<?php echo FTr::tr( "New item ...") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'buche', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', '0', null, showAbKorrAll) ; return false ; ">
							<?php echo FTr::tr( "Book") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'unbuche', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', '0', null, showAbKorrAll) ; return false ; ">
							<?php echo FTr::tr( "Un-Book") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'bucheAll', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', '0', null, showTableAbKorrPosten) ; return false ; ">
							<?php echo FTr::tr( "Book all") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'unbucheAll', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', '0', null, showTableAbKorrPosten) ; return false ; ">
							<?php echo FTr::tr( "Un-Book all") ; ?>
						</button>
					</div>
				</div>
<center>
<form name="mainAbKorrItems"><table>
	<tr>
		<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" /></td>
		<td><input type="text" name="_SRowCount" size="2" maxlength="2" value="10" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'getTablePostenAsXML', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', 'f50', 'AbKorrKeyData', showTableAbKorrPosten) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'getTablePostenAsXML', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', 'p50', 'AbKorrKeyData', showTableAbKorrPosten) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" onClick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'getTablePostenAsXML', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', 't50', 'AbKorrKeyData', showTableAbKorrPosten) ; return false ; "/></td>
		<td><input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'getTablePostenAsXML', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', 'n50', 'AbKorrKeyData', showTableAbKorrPosten) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'getTablePostenAsXML', document.forms['AbKorrKeyData']._IAbKorrNr.value, '', 'l50', 'AbKorrKeyData', showTableAbKorrPosten) ; return false ; " /></td>
	</tr>
</table></form>
</center>
				<div id="TableAbKorrPostenRoot">
					<table id="TableAbKorrPosten" eissClass="AbKorrPosten">
						<thead>
							<tr eissType="header">
								<th eissAttribute="Id">Id</th>
								<th eissAttribute="PosNr">Item</th>
								<th eissAttribute="ArtikelNr" eissLinkTo="screenArtikel" colspan="2">Article no.</th>
								<th eissAttribute="ERPNo" eissLinkTo="screenArtikel" colspan="2">ERP no.</th>
								<th eissAttribute="ArtikelBez1">Description</th>
								<th eissAttribute="Menge" eissFunctions="step" colspan="3">Qty.</th>
								<th eissAttribute="MengeProVPE" >Qty. / pack</th>
								<th eissAttribute="MengeGebucht">Qty. booked</th>
								<th colspan="4" eissFunctions="edit,move,delete">Functions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
