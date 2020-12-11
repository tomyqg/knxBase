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
			<form name="formselCuDlvrFilter" id="formselCuDlvrFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Offer no."), "_SCuDlvrNo", 8, 8, "", "", "onkeyup=\"screenCurrent.selCuDlvr.cache( event, true) ;\"") ;
					rowOption( FTr::tr( "Status"), "_SStatus", CuDlvr::getRStatus(), "-1", "") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"screenCurrent.selCuDlvr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "PLZ"), "_SZIP", 24, 32, "", "", "onkeyup=\"screenCurrent.selCuDlvr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Contact"), "_SContact", 24, 32, "", "", "onkeyup=\"screenCurrent.selCuDlvr.cache( event, true) ;\"") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" 
					onclick="screenCurrent.selCuDlvr.clear( 'formselCuDlvrFilter') ; ">
				<?php echo FTr::tr( "Clear filter") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="CuDlvrSurveyRoot">
				<?php tableBlock( "itemViews['selCuDlvrDTV']", "formselCuDlvrTop") ;		?>
				<table id="TableselCuDlvrSurvey" eissClass="CuDlvr" eissSelKey="CuDlvrNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="CuDlvrNo">CuDlvrNo</th>
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
	</div>
</body>
</html>
