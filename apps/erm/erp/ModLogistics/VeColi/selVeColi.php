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
			<form name="formselVeColiFilter" id="formselVeColiFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Coli no."), "_SVeColiNr", 8, 8, "", "") ;
					rowEdit( FTr::tr( "Ref. no."), "_SRefNr", 8, 8, "", "") ;
					rowEdit( FTr::tr( "Company"), "_SFirma", 8, 8, "", "") ;
					rowEdit( FTr::tr( "Status"), "_SStatus", 8, 8, "", "") ;
					?></table>
				<button data-dojo-type="dijit/form/Button" 
						onclick="itemViews['selVeColiDTV'].clear( 'formselVeColiFilter') ;">
					<?php echo FTr::tr( "Refresh") ; ?>
				</button>
			</form>
		</div>
		<div id="depdata">
			<div id="VeColiSurveyRoot">
				<?php tableBlock( "itemViews['selVeColiDTV']", "formselVeColiTop") ;		?>
				<table id="TableselVeColiSurvey" eissClass="VeColi" eissSelKey="VeColiNr" width="100%">
					<thead>
						<tr eissType="header">
							<th width="50" eissAttribute="Id"><?php echo FTr::tr( "Id") ; ?></th>
							<th width="50" eissAttribute="VeColiNr"><?php echo FTr::tr( "Coli no.") ; ?></th>
							<th width="150" eissAttribute="RefNr"><?php echo FTr::tr( "Reference no.") ; ?></th>
							<th width="50" eissAttribute="Datum"><?php echo FTr::tr( "Date") ; ?></th>
							<th width="150" eissAttribute="Status"><?php echo FTr::tr( "Status") ; ?></th>
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
