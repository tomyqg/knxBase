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
			<form name="formselUI_ScreenFilter" id="formselUI_ScreenFilter">
				<table><?php
					rowEdit( FTr::tr( "Module name"), "_SModuleName", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Screen name"), "_SScreenName", 24, 32, "", "", "onkeyup=\"screenCurrent.select.cache( event, true) ;\"") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" 
					onclick="screenCurrent.select.show( '', -1, '') ; ">
				<?php echo FTr::tr( "Refresh") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="UI_ScreenSurveyRoot">
				<?php tableBlock( "itemViews['selUI_ScreenDTV']", "formselUI_ScreenTop") ;		?>
				<table id="TableselUI_ScreenSurvey" eissClass="UI_Screen" eissSelKey="Id" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="ModuleName">Module name</th>
							<th eissAttribute="ScreenName">Screen name</th>
							<th colspan="1" eissFunctions="select">Functions</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
<!-- 
 		<div id="maindata">
			<?php tableBlock( "itemViews['selUI_ScreenDTV']", "formselUI_ScreenBot") ;		?>
		</div>
 -->
	</div>
</body>
</html>
