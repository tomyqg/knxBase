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
			<form name="selProjData" id="selProjData" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Offer no."), "_SProjNr", 8, 8, "", "", "onkeyup=\"return refSelProj( 'ModProj', 'Proj', 'refSelProj', 'selProjData') ; \" ") ;
					rowOption( FTr::tr( "Status"), "_SStatus", Proj::getRStatus(), "", "") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"return refSelProj( 'ModProj', 'Proj', 'refSelProj', 'selProjData') ; \" ") ;
					rowEdit( FTr::tr( "ZIP"), "_SZIP", 24, 32, "", "", "onkeyup=\"return refSelProj( 'ModProj', 'Proj', 'refSelProj', 'selProjData') ; \" ") ;
					rowEdit( FTr::tr( "Contact"), "_SContact", 24, 32, "", "", "onkeyup=\"return refSelProj( 'ModProj', 'Proj', 'refSelProj', 'selProjData') ; \" ") ;
					?></table>
				<input type="submit" name="actionUpdProj" value="<?php echo FTr::tr( "Refresh") ; ?>" tabindex="14" border="0"
						onclick="refSelProj( 'Base', 'Proj', 'refSelProj', 'selProjData') ; return false ; ">
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
			<input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selProjFirstTen( 'selProjData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selProjPrevTen( 'selProjData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selProjNextTen( 'selProjData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selProjLastTen( 'selProjData') ; return false ; " />
			</td>
	</tr>
</table>
</center>
			</form>
			<div style="width: 500px; height: 400px; overflow: auto;">
				<div id="selProj">
				</div>
			</div>
		</div>
	</div>
</body>
</html>
