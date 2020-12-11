<?php
/**
 * core.php - Definition der Basis Klasses für Liefn Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
if ( isset( $_GET["screenName"])) {
	$screenName	=	$_GET["screenName"] ;
} else {
	echo "\$screen not defined!!!" ;
}
error_log( "core.php: Getting screen name: [" . $screenName . "]") ;
/**
 * define the line-ending; should be set to "" in production version, makes the codes less readable
 * Enter description here ...
 * @var string
 */
$lineEnd	=	"\n" ;
/**
 * 
 */
$myScr	=	new UI_Screen( $screenName) ;
if ( $myScr->isValid()) {
	$myScreenName	=	$myScr->ScreenName ;
	$myClassName	=	$myScr->MainObj ;
	$myClassKey		=	$myScr->MainObjKey ;
	error_log( "core.php: Getting module name: [" . $myScr->ModuleName . "]") ;
	$myMod	=	new UI_Module( $myScr->ModuleName) ;
	$myModuleName	=	$myMod->ModuleName ;
	/**
	 * construct some names for convenient usage
	 */
	$mySelName	=	$myScreenName . ".sel" . $myClassName ;
	error_log( "core.php: \$mySelName = '$mySelName'") ;
	/**
	 * create some obejct instances for the iterations
	 */
	$myTab	=	new UI_Tab() ;
	$mySubTab	=	new UI_Tab() ;
	$myFormToTab	=	new UI_FormToTab() ;
	$myForm	=	new UI_Form() ;
	$myDTVToTab	=	new UI_DTVToTab() ;
	$myDTV	=	new UI_DTV() ;
	$myField	=	new UI_FormField() ;
	$myDTVField	=	new UI_DTVField() ;
	if ( $myMod->isValid()) {
		$scMain	=	"SC" . $myScr->ScreenName . "Main" ;
		$cpKey	=	"CP" . $myScr->ScreenName . "Key" ;
		$cpData	=	"CP" . $myScr->ScreenName . "Data" ;
		$formKey	=	"form" . $myScr->ScreenName . "Key" ;
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
								<input type="text" size="32" maxlength="64" name="_I<?php echo $myScr->MainObjKey ; ?>" id="MainInputField" value="" onkeypress="return enterKey( event) ;"/>
							</td>
							<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" /></td>
							<td>
								<input type="button" value="Select ..." border="0"
									onclick="<?php echo $mySelName ; ?>.show( '', -1, '') ;"/> 
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Name") . ":" ; ?></th>
							<td colspan="4">
								<input type="text" name="_DScreenName" id="VOID" size="35" value="" />
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
		//
		// show all the tabs for this screen
		//
		$myTab->setIterCond( "ParentScreenName = '" . $myScr->ScreenName . "' ") ;
		$myTab->setIterOrder( "ORDER BY SeqNo ASC ") ;
		foreach ( $myTab as $key => $tab) {
			$tabName	=	$tab->TabName ;
//			error_log( "Working on tab: $tabName") ;
?>
			<div id="cp<?php echo $tabName . "Data" ; ?>" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( $tab->Label) ; ?>">
				<div id="content">
					<div id="maindata">
<?php
			//
			// show all the forms for this tab
			// HTML form names are setup as ModuleName_ScreenName_TabName to make'm unique!
			//
			$myFormToTab->setIterCond( "TabName = '" . $tabName . "' ") ;
			$myFormToTab->setIterOrder( "ORDER BY SeqNo ") ;
			foreach( $myFormToTab as $keyForm => $formToTab) {
//				error_log( "Working on relation: $formToTab->TabName -> $formToTab->FormName") ;
				$myForm->setFormName( $myFormToTab->FormName) ;
				$uniqueFormName	=	"form".$myModuleName."_".$myScreenName."_".$tabName ;
?>
						<form enctype="multipart/form-data" name="<?php echo $uniqueFormName ; ?>" id="<?php echo $uniqueFormName ; ?>" onsubmit="return false ;">  
							<table>
<?php
				//
				// show all the fields for this tab
				$myField->setIterCond( "FormName = '" . $myFormToTab->FormName . "' ") ;
				$myField->setIterOrder( "ORDER BY SeqNo ASC ") ;
				$myField->setIterJoin( "LEFT JOIN UI_Dict AS UID ON UID.DataItemType = C.DataItemType ", "UID.Type, UID.Length AS MaxLength ") ;
				foreach ( $myField as $key => $field) {
//					error_log( $field->SeqNo . " --> " . $field->FieldName) ;
					switch ( $field->DataItemType) {
						case	"flag"	:
							rowFlag( FTr::tr( $field->Label), "_I".$field->DataItemName, Opt::getArray( "Options", "Key", "Value", "OptionName = '$field->DataItemName'"), "", FTr::tr( "INFO-ACTUAL")) ;
							break ;
						case	"option"	:
							rowOption( FTr::tr( $field->Label), "_I".$field->DataItemName, Opt::getArray( "Options", "Key", "Value", "OptionName = '$field->DataItemName'"), "", FTr::tr( "INFO-ACTUAL")) ;
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
				if ( strpos( $myForm->Functions, "add") !== false) {
?>
						<button type="button" data-dojo-type="dijit/form/Button" tabindex="14" border="0" onclick="dispatchForm( 'xml', true, 'add', '<?php echo $uniqueFormName ; ?>') ; return false ;">
							<?php echo FTr::tr( "Add with this data") ; ?>
						</button>
<?php
				}
				if ( strpos( $myForm->Functions, "addEmpty") !== false) {
?>
						<button type="button" data-dojo-type="dijit/form/Button" tabindex="14" border="0" onclick="dispatchForm( 'xml', true, 'addEmpty', '<?php echo $uniqueFormName ; ?>') ; return false ;">
							<?php echo FTr::tr( "Add empty") ; ?>
						</button>
<?php
				}
				if ( strpos( $myForm->Functions, "upd") !== false) {
?>
						<button type="button" data-dojo-type="dijit/form/Button" tabindex="14" border="0" onclick="dispatchForm( 'xml', true, 'upd', '<?php echo $uniqueFormName ; ?>') ; return false ;">
							<?php echo FTr::tr( "Update") ; ?>
						</button>
<?php
				}
				if ( strpos( $myForm->Functions, "del") !== false) {
?>
						<button type="button" data-dojo-type="dijit/form/Button" tabindex="14" border="0" onclick="dispatchForm( 'xml', true, 'del', '<?php echo $uniqueFormName ; ?>') ; return false ;">
							<?php echo FTr::tr( "Delete") ; ?>
						</button>
<?php
				}
			}
?>
					</div>
					<div id="depdata">
<?php
			//
			// show all the datableviews for this tab
			//
			$myDTVToTab->setIterCond( "TabName = '" . $tabName . "' ") ;
			$myDTVToTab->setIterOrder( "ORDER BY SeqNo ") ;
			foreach( $myDTVToTab as $keyForm => $dtvToTab) {
//				error_log( "Working on relation: $dtvToTab->TabName -> $dtvToTab->DTVName") ;
				$myDTV->setDTVName( $myDTVToTab->DTVName) ;
				$dtvName	=	$myDTV->DTVName ;
				$edtName	=	"edt" . $myDTV->ObjectClass ;
				tableBlock( $myScr->ScreenName.".".$dtvName, "form".$dtvName."Top") ;
?>
						<button type="button" data-dojo-type="dijit/form/Button"
							onclick="itemEditors['<?php echo $edtName ; ?>'].edit( <?php echo $myScr->ScreenName ; ?>.keyField.value, '-1', '<?php echo $myDTV->ObjectClass ; ?>') ;">
							<?php echo FTr::tr( "New ...") ; ?>
						</button>
						<div id="root<?php echo $dtvName ; ?>">
							<table id="<?php echo $dtvName ; ?>" eissClass="<?php echo $myDTV->ObjectClass ; ?>">
								<thead>
									<tr eissType="header">
<?php
				//
				// establish the table
				//
				$myDTVField->setIterCond( "DTVName = '" . $myDTVToTab->DTVName . "' ") ;
				$myDTVField->setIterOrder( "ORDER BY SeqNo ASC ") ;
				foreach ( $myDTVField as $key => $field) {
//					error_log( $field->SeqNo . " --> " . $field->FieldName) ;
?>
										<th <?php 
											echo "eissAttribute=\"".$myDTVField->DataItemName."\"" ;
											if ( $myDTVField->LinkTo != "")
												echo "colspan = \"2\" eissLinkTo=\"".$myDTVField->LinkTo."\"" ;
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
				tableBlock( $myScr->ScreenName.".".$dtvName, "form".$dtvName."Bot") ; 
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
		echo "can't load UI_Module data from database" ;
	}
} else {
	echo "can't load UI_Screen data from database" ;
}
?>