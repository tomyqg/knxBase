<?php
require_once( "config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "option.php") ;
require_once( "common.php") ;
?>
<div id="InKonfRoot" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="InKonfKey" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form name="InKonfKeyData" id="InKonfKeyData" action="mainMenu.php" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Assembly no.") ; ?>:</th>
							<td><input type="image" src="/licon/yellow/18/left.png" name="prevInKonf" onclick="prevInKonf( MainInputField.value, '') ; return false ;" /></td>
							<td>
								<input type="text" name="_IInKonfNr" id="MainInputField" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'getXMLComplete', MainInputField.value, '', '', null, showInKonfAll) ;}else{return true ;} return false ; "/>
							</td>
							<td><input type="image" src="/licon/yellow/18/right.png" name="nextInKonf" onclick="nextInKonf( MainInputField.value, '') ; return false ;" /></td>
							<td>
								<input type="button" name="btnSelInKonf" value="Select ..." border="0" onclick="selInKonf( 'Base', 'InKonf', document.forms['InKonfKeyData']._IInKonfNr.value, 'getXMLComplete', showInKonfAll) ; return false ; "/> 
							</td>
							<td>
								<div id="lockStateInKonf"></div>
							</td>
							<td class="space" width="100"></td>
							<td class="image">
								<input type="image" src="/rsrc/gif/back-icon.png" onclick="back( '', '') ; return false ;" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/new-icon.png" onclick="newInKonf( '', '') ; return false ;" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/copy-icon.png" disabled onclick="newInKonf( document.forms['InKonfKeyData']._IInKonfNr.value, '') ; return false ;" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/save-icon.png" onclick="saveInKonf( document.forms['InKonfKeyData']._IInKonfNr.value, '') ; return false ;" title="Alle Daten speichern" />
							</td>
							<td class="image">
								<input type="image" src="/rsrc/gif/druckenicon_large.jpg" onclick="createPdfInKonf( document.forms['InKonfKeyData']._IInKonfNr.value, '') ; return false ;" title="PDF erzeugen" />
							</td>
							<td class="image">
								<div id="pdfInKonf">
								</div>
							</td>
							<td class="image">
								<input type="image" src="/licon/yellow/32/Recycle.png" onclick="delInKonf( document.forms['InKonfKeyData']._IInKonfNr.value, '') ; return false ;" title="Alle Daten l&ouml;schen"/>
							</td>
							<td class="image">
								<input type="image" src="/licon/Green/32/refresh.png" onclick="reloadScreenInKonf( document.forms['InKonfKeyData']._IInKonfNr.value, '') ; return false ;" title="Maske neu laden (DEBUG)" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="InKonfData" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="InKonfDataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="InKonfDataTCMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formInKonfMain" id="formInKonfMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowDate( FTr::tr( "Date"), "_IDatumInKonf", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Article no."), "_IArtikelNr", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Qty."), "_IMenge", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Qty. delivered."), "_IMengeGel", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Qty. booked"), "_IMengeGebucht", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Slogan"), "_ISlogan", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Ref. no."), "_IRefNr", 24, 32, "", "") ;
								rowDate( FTr::tr( "Ref. date"), "_IRefDatumInKonf", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Items"), "_IPositionen", 10, 10, "", "") ;
								rowOption( FTr::tr( "Status"), "_IStatus", InKonf::getRStatus(), "0") ;
								?></table>
							<input type="button" value="<?php echo FTr::tr( "Update") ; ?>" tabindex="14" border="0" onclick="requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'upd', document.forms['InKonfKeyData']._IInKonfNr.value, '', '', 'formInKonfMain', showInKonf) ; return false ; ">
						</form> 
					</div>
				</div>
			</div>
			<div id="InKonfDataTCModi" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Modi") ; ?>">
				<div id="content">
					<div id="maindata">
						<form action="KundeBearb.php" method="post" name="formInKonfModi" id="formInKonfModi" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowOption( FTr::tr( "Status"), "_IStatus", InKonf::getRStatus(), "0") ;
								rowHTMLEdit( FTr::tr( "Prefix"), "_RPrefixInKonf", 64, 5, "", "", "onkeypress='mark( _RPrefix) ;'") ;
								rowHTMLEdit( FTr::tr( "Postfix"), "_RPostfixInKonf", 64, 5, "", "", "onkeypress='mark( _RPostfix) ;'") ;
								rowTextEdit( FTr::tr( "Bemerkungen"), "_IRem1", 64, 3, "", "", "onkeypress='mark( _IRem1) ;'") ;
								?></table> 
							<input type="button" value="<?php echo FTr::tr( "Update") ; ?>" tabindex="14" border="0" onclick="requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'upd', document.forms['InKonfKeyData']._IInKonfNr.value, '', '', 'formInKonfModi', showInKonf) ; return false ; ">
							<input type="reset" value="Reset input fields" tabindex="15" border="0"> 
						</form> 
					</div>
				</div>
			</div>
			<div id="InKonfDataTCPosten" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Items") ; ?>">
<!--
	tell the artikel selector to:
	use an object of classe "InKonf" with the key of the "MainInputField" (InKonfNr)
	with this run the script "/Common/hdlObject.php" to call the method "InKonf".addPos and when done
	run the javascript function "showInKonfPosten"
-->
				<div id="content">
					<div id="maindata">
						<input type="button" name="addPos" value="<?php FTr::tr( "New item ...") ; ?>" onclick="selArtikelVK( 'Base', 'InKonf', document.forms['InKonfKeyData']._IInKonfNr.value, 'addPos', showTableInKonfPosten) ;" />
						<input type="button" value="<?php echo FTr::tr( "Reserve") ; ?>" onclick="requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'buche', document.forms['InKonfKeyData']._IInKonfNr.value, '', '0', null, showTableInKonfPosten) ; return false ; ">
						<input type="button" value="<?php echo FTr::tr( "Un-Reserve") ; ?>" onclick="requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'unbuche', document.forms['InKonfKeyData']._IInKonfNr.value, '', '0', null, showTableInKonfPosten) ; return false ; ">
					</div>
				</div>
				<div id="TableInKonfPosten">
				</div>
			</div>
			<div id="InKonfDataTCFunktionen" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Functions") ; ?>">
			</div>
		</div>
	</div>
</div>
