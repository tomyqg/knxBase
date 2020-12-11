<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="SCFrontEnd" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="FrontEndCPMain" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
<!-- 
<div>Diese Seite stellt die Schnittstelle zu dem FTP Verzeichnis auf dem Frontend Server dar.<br/>
Die IMPORT Funktion kopiert die Daten aus dem FTP Verzeichnis des Servers in ein lokales Verzeichnis, wertet
die Daten aus und L…SCHT diese anschliessend lokal und AUCH AUF DEM FRONTEND !<br/>
Die Lšschfunktion lšscht die Daten AUF DEM FRONTEND OHNE AUSWERTUNG.<br/>
Alle Daten BLEIBEN IN DER DATENBANK des Frontends gespeichert.<br/>
</div>
-->
		<div id="FrontEndMainTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="FrontEndMainCPCustomer" data-dojo-type="dijit/layout/ContentPane" title="Kunden"
					onShow="requestUni( 'Base', 'FrontEndConnect', '/Common/hdlObject.php', 'getTableCustAsXML', '', '', '', null, showTableFrontEndCust) ; return false ; ">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onClick="requestUni( 'Base', 'FrontEndConnect', '/Common/hdlObject.php', 'getTableCustAsXML', '', '', '', null, showTableFrontEndCust) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="tableFrontEndCust">
						</div>
					</div>
				</div>
			</div>
			<div id="FrontEndMainCPMerkzettel" data-dojo-type="dijit/layout/ContentPane" title="Merkzettel"
					onShow="requestUni( 'Base', 'FrontEndConnect', '/Common/hdlObject.php', 'getTableMZAsXML', '', '', '', null, showTableFrontEndMZ) ; return false ; ">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onClick="requestUni( 'Base', 'FrontEndConnect', '/Common/hdlObject.php', 'getTableMZAsXML', '', '', '', null, showTableFrontEndMZ) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="tableFrontEndMZ">
						</div>
					</div>
				</div>
			</div>
			<div id="FrontEndMainCPCuRfqt" data-dojo-type="dijit/layout/ContentPane" title="Anfragen"
					onShow="requestUni( 'Base', 'FrontEndConnect', '/Common/hdlObject.php', 'getTableCuRfqtAsXML', '', '', '', null, showTableFrontEndCuRfqt) ; return false ; ">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onClick="requestUni( 'Base', 'FrontEndConnect', '/Common/hdlObject.php', 'getTableCuRfqtAsXML', '', '', '', null, showTableFrontEndCuRfqt) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="tableFrontEndCuRfqt">
						</div>
					</div>
				</div>
			</div>
			<div id="FrontEndMainCPCuOrdr" data-dojo-type="dijit/layout/ContentPane" title="Bestellungen"
					onShow="requestUni( 'Base', 'FrontEndConnect', '/Common/hdlObject.php', 'getTableCuOrdrAsXML', '', '', '', null, showTableFrontEndCuOrdr) ; return false ; ">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onClick="requestUni( 'Base', 'FrontEndConnect', '/Common/hdlObject.php', 'getTableCuOrdrAsXML', '', '', '', null, showTableFrontEndCuOrdr) ; return false ; ">
							<?php echo FTr::tr( "Refresh") ; ?>
						</button>
						<div id="tableFrontEndCuOrdr">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
