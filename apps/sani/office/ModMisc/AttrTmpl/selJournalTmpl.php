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
			<form name="selJournalTmplData" id="selJournalTmplData" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Template no."), "_SJournalTmplNo", 8, 8, "", "", "onkeyup=\"return refSelJournalTmpl( 'ModMisc', 'JournalTmpl', 'refSelJournalTmpl', 'selJournalTmplData') ; \" ") ;
					rowEdit( FTr::tr( "Keyword"), "_SKeywords", 24, 32, "", "", "onkeyup=\"return refSelJournalTmpl( 'ModMisc', 'JournalTmpl', 'refSelJournalTmpl', 'selJournalTmplData') ; \" ") ;
					?></table>
				<button data-dojo-type="dijit/form/Button" name="actionUpdJournalTmpl" 
						onclick="refSelJournalTmpl( 'ModMisc', 'JournalTmpl', 'refSelJournalTmpl', 'selJournalTmplData') ; return false ; ">
					<?php echo FTr::tr( "Refresh") ; ?>
				</button>
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
			<input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selJournalTmplFirstTen( 'selJournalTmplData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selJournalTmplPrevTen( 'selJournalTmplData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selJournalTmplNextTen( 'selJournalTmplData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selJournalTmplLastTen( 'selJournalTmplData') ; return false ; " />
			</td>
	</tr>
</table>
</center>
			</form>
			<div style="width: 500px; height: 400px; overflow: auto;">
				<div id="selJournalTmpl">
				</div>
			</div>
		</div>
	</div>
</body>
</html>
