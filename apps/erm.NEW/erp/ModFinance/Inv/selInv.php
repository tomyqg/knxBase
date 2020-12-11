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
			<form name="formselInvFilter" id="formselInvFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Criteria"), "_SInvNo", 8, 8, "", "") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" 
					onclick="screenCurrent.selInv.show( '', -1, '') ; ">
				<?php echo FTr::tr( "Refresh") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="InvSurveyRoot">
				<?php tableBlock( "itemViews['selInvDTV']", "formselInvTop") ;		?>
				<table id="TableselInvSurvey" eissClass="Inv" eissSelKey="InvNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="InvNo">InvNr</th>
							<th eissAttribute="Date">Datum</th>
							<th eissAttribute="KeyDate">PLZ</th>
							<th eissAttribute="Description">Name</th>
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
					