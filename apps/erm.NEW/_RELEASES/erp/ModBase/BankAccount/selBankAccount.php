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
			<form name="selBankAccountData" id="selBankAccountData" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "ERP No."), "_SERPNo", 8, 8, "", "", "onkeyup=\"return refSelBankAccount( 'Base', 'BankAccount', 'refSelBankAccount', 'selBankAccountData') ; \" ") ;
					rowEdit( FTr::tr( "FullName"), "_SFullName", 32, 32, "", "", "onkeyup=\"return refSelBankAccount( 'Base', 'BankAccount', 'refSelBankAccount', 'selBankAccountData') ; \" ") ;
					rowEdit( FTr::tr( "Account no."), "_SAccountNo", 8, 8, "", "", "onkeyup=\"return refSelBankAccount( 'Base', 'BankAccount', 'refSelBankAccount', 'selBankAccountData') ; \" ") ;
					?></table>
				<button type="button" data-dojo-type="dijit/form/Button"name="actionUpdBankAccount" tabindex="14" border="0"
						onclick="refSelBankAccount( 'Base', 'BankAccount', 'refSelBankAccount', 'selBankAccountData') ; return false ; ">
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
			<input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="selBankAccountFirstTen( 'selBankAccountData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="selBankAccountPrevTen( 'selBankAccountData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="selBankAccountNextTen( 'selBankAccountData') ; return false ; " />
			</td>
		<td>
			<input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="selBankAccountLastTen( 'selBankAccountData') ; return false ; " />
			</td>
	</tr>
</table>
</center>
			</form>
			<div style="width: 500px; height: 400px; overflow: auto;">
				<div id="selBankAccount">
				</div>
			</div>
		</div>
	</div>
</body>
</html>
