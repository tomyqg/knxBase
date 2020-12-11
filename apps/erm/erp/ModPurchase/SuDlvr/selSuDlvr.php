<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<html>
<head></head>
<body>
	<div id="content">
		<div id="maindata">
			<form name="formselSuDlvrFilter" id="formselSuDlvrFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Delivery no."), "_SSuDlvrNo", 8, 8, "", "", "onkeyup=\"screenCurrent.selSuDlvr.cache( event, true) ;\"") ;
					rowOption( FTr::tr( "Status"), "_SStatus", SuDlvr::getRStatus(), "", "", "onkeyup=\"screenCurrent.selSuDlvr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"screenCurrent.selSuDlvr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "PLZ"), "_SZIP", 24, 32, "", "", "onkeyup=\"screenCurrent.selSuDlvr.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Contact"), "_SName", 24, 32, "", "", "onkeyup=\"screenCurrent.selSuDlvr.cache( event, true) ;\"") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" 
					onclick="screenCurrent.selSuDlvr.clear( 'formselSuDlvrFilter') ;">
				<?php echo FTr::tr( "Clear filter") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="SuDlvrSurveyRoot">
				<?php tableBlock( "itemViews['selSuDlvrDTV']", "formselSuDlvrTop") ;		?>
				<table id="TableselSuDlvrSurvey" eissClass="SuDlvr" eissSelKey="SuDlvrNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="SuDlvrNo">SuDlvrNo</th>
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
