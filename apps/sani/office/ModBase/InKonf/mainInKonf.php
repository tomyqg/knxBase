<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="InKonfRoot" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="InKonfKey" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="InKonfKeyData" id="InKonfKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Assembly no.") ; ?>:</th>
							<td class="space"><input type="image" src="/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IInKonfNr" id="MainInputField" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'getXMLComplete', MainInputField.value, '', '', null, showInKonfAll) ;}else{return true ;} return false ; "/>
							</td>
							<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" 
									onclick="selInKonf( 'Base', 'InKonf', document.forms['InKonfKeyData']._IInKonfNr.value, 'getXMLComplete', showInKonfAll) ; return false ; "/>
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
							<td>
								<div id="lockStateInKonf"></div>
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
							<button data-dojo-type="dijit/form/Button"
									onclick="requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'upd', document.forms['InKonfKeyData']._IInKonfNr.value, '', '', 'formInKonfMain', showInKonf) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
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
							<button data-dojo-type="dijit/form/Button" 
									onclick="requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'upd', document.forms['InKonfKeyData']._IInKonfNr.value, '', '', 'formInKonfModi', showInKonf) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
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
						<button data-dojo-type="dijit/form/Button" onclick="selArtikelVK( 'Base', 'InKonf', document.forms['InKonfKeyData']._IInKonfNr.value, 'addPos', showTableInKonfPosten) ;">
							<?php echo FTr::tr( "New item ...") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'buche', document.forms['InKonfKeyData']._IInKonfNr.value, '', '0', null, showTableInKonfPosten) ; return false ; ">
							<?php echo FTr::tr( "Book") ; ?>
						</button>
						<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'unbuche', document.forms['InKonfKeyData']._IInKonfNr.value, '', '0', null, showTableInKonfPosten) ; return false ; ">
							<?php echo FTr::tr( "Un-book") ; ?>
						</button>
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
