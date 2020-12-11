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
			<form name="selInKonfData" id="selInKonfData" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Assembly no."), "_SInKonfNr", 8, 8, "", "") ;
					rowEdit( FTr::tr( "Description"), "_SDescr", 8, 8, "", "") ;
					?></table>
				<input type="submit" name="actionUpdInKonf" value="aktualisieren" tabindex="14" border="0"
						onclick="refSelInKonf( 'Base', 'InKonf', 'refSelInKonf', 'selInKonfData') ; return false ; ">
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
			<input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selInKonfFirstTen( 'selInKonfData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selInKonfPrevTen( 'selInKonfData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selInKonfNextTen( 'selInKonfData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selInKonfLastTen( 'selInKonfData') ; return false ; " />
			</td>
	</tr>
</table>
</center>
			</form>
			<div style="width: 500px; height: 400px; overflow: auto;">
				<div id="selInKonf">
				</div>
			</div>
		</div>
	</div>
</body>
</html>
