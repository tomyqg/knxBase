<?php
/**
 * SysScreen.php - Definition der Basis Klasses für Liefn Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
$lineEnd	=	"\n" ;
/**
 * 
 */
$myScr	=	new UI_Screen( "screenScreen") ;
$myMod	=	new UI_Module( $myScr->ModuleName) ;
$modName	=	$myMod->ModuleName ;
$scrName	=	$myScr->ScreenName ;
$myTab	=	new UI_ScreenTab() ;
$mySubTab	=	new UI_ScreenTab() ;
$myFormToTab	=	new UI_FormToTab() ;
$myForm	=	new UI_ScreenForm() ;
$myDTVToTab	=	new UI_DTVToTab() ;
$myDTV	=	new UI_ScreenDTV() ;
$myField	=	new UI_FormField() ;
$myDTVField	=	new UI_DTVField() ;
if ( $myMod->isValid() && $myScr->isValid()) {
	$scMain	=	"SC" . $myScr->ScreenName . "Main" ;
	$cpKey	=	"CP" . $myScr->ScreenName . "Key" ;
	$cpData	=	"CP" . $myScr->ScreenName . "Data" ;
	$formKey	=	"form" . $myScr->ScreenName . "Key" ;
	$selector	=	"screen" . $myScr->ScreenName . ".selector" ;
	?>
<div id="<?php echo $scMain ; ?>" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="<?php echo $cpKey ; ?>" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="<?php echo $formKey ; ?>" id="<?php echo $formKey ; ?>" onsubmit="return false ;">  
					<table>
						<tr>
							<th><?php echo FTr::tr( $myScr->MainObjKey) ; ?>:&nbsp;</th>
							<td class="space"><input type="image" src="/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" /></td>
							<td>
								<input type="text" size="10" maxlength="8" name="_I<?php echo $myScr->MainObjKey ; ?>" id="MainInputField" value="" onkeypress="return enterKey( event) ;"/>
							</td>
							<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" /></td>
							<td>
								<input type="button" value="Select ..." border="0" onclick="<?php $selector ; ?>.show( '', -1, '') ;"/> 
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Name") . ":" ; ?></th>
							<td colspan="4">
								<input type="text" name="_DFirmaName1" id="VOID" size="35" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="<?php echo $cpData ; ?>" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="tc<?php echo $myScr->ScreenName . "Data" ; ?>" data-dojo-type="dijit/layout/TabContainer" style="">
<?php
	$myTab->setIterCond( "ParentScreenName = '" . $myScr->ScreenName . "' ") ;
	$myTab->setIterOrder( "ORDER BY SeqNo ASC ") ;
	foreach ( $myTab as $key => $tab) {
		$tabName	=	$tab->TabName ;
		error_log( "Working on tab: $tabName") ;
?>
			<div id="cp<?php echo $tabName . "Data" ; ?>" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( $tabName) ; ?>">
				<div id="content">
					<div id="maindata">
<?php
		$myFormToTab->setIterCond( "TabName = '" . $tabName . "' ") ;
		$myFormToTab->setIterOrder( "ORDER BY SeqNo ") ;
		foreach( $myFormToTab as $keyForm => $formToTab) {
			error_log( "Working on relation: $formToTab->TabName -> $formToTab->FormName") ;
			$myForm->setFormName( $myFormToTab->FormName) ;
?>
						<form enctype="multipart/form-data" name="form<?php echo $modName."_".$scrName."_".$tabName ; ?>" id="form<?php echo $modName."_".$scrName."_".$tabName ; ?>" onsubmit="return false ;">  
							<table>
<?php
			$myField->setIterCond( "FormName = '" . $myFormToTab->FormName . "' ") ;
			$myField->setIterOrder( "ORDER BY SeqNo ASC ") ;
			foreach ( $myField as $key => $field) {
				error_log( $field->SeqNo . " --> " . $field->FieldName) ;
				rowEdit( $field->FieldName, "_I".$field->DataItemName, 32, 64, "", "") ;
			}
?>
							</table>
						</form>
<?php
		}
?>
					</div>
					<div id="depdata">
<?php
		$myDTVToTab->setIterCond( "TabName = '" . $tabName . "' ") ;
		$myDTVToTab->setIterOrder( "ORDER BY SeqNo ") ;
		foreach( $myDTVToTab as $keyForm => $dtvToTab) {
			error_log( "Working on relation: $dtvToTab->TabName -> $dtvToTab->DTVName") ;
			$myDTV->setDTVName( $myDTVToTab->DTVName) ;
?>
						<table>
<?php
			$myDTVField->setIterCond( "DTVName = '" . $myDTVToTab->DTVName . "' ") ;
			$myDTVField->setIterOrder( "ORDER BY SeqNo ASC ") ;
			foreach ( $myDTVField as $key => $field) {
				error_log( $field->SeqNo . " --> " . $field->FieldName) ;
			}
?>
						</table>
<?php
		}
?>
					</div>
				</div>
			</div>
<?php
	}
?>
		</div>
	</div>
</div>
<?php
} else {
	echo "can't load UI_Screen data from database" ;
}
?>