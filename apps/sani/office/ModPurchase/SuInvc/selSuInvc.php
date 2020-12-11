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
			<form name="formselAdrFilter" id="formselAdrFilter">
				<table><?php
					rowEdit( FTr::tr( "Address no."), "_SAdrNr", 8, 8, "", "", "onkeyup=\"screenCurrent.selAdr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 24, "", "", "onkeyup=\"screenCurrent.selAdr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "ZIP"), "_SZIP", 8, 8, "", "", "onkeyup=\"screenCurrent.selAdr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Contact"), "_SName", 24, 32, "", "", "onkeyup=\"screenCurrent.selAdr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Phone/FAX"), "_SPhone", 24, 32, "", "", "onkeyup=\"screenCurrent.selAdr.cache( event, true) ;\"") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" 
					onclick="screenCurrent.selAdr.show( '', -1, '') ; ">
				<?php echo FTr::tr( "Refresh") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="AdrSurveyRoot">
				<?php tableBlock( "itemViews['selAdrDTV']", "formselAdrTop") ;		?>
				<table id="TableselAdrSurvey" eissClass="Adr" eissSelKey="AdrNr" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="AdrNr">AdrNr</th>
							<th eissAttribute="FirmaName1">Datum</th>
							<th eissAttribute="PLZ">PLZ</th>
							<th eissAttribute="Name">Name</th>
							<th colspan="1" eissFunctions="select">Functions</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
<!-- 
 		<div id="maindata">
			<?php tableBlock( "itemViews['selAdrDTV']", "formselAdrBot") ;		?>
		</div>
 -->
	</div>
</body>
</html>
