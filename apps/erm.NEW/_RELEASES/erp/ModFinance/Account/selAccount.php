<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<html>
<head>
<link rel="stylesheet" type="text/css" media="screen" href="/styles/v1<?php echo $_SERVER['PHP_AUTH_USER'] ; ?>.css" title="Version 1 <?php echo $_SERVER['PHP_AUTH_USER'] ; ?>" />
</head>
<body>
	<div id="content">
		<div id="maindata">
			<form name="formselAccountFilter" id="formselAccountFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Account no."), "_SAccountNo", 8, 8, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Description"), "_SDescription", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					?></table>
			</form>
		</div>
		<div id="depdata">
			<div id="AccountSurveyRoot">
				<?php tableBlock( "itemViews['selAccountDTV']", "formselAccountTop") ; ?>
				<table id="TableselAccountSurvey" eissClass="Account" eissSelKey="AccountNo" width="100%">
					<thead>
						<tr eissType="header">
							<th width="50" eissAttribute="Id"><?php echo FTr::tr( "Id") ; ?></th>
							<th width="50" eissAttribute="AccountNo"><?php echo FTr::tr( "Account no.") ; ?></th>
							<th width="50" eissAttribute="SubAccountNo"><?php echo FTr::tr( "Sub-Account no.") ; ?></th>
							<th width="150" eissAttribute="Description1"><?php echo FTr::tr( "Description 1") ; ?></th>
							<th width="150" eissAttribute="Description2"><?php echo FTr::tr( "Description 2") ; ?></th>
 							<th colspan="1" eissFunctions="select"><?php echo FTr::tr( "Functions") ; ?></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</body>
</html>
