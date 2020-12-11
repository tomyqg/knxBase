<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
?>
<html>
<head>
<link rel="stylesheet" type="text/css" media="screen" href="/styles/v1<?php echo $_SERVER['PHP_AUTH_USER'] ; ?>.css" title="Version 1 <?php echo $_SERVER['PHP_AUTH_USER'] ; ?>" />
</head>
<body>
	<div id="content">
		<div id="maindata">
			<form name="selStockData" id="selStockData" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Criteria"), "_SStockId", 8, 8, "", "") ;
					?></table>
				<button data-dojo-type="dijit/form/Button"
						onclick="refSelStock( 'Base', 'Stock', 'refSelStock', 'selStockData') ; return false ; ">
					<?php echo FTr::tr( "Refresh") ; ?>
				</button>
<center>
<table>
	<tr>
		<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/Repeat.png" name="setStartRow" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selStockFirstTen( 'selStockData') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selStockPrevTen( 'selStockData') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selStockNextTen( 'selStockData') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selStockLastTen( 'selStockData') ; return false ; " /></td>
	</tr>
</table>
</center>
			</form>
			<div style="width: 500px; height: 400px; overflow: auto;">
				<div id="selStock"></div>
			</div>
		</div>
	</div>
</body>
</html>
