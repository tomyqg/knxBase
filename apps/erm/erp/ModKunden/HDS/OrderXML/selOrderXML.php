<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<html>
<head>
<link rel="stylesheet" type="text/css" media="screen" href="/styles/v1<?php echo $_SESSION['UserId'] ; ?>.css" title="Version 1 <?php echo $_SESSION['UserId'] ; ?>" />
<link rel="alternate stylesheet" type="text/css" media="screen" href="/styles/v1alt.css" title="Version 1 Alt" />
<link rel="alternate stylesheet" type="text/css" media="screen" href="/styles/v1.css" title="Version 1" />
</head>
<body>
	<div id="content">
		<div id="maindata">
			<form name="formselKundeFilter" id="formselKundeFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Customer no."), "_SCustNo", 8, 8, "", "", "onkeyup=\"screenCurrent.select.cache( event, true, 3, this) ;\"") ;
					rowEdit( FTr::tr( "Company"), "_SCompany", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true, 3, this) ;\"") ;
					rowEdit( FTr::tr( "ZIP"), "_SZIP", 8, 8, "", "", "onkeyup=\"screenCurrent.select.cache( event, true, 3, this) ;\"") ;
					rowEdit( FTr::tr( "Contact"), "_SName", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true, 3, this) ;\"") ;
					rowEdit( FTr::tr( "eMail"), "_SeMail", 24, 64, "", "", "onkeyup=\"screenCurrent.select.cache( event, true, 3, this) ;\"") ;
					rowEdit( FTr::tr( "Phone/FAX"), "_SPhone", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true, 3, this) ;\"") ;
					?></table>
			</form>
		</div>
		<div id="depdata">
			<div id="KundeSurveyRoot">
				<?php tableBlock( "itemViews['selKundeDTV']", "formselKundeTop") ; ?>
				<table id="TableselKundeSurvey" eissClass="Kunde" eissSelKey="KundeNr" width="100%">
					<thead>
						<tr eissType="header">
							<th width="50" eissAttribute="Id"><?php echo FTr::tr( "Id") ; ?></th>
							<th width="50" eissAttribute="KundeNr"><?php echo FTr::tr( "Customer no.") ; ?></th>
							<th width="150" eissAttribute="Firma"><?php echo FTr::tr( "Company") ; ?></th>
							<th width="50" eissAttribute="PLZ"><?php echo FTr::tr( "ZIP") ; ?></th>
							<th width="150" eissAttribute="Name"><?php echo FTr::tr( "Name") ; ?></th>
 							<th width="150" eissAttribute="eMail"><?php echo FTr::tr( "eMail") ; ?></th>
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
