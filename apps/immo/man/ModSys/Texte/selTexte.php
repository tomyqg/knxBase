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
			<form name="formselTexteFilter" id="formselTexteFilter" onsubmit="return false ;">
				<table><?php
					rowEdit( FTr::tr( "Name"), "_SName", 24, 32, "", "", "onkeyup=\"screenCurrent.selTexte.cache( event, true) ;\"") ;
					rowEdit( FTr::tr( "Text"), "_SText", 24, 32, "", "", "onkeyup=\"screenCurrent.selTexte.cache( event, true) ;\"") ;
					rowOption( FTr::tr( "Language"), "_SLanguage", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Language'"), "de_DE", "") ;
					rowFlag( FTr::tr( "Not translated only"), "_STranslated", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Flag'"), "", FTr::tr( "HELP")) ;
					?></table>
			</form>
		</div>
		<div id="depdata">
			<div id="TexteSurveyRoot">
				<?php tableBlock( "itemViews['selTexteDTV']", "formselTexteTop") ;		?>
				<table id="TableselTexteSurvey" eissClass="Texte" eissSelKey="Id" width="100%">
					<thead>
						<tr eissType="header">
							<th eissAttribute="Id">Id</th>
							<th eissAttribute="Name"><?php echo FTr::tr( "Name") ; ?></th>
							<th eissAttribute="Volltext"><?php echo FTr::tr( "Original text") ; ?></th>
							<th eissAttribute="Sprache"><?php echo FTr::tr( "Language") ; ?></th>
							<th eissAttribute="Volltext2"><?php echo FTr::tr( "Textelation") ; ?></th>
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
