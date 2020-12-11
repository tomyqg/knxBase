<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "option.php") ;
?>
<html>
<head>
</head>
<body>
	<div id="content">
		<div id="maindata">
			<form name="selKdLeihData" id="selKdLeihData" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Testdrive no."), "_SKdLeihNr", 8, 8, "", "") ;
					rowOption( FTr::tr( "Status"), "_SStatus", KdLeih::getRStatus(), "", "") ;
					rowEdit( FTr::tr( "Company"), "_SFirma", 24, 32, "", "") ;
					rowEdit( FTr::tr( "Contact"), "_SName", 24, 32, "", "") ;
					?></table>
				<input type="submit" name="actionUpdKdLeih" value="aktualisieren" tabindex="14" border="0"
						onclick="refSelKdLeih( 'Base', 'KdLeih', 'refSelKdLeih', 'selKdLeihData') ; return false ; ">
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
			<input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selKdLeihFirstTen( 'selKdLeihData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selKdLeihPrevTen( 'selKdLeihData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selKdLeihNextTen( 'selKdLeihData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selKdLeihLastTen( 'selKdLeihData') ; return false ; " />
			</td>
	</tr>
</table>
</center>
			</form>
			<div style="width: 500px; height: 400px; overflow: auto;">
				<div id="selKdLeih">
				</div>
			</div>
		</div>
	</div>
</body>
</html>
