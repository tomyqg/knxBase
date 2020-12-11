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
			<form name="formselKundeKontaktFilter" id="formselKundeKontaktFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Customer no."), "_SCustNo", 8, 8, "", "", "onkeyup=\"screenCurrent.selKunde.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"screenCurrent.selKunde.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "ZIP"), "_SZIP", 8, 8, "", "", "onkeyup=\"screenCurrent.selKunde.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Contact"), "_SName", 24, 32, "", "", "onkeyup=\"screenCurrent.selKunde.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Phone/FAX"), "_SPhone", 24, 32, "", "", "onkeyup=\"screenCurrent.selKunde.cache( event, true) ;\"") ;
					?></table>
			</form>
		</div>
		<div id="depdata">
			<div id="KundeKontaktSurveyRoot">
				<?php tableBlock( "itemViews['selKundeKontaktDTV']", "formselKundeKontaktTop") ;		?>
				<table id="TableselKundeKontaktSurvey" wapClass="Kunde" wapSelKey="Id" width="100%">
					<thead>
						<tr wapType="header">
							<th width="50" wapAttribute="Id">Id</th>
							<th width="50" wapAttribute="KundeNr">Kunde Nr.</th>
							<th width="150" wapAttribute="FirmaName1">Firma</th>
							<th width="50" wapAttribute="PLZ">PLZ</th>
							<th width="150" wapAttribute="Name">Name</th>
 							<th colspan="1" wapFunctions="select">Functions</th>
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
