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
			<form name="formselArtGrFilter" id="formselArtGrFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Article group no."), "_SArtGrNo", 8, 8, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Name"), "_SArtGrName", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					?></table>
				</button>
			</form>
			<button data-dojo-type="dijit/form/Button"
				onclick="screenCurrent.selArtGr.show( '', -1, '') ; ">
				<?php echo FTr::tr( "Refresh") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="ArtGrSurveyRoot">
				<?php tableBlock( "itemViews['selArtGrDTV']", "formselArtGrTop") ;		?>
				<table id="TableselArtGrSurvey" eissClass="ArtGr" eissSelKey="ArtGrNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id"><?php echo FTr::tr( "Id") ; ?></th>
							<th eissAttribute="ArtGrNo"><?php echo FTr::tr( "Product group no.")?></th>
							<th eissAttribute="ArtGrName"><?php echo FTr::tr( "Name") ; ?></th>
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
