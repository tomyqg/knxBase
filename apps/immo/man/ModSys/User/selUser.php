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
			<form name="selUserData" id="selUserData" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Customer no."), "_SUserId", 8, 8, "", "") ;
					rowEdit( FTr::tr( "Person"), "_SName", 8, 8, "", "") ;
					?></table>
				<button data-dojo-type="dijit/form/Button"
						onclick="refSelUser( 'ModSys', 'User', 'refSelUser', 'selUserData') ; return false ; ">
					<?php echo FTr::tr( "Refresh") ; ?>
				</button>
			
<center>
<table>
	<tr>
		<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" />
			</td>
		<td><input type="image" type="submit" src="/licon/Blue/24/Repeat.png" name="setStartRow" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selUserFirstTen( 'selUserData') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selUserPrevTen( 'selUserData') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selUserNextTen( 'selUserData') ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selUserLastTen( 'selUserData') ; return false ; " /></td>
	</tr>
</table>
</center>
			</form>
			<div style="width: 500px; height: 400px; overflow: auto;">
				<div id="selUsers">
				</div>
			</div>
		</div>
	</div>
</body>
</html>
