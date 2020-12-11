<?php
require_once( "config.inc.php") ;
require_once( "globalLib.php") ;
?>
<html>
<head>
</head>
<body>
	<div id="content">
		<div id="maindata">
			<form name="selCuRMAData" id="selCuRMAData" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Order no."), "_SCuRMANr", 8, 8, "", "", "onkeyup=\"return refSelCuRMA( 'ModRMA', 'CuRMA', 'refSelCuRMA', 'selCuRMAData') ; \" ") ;
					rowOption( FTr::tr( "Status"), "_SStatus", CuRMA::getRStatus(), "", "") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"return refSelCuRMA( 'ModRMA', 'CuRMA', 'refSelCuRMA', 'selCuRMAData') ; \" ") ;
					rowEdit( FTr::tr( "PLZ"), "_SZIP", 24, 32, "", "", "onkeyup=\"return refSelCuRMA( 'ModRMA', 'CuRMA', 'refSelCuRMA', 'selCuRMAData') ; \" ") ;
					rowEdit( FTr::tr( "Contact"), "_SContact", 24, 32, "", "", "onkeyup=\"return refSelCuRMA( 'ModRMA', 'CuRMA', 'refSelCuRMA', 'selCuRMAData') ; \" ") ;
					?></table>
				<input type="submit" name="actionUpdCuRMA" value="aktualisieren" tabindex="14" border="0"
						onclick="refSelCuRMA( 'ModRMA', 'CuRMA', 'refSelCuRMA', 'selCuRMAData') ; return false ; ">
<center>
<table>
	<tr>
		<td>
			<input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Blue/24/Repeat.png" name="setStartRow" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selCuRMAFirstTen( 'selCuRMAData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selCuRMAPrevTen( 'selCuRMAData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selCuRMANextTen( 'selCuRMAData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selCuRMALastTen( 'selCuRMAData') ; return false ; " />
			</td>
	</tr>
</table>
</center>
			</form>
			<div style="width: 500px; height: 400px; overflow: auto;">
				<div id="selCuRMA">
				</div>
			</div>
		</div>
	</div>
</body>
</html>
