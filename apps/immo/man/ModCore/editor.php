<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
error_log( "editor.php: begin") ;
if ( isset( $_GET["edtName"]))
	$edtName	=	$_GET["edtName"] ;
error_log( "Getting editor name: [" . $edtName . "]") ;
$myEdt	=	new UI_Editor() ;
$myEdt->setEditorName( $edtName) ;
$formName	=	$myEdt->FormName ;
$myField	=	new UI_FormField() ;
?>
<html>
<head></head>
<body>
<div id="content">
	<div id="maindata">
		<form name="editorObject" enctype="multipart/form-data""> 
			<table><?php
?>
						<form enctype="multipart/form-data" name="form<?php echo $edtName ; ?>" id="form<?php echo $edtName ; ?>" onsubmit="return false ;">  
							<table>
							<?php
			rowDisplay( FTr::tr( "Id"), "_HId", 12, 128, "", "") ;
			rowDisplay( FTr::tr( "Key"), "_HKey", 12, 128, "", "") ;
			$myField->setIterCond( "FormName = '" . $formName . "' ") ;
			$myField->setIterOrder( "ORDER BY SeqNo ASC ") ;
			$myField->setIterJoin( "LEFT JOIN UI_Dict AS UID ON UID.DataItemType = C.DataItemType ", "UID.Type, UID.Length AS MaxLength ") ;
			foreach ( $myField as $key => $field) {
				error_log( $field->SeqNo . " --> " . $field->FieldName) ;
				switch ( $field->DataItemType) {
					case	"date"	:
						rowDate( FTr::tr( $field->Label), "_I".$field->DataItemName, $field->DisplayLength, $field->MaxLength, "", "") ;
						break ;
					case	"flag"	:
						rowFlag( FTr::tr( $field->Label), "_F".$field->DataItemName, Opt::getArray( "Options", "Key", "Value", "OptionName = '$field->OptionName'"), "", FTr::tr( "INFO-ACTUAL")) ;
						break ;
					case	"option"	:
						rowOption( FTr::tr( $field->Label), "_I".$field->DataItemName, Opt::getArray( "Options", "Key", "Value", "OptionName = '$field->OptionName'"), "", FTr::tr( "INFO-ACTUAL")) ;
						break ;
					case	"float"	:
					case	"double"	:
						rowEdit( FTr::tr( $field->Label), "_F".$field->DataItemName, $field->DisplayLength, $field->MaxLength, "", "") ;
						break ;
					default	:
						rowEdit( FTr::tr( $field->Label), "_I".$field->DataItemName, $field->DisplayLength, $field->MaxLength, "", "") ;
						break ;
				}
			}
?>
							</table>
						</form>
<?php
			?></table>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionCreateObject" onclick="objEditorNS.add( '<?php echo $edtName ; ?>') ;" />
				<?php echo FTr::tr( "Create") ; ?>
			</button>
			<button type="submit" name="actionUpdateObject" onclick="objEditorNS.upd( '<?php echo $edtName ; ?>') ;" />
				<?php echo FTr::tr( "Update") ; ?>
			</button>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionDeleteObject" onclick="objEditorNS.cancel( '<?php echo $edtName ; ?>') ;" />
				<?php echo FTr::tr( "Cancel") ; ?>
			</button>
		</form> 
	</div>
</div>
<?php
error_log( "editor.php: end") ;
?>