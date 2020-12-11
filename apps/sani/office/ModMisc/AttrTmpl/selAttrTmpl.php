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
			<form name="formselAttrTmplFilter" id="formselAttrTmplFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Offer no."), "_SAttrTmplNo", 8, 8, "", "", "onkeyup=\"screenCurrent.selAttrTmpl.cache( event, true) ;\"") ;
					?></table>
			</form>
			<button data-dojo-type="dijit/form/Button" 
					onclick="screenCurrent.selAttrTmpl.clear( 'formselAttrTmplFilter') ; ">
				<?php echo FTr::tr( "Clear filter") ; ?>
			</button>
		</div>
		<div id="depdata">
			<div id="AttrTmplSurveyRoot">
				<?php tableBlock( "itemViews['selAttrTmplDTV']", "formselAttrTmplTop") ;		?>
				<table id="TableselAttrTmplSurvey" eissClass="AttrTmpl" eissSelKey="AttrTmplNo" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="AttrTmplNo"><?php echo FTr::tr( "Template No.") ; ?></th>
							<th eissAttribute="Keywords"><?php echo FTr::tr( "Keywords") ; ?></th>
 							<th colspan="1" eissFunctions="select">Functions</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	</div>
</body>
</html>
