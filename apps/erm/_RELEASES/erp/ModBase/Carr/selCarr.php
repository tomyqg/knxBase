<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
?>
<html>
<head>
</head>
<body>
	<div id="content">
		<div id="maindata">
			<form name="selCarrData" id="selCarrData" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Carrier no."), "_SCarrier", 8, 8, "", "") ;
					rowEdit( FTr::tr( "Carrier name").":", "_SCarrName", 24, 24, "", "", "onkeyup=\"return refSelCarrCont( 'Base', 'Carr', 'refSelCarr', 'selCarrData') ; \" ") ;
									?></table>
				<button data-dojo-type="dijit/form/Button" 
						onclick="refSelKunde( 'Base', 'Carr', 'refSelCarr', 'selCarrData') ; return false ; ">
					<?php echo FTr::tr( "Refresh") ; ?>
				</button>
<center>
<table>
	<tr>
		<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/Repeat.png" name="setStartRow" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selCarrFirstTen( 'selCarrData') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selCarrPrevTen( 'selCarrData') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selCarrNextTen( 'selCarrData') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selCarrLastTen( 'selCarrData') ; return false ; " /></td>
	</tr>
</table>
</center>
			</form>
			<div style="width: 500px; height: 400px; overflow: auto;">
				<div id="divSelCarr">
				</div>
			</div>
		</div>
	</div>
</body>
</html>
