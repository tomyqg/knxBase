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
			<form name="formselLiefFilter" id="formselLiefFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Supplier no."), "_SSuppNo", 8, 8, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ; \" ") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ; \" ") ;
					rowEdit( FTr::tr( "ZIP").":", "_SZIP", 8, 8, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ; \" ") ;
					rowEdit( FTr::tr( "Contact"), "_SName", 24, 32, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ; \" ") ;
					rowEdit( FTr::tr( "Phone/FAX"), "_SPhone", 24, 32, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ;\"") ;
					?></table>
			</form>
		</div>
		<div id="depdata">
			<div id="LiefSurveyRoot">
				<?php tableBlock( "itemViews['selLiefDTV']", "formselLiefTop") ; ?>
				<table id="TableselLiefSurvey" eissClass="Lief" eissSelKey="LiefNr" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id"><?php echo FTr::tr( "Id") ; ?></th>
							<th eissAttribute="LiefNr"><?php echo FTr::tr( "Supplier no.") ; ?></th>
							<th eissAttribute="FirmaName1"><?php echo FTr::tr( "Company") ; ?></th>
							<th eissAttribute="PLZ"><?php echo FTr::tr( "ZIP") ; ?></th>
 							<th eissAttribute="Name"><?php echo FTr::tr( "Name") ; ?></th>
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
