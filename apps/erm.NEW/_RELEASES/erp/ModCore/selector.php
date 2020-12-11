<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
error_log( "selector.php: begin") ;
if ( isset( $_GET["scrName"]))
	$myScreenName	=	$_GET["scrName"] ;
if ( isset( $_GET["selName"]))
	$mySelectorName	=	$_GET["selName"] ;
error_log( "Getting selector name: [" . $mySelectorName . "]") ;
$mySel	=	new UI_Selector() ;
$mySel->setSelectorName( $mySelectorName) ;
$myClassName	=	$mySel->MainClass ;
$myFormName	=	$mySel->FormName ;
$myFormField	=	new UI_FormField() ;
$myDTVName	=	$mySel->DTVName ;
$myDTV	=	new UI_DTV() ;
$myDTV->setDTVName( $myDTVName) ;
$myDTVField	=	new UI_DTVField() ;
$mySelName	=	$myScreenName . ".sel" . $mySel->MainClass ;
?>
<html>
<head></head>
<body>
<div id="content">
	<div id="maindata">
		<table>
			<form enctype="multipart/form-data" name="form<?php echo $mySelectorName ; ?>" id="form<?php echo $mySelectorName ; ?>" onsubmit="return false ;">  
				<table>
<?php
$myFormField->setIterCond( "FormName = '" . $myFormName . "' ") ;
$myFormField->setIterOrder( "ORDER BY SeqNo ASC ") ;
$myFormField->setIterJoin( "LEFT JOIN UI_Dict AS UID ON UID.DataItemType = C.DataItemType ", "UID.Type, UID.Length AS MaxLength ") ;
foreach ( $myFormField as $key => $field) {
	error_log( $field->SeqNo . " --> " . $field->FieldName) ;
	switch ( $field->DataItemType) {
		case	"flag"	:
			rowFlag( FTr::tr( $field->Label), "_S".$field->DataItemName, Opt::getArray( "Options", "Key", "Value", "OptionName = '$field->DataItemName'"), "", FTr::tr( "INFO-ACTUAL")) ;
			break ;
		case	"option"	:
			rowOption( FTr::tr( $field->Label), "_S".$field->DataItemName, Opt::getArray( "Options", "Key", "Value", "OptionName = '$field->DataItemName'"), "", FTr::tr( "INFO-ACTUAL")) ;
			break ;
		default	:
			rowEdit( FTr::tr( $field->Label), "_S".$field->DataItemName, $field->DisplayLength, $field->MaxLength, "", "") ;
			break ;
	}
}
?>
			</table>
		</form>
	</div>
	<div class="depdata">
<?php
tableBlock( $mySelName.".dtv", $myFormName."Top") ;
?>
						<div id="root<?php echo $myDTVName ; ?>">
							<table id="Table<?php echo $myClassName ; ?>Survey" eissClass="<?php echo $myClassName ; ?>" eissSelKey="<?php echo $mySel->MainClassKey ; ?>">
								<thead>
									<tr eissType="header">
<?php
/**
 * establish the table
 */
$myDTVField->setIterCond( "DTVName = '" . $myDTVName . "' ") ;
$myDTVField->setIterOrder( "ORDER BY SeqNo ASC ") ;
foreach ( $myDTVField as $key => $field) {
	error_log( $field->SeqNo . " --> " . $field->FieldName) ;
?>
										<th <?php 
											echo "eissAttribute=\"".$field->DataItemName."\"" ;
											?>><?php echo $myDTVField->Label ; ?>
										</th>
<?php
}
?>
 									<th eissFunctions="<?php echo $myDTV->Functions ; ?>">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
<?php
tableBlock( $mySelName.".dtv", $myFormName."Bot") ;
?>
	</div>
</div>
<?php
error_log( "selector.php: end") ;
?>