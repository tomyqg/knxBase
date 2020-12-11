<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<html>
<head>
</head>
<body>
	<div id="content">
		<div id="maindata">
			<form name="formselPCuOrdrFilter" id="formselPCuOrdrFilter">
				<table><?php
					rowEdit( FTr::tr( "Order no."), "_SPCuOrdrNo", 8, 8, "", "", "onkeyup=\"screenCurrent.selPCuOrdr.cache( event, true) ;\"") ;
					rowOption( FTr::tr( "Status"), "_SStatus", PCuOrdr::getRStatus(), "", "") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"screenCurrent.selPCuOrdr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "PLZ"), "_SZIP", 24, 32, "", "", "onkeyup=\"screenCurrent.selPCuOrdr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Contact"), "_SContact", 24, 32, "", "", "onkeyup=\"screenCurrent.selPCuOrdr.cache( event, true) ;\"") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" 
					onclick="itemViews['selPCuOrdrDTV'].clear( 'formselPCuOrdrFilter') ; ">
				<?php echo FTr::tr( "Clear filter") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="PCuOrdrSurveyRoot">
				<?php tableBlock( "itemViews['selPCuOrdrDTV']", "formselPCuOrdrTop") ;		?>
				<table id="TableselPCuOrdrSurvey" eissClass="PCuOrdr" eissSelKey="PCuOrdrNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="PCuOrdrNo">PCuOrdrNo</th>
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
