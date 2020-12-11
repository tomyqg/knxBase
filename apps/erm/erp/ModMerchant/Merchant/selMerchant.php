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
			<form name="formselMerchantFilter" id="formselMerchantFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Merchant id."), "_SMerchantId", 8, 8, "", "") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" 
					onclick="screenCurrent.selMerchant.clear( 'formselMerchantFilter') ;">
				<?php echo FTr::tr( "Clear filter") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="MerchantSurveyRoot">
				<?php tableBlock( "itemViews['selMerchantDTV']", "formselMerchantTop") ;		?>
				<table id="TableselMerchantSurvey" eissClass="Merchant" eissSelKey="MerchantId" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="MerchantId"><?php echo FTr::tr( "Merchant") ; ?></th>
							<th eissAttribute="Marketplace"><?php echo FTr::tr( "Marketplace") ; ?></th>
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
