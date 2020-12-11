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
			<form name="formselPSuOrdrFilter" id="formselPSuOrdrFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Order proposel no."), "_SPSuOrdrNo", 8, 8, "", "", "onkeyup=\"screenCurrent.selPSuOrdr.cache( event, true) ;\"") ;
					rowOption( FTr::tr( "Status"), "_SStatus", PSuOrdr::getRStatus(), "", "", "onkeyup=\"screenCurrent.selPSuOrdr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"screenCurrent.selPSuOrdr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "PLZ"), "_SZIP", 24, 32, "", "", "onkeyup=\"screenCurrent.selPSuOrdr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Contact"), "_SContact", 24, 32, "", "", "onkeyup=\"screenCurrent.selPSuOrdr.cache( event, true) ;\"") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" 
					onclick="screenCurrent.selPSuOrdr.clear( 'formselPSuOrdrFilter') ;">
				<?php echo FTr::tr( "Clear filter") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="PSuOrdrSurveyRoot">
				<?php tableBlock( "itemViews['selPSuOrdrDTV']", "formselPSuOrdrTop") ;		?>
				<table id="TableselPSuOrdrSurvey" eissClass="PSuOrdr" eissSelKey="PSuOrdrNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="PSuOrdrNo">PSuOrdrNo</th>
							<th eissAttribute="Datum">Datum</th>
							<th eissAttribute="Status">Status</th>
							<th eissAttribute="FirmaName1">Firma</th>
							<th eissAttribute="PLZ">PLZ</th>
							<th eissAttribute="Name">Ansprechpartner</th>
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
