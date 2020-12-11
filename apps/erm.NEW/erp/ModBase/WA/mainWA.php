<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="WAC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="WAC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form name="WAKeyData" id="WAKeyData" onsubmit="return false ;" >  
					<table>
						<tr>
							<td class="space" width="200"></td>
							<td class="image">
								<input type="image" src="/rsrc/gif/back-icon.png" onclick="back() ; return false ;" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/new-icon.png" onclick="newWA( '', '') ; return false ;" title="Neuen WAn <?php echo FTr::tr( "Create") ; ?> mit aktuellen Daten der Maske"/>
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/druckenicon_large.jpg" onclick="createPdfWA( document.forms['WAKeyData']._IWANr.value, '') ; return false ;" title="PDF erzeugen" />
							</td>
							<td class="image">
								<input type="image" src="/licon/Green/32/refresh.png" onclick="reloadScreenWA( '', '') ; return false ;" title="Maske neu laden (DEBUG)" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="WAC3s1c2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="WAC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="WAOverview" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Pending Orders") ; ?>">
				<div id="content">
					<div id="maindata">
						<input type="button" value="<?php echo FTr::tr( "Refresh") ; ?>" onClick="requestUni( 'Base', 'WAStatus', '/Common/hdlObject.php', 'getTableAsXML', '', '', '', null, showWAAll) ; return false ; " />
						<div id="TableWAPosten">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
