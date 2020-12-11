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
			<form name="formselCustomerCommissionFilter" id="formselCustomerCommissionFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Offer no."), "_SCustomerCommissionNo", 8, 8, "", "", "onkeyup=\"screenCurrent.selCustomerCommission.cache( event, true) ;\"") ;
					rowOption( FTr::tr( "Status"), "_SStatus", CustomerCommission::getRStatus(), "-1", "") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"screenCurrent.selCustomerCommission.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "PLZ"), "_SZIP", 24, 32, "", "", "onkeyup=\"screenCurrent.selCustomerCommission.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Contact"), "_SContact", 24, 32, "", "", "onkeyup=\"screenCurrent.selCustomerCommission.cache( event, true) ;\"") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button"
					onclick="screenCurrent.selCustomerCommission.clear( 'formselCustomerCommissionFilter') ; ">
				<?php echo FTr::tr( "Clear filter") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="CustomerCommissionSurveyRoot">
				<?php tableBlock( "itemViews['selCustomerCommissionDTV']", "formselCustomerCommissionTop") ;		?>
				<table id="TableselCustomerCommissionSurvey" eissClass="CustomerCommission" eissSelKey="CustomerCommissionNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="CustomerCommissionNo">CustomerCommissionNo</th>
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
