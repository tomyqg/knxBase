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
			<form name="formselProdGrFilter" id="formselProdGrFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Product group no."), "_SProdGrNo", 8, 8, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Name"), "_SProdGrName", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					?></table>
				</button>
			</form>
			<button data-dojo-type="dijit/form/Button"
				onclick="screenCurrent.selProdGr.show( '', -1, '') ; ">
				<?php echo FTr::tr( "Refresh") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="ProdGrSurveyRoot">
				<?php tableBlock( "itemViews['selProdGrDTV']", "formselProdGrTop") ;		?>
				<table id="TableselProdGrSurvey" eissClass="ProdGr" eissSelKey="ProdGrNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id"><?php echo FTr::tr( "Id") ; ?></th>
							<th eissAttribute="ProdGrNo"><?php echo FTr::tr( "Product group no.")?></th>
							<th eissAttribute="ProdGrName"><?php echo FTr::tr( "Name") ; ?></th>
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
