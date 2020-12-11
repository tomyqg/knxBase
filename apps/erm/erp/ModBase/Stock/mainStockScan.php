<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="StockC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="StockC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="StockScanData" id="StockScanData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Data") ; ?>:&nbsp;</th>
							<td><input type="text" name="_IData" id="Data" value="" onkeypress="if(event.keyCode==13){dataEntered();}else{return true ;} return false ; "/>
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Stock no.") ; ?>:&nbsp;</th>
							<td><input type="text" name="_IStockId" id="Stock Id." value="" onkeypress="return false ; "/>
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Article no.") ; ?>:&nbsp;</th>
							<td><input type="text" name="_IArticleNo" id="Article no." value="" onkeypress="return false ; "/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<input type="button" value="Force update" border="0" onclick="forceStockScanUpdate() ; return false ;"/> 
	</div>
</div>
