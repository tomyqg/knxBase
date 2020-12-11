<?php
require_once( "config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "option.php") ;
require_once( "common.php") ;
?>
<div id="SearchC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="SearchC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form name="SearchKeyData" id="SearchKeyData" onsubmit="return false ;" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Search term") ; ?></th>
							<td>
								<input type="text" name="_ISearchKey" id="MainInputField" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'Search', '/Common/hdlObject.php', 'getTableAsXML', '', '', document.forms['SearchKeyData']._ISearchKey.value, null, showTableSearch) ;}else{return true ;} return false ; "/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="SearchC3s1c2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="SearchC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="SearchCPMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Objects in database") ; ?>">
				<div id="content">
					<div id="depdata">
						<div id="divSearch">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
