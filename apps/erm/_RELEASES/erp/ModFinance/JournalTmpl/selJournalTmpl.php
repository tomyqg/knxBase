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
			<form name="formselJournalTmplFilter" id="formselJournalTmplFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Template no."), "_SJournalTmplNo", 8, 8, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Description"), "_SDescription", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					?></table>
			</form>
		</div>
		<div id="depdata">
			<div id="JournalTmplSurveyRoot">
				<?php tableBlock( "itemViews['selJournalTmplDTV']", "formselJournalTmplTop") ; ?>
				<table id="TableselJournalTmplSurvey" eissClass="JournalTmpl" eissSelKey="JournalTmplNo" width="100%">
					<thead>
						<tr eissType="header">
							<th width="50" eissAttribute="Id"><?php echo FTr::tr( "Id") ; ?></th>
							<th width="50" eissAttribute="JournalTmplNo"><?php echo FTr::tr( "Template no.") ; ?></th>
							<th width="150" eissAttribute="Description"><?php echo FTr::tr( "Description") ; ?></th>
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
