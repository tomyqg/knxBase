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
			<form name="formselJournalFilter" id="formselJournalFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Journal no."), "_SJournalNo", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Description"), "_SDescription", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					?></table>
			</form>
		</div>
		<div id="depdata">
			<div id="JournalSurveyRoot">
				<?php tableBlock( "itemViews['selJournalDTV']", "formselJournalTop") ;		?>
				<table id="TableselJournalSurvey" eissClass="Journal" eissSelKey="JournalNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="JournalNo"><?php echo FTr::tr( "Journal no.") ; ?></th>
							<th eissAttribute="Description"><?php echo FTr::tr( "Description") ; ?></th>
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
