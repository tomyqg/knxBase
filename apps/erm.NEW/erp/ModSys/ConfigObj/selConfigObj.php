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
			<form name="formselConfigObjFilter" id="formselConfigObjFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Class"), "_SClass", 24, 32, "", "",
								"onkeyup=\"screenCurrent.selConfigObj.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Block"), "_SBlock", 24, 32, "", "",
								"onkeyup=\"screenCurrent.selConfigObj.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Prameter"), "_SParameter", 24, 32, "", "",
								"onkeyup=\"screenCurrent.selConfigObj.cache( event, true) ;\"") ;
					?></table>
			</form>
		</div>
		<div id="depdata">
			<div id="ConfigObjSurveyRoot">
				<?php tableBlock( "itemViews['selConfigObjDTV']", "formselConfigObjTop") ;		?>
				<table id="TableselConfigObjSurvey" eissClass="ConfigObj" eissSelKey="Id" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="Class"><?php echo FTr::tr( "Class") ; ?></th>
							<th eissAttribute="Block"><?php echo FTr::tr( "Block") ; ?></th>
							<th eissAttribute="Parameter"><?php echo FTr::tr( "Parameter") ; ?></th>
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
