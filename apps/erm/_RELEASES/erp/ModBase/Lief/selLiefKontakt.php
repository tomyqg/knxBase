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
			<form name="formselLiefKontaktFilter" id="formselLiefKontaktFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Supplier no."), "_SSuppNo", 8, 8, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "ZIP"), "_SZIP", 8, 8, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Contact"), "_SName", 24, 32, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Phone/FAX"), "_SPhone", 24, 32, "", "", "onkeyup=\"screenCurrent.selLief.cache( event, true) ;\"") ;
					?></table>
			</form>
		</div>
		<div id="depdata">
			<div id="LiefKontaktSurveyRoot">
				<?php tableBlock( "itemViews['selLiefKontaktDTV']", "formselLiefKontaktTop") ;		?>
				<table id="TableselLiefKontaktSurvey" eissClass="Lief" eissSelKey="Id" width="100%">
					<thead>
						<tr eissType="header">
							<th width="50" eissAttribute="Id">Id</th>
							<th width="50" eissAttribute="LiefNr">Lief Nr.</th>
							<th width="150" eissAttribute="FirmaName1">Firma</th>
							<th width="50" eissAttribute="PLZ">PLZ</th>
							<th width="150" eissAttribute="Name">Name</th>
 							<th colspan="1" eissFunctions="select">Functions</th>
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
