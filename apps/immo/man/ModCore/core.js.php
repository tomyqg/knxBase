/**
 * 
 */
<?php 
error_log( "core.js.php: getting screen name: [" . $screenName . "]") ;
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$lineEnd	=	"\n" ;
$myScr	=	new UI_Screen( $screenName) ;
$myMainObj	=	$myScr->MainObj ;
$myMod	=	new UI_Module( $myScr->ModuleName) ;
$modName	=	$myMod->ModuleName ;
$myMainTab	=	new UI_Tab() ;
$mySubTab	=	new UI_Tab() ;
$myField	=	new UI_FormField() ;
$myDTVToTab	=	new UI_DTVToTab() ;
$myDTV		=	new UI_DTV() ;
$myFormToTab	=	new UI_FormToTab() ;
$myForm		=	new UI_Form() ;
$myEditor	=	new UI_Editor() ;
if ( $myScr) {
	$myScreenName	=	$myScr->ScreenName ;
	$myClassName	=	$myScr->MainObj ;
	$myClassKey		=	$myScr->MainObjKey ;
	$scMain	=	"SC" . $myScreenName . "Main" ;
	$cpKey	=	"CP" . $myScreenName . "Key" ;
	$cpData	=	"CP" . $myScreenName . "Data" ;
	$formKey	=	"form" . $myScreenName . "Key" ;
	$mySelName	=	$myScreenName . ".sel" . $myClassName ;
	
?>
function	regMod<?php echo $myScreenName ; ?>() {
	_debugL( 0x00000001, "regMod<?php echo $myScreenName ; ?>: begin\n") ;
	<?php echo $myScreenName ; ?>	=	screenAdd( "<?php echo $myScreenName ; ?>", link<?php echo $myScreenName ; ?>, "<?php echo $myScr->MainObj ; ?>", "<?php echo $myScreenName ; ?>KeyData", "_I<?php echo $myScr->MainObjKey ; ?>", show<?php echo $myScreenName ; ?>All, null) ;
	<?php echo $myScreenName ; ?>.fncNew	=	new<?php echo $myScreenName ; ?> ;
	<?php echo $myScreenName ; ?>.package	=	"ModBase" ;
	<?php echo $myScreenName ; ?>.module	=	"<?php echo $myScreenName ; ?>" ;
	<?php echo $myScreenName ; ?>.coreObject	=	"<?php echo $myScr->MainObj ; ?>" ;
	<?php echo $myScreenName ; ?>.showFunc	=	show<?php echo $myScreenName ; ?>All ;
	<?php echo $myScreenName ; ?>.keyField	=	getFormField( "<?php echo $formKey ; ?>", "_I<?php echo $myScr->MainObjKey ; ?>") ;
	<?php echo $myScreenName ; ?>.delConfDialog	=	"<?php echo $modName."/".$myScreenName."/conf".$myMainObj."Del.php" ; ?>" ;
	/**
	 * create the editors
	 */
<?php
//	$myEditor->setIterCond( "ScreenName = '" . $myScreenName . "' ") ;
//	foreach ( $myEditor as $key => $edt) {
//		$edtName	=	$edt->EditorName ;
//		echo $myScreenName.".".$edtName." = new objEditor( ".$myScreenName.".$edtName, \"ModCore\", \"/ModCore/editor.php?editorName=".$edtName."&ownRef=".$myScreenName.".".$edtName."\", \"".$edt->MainObj."\", null, null) ;" ;
//	}
?>
	/**
	 * create the dataTableViews
	 */
<?php
	$myMainTab->setIterCond( "ParentScreenName = '" . $myScreenName . "' ") ;
	$myMainTab->setIterOrder( "ORDER BY SeqNo ASC ") ;
	foreach ( $myMainTab as $key => $tab) {
		$tabName	=	$tab->TabName ;
		$myDTVToTab->setIterCond( "TabName = '" . $tabName . "' ") ;
		$myDTVToTab->setIterOrder( "ORDER BY SeqNo ") ;
		foreach( $myDTVToTab as $keyForm => $dtvToTab) {
//			error_log( "Working on relation: $dtvToTab->TabName -> $dtvToTab->DTVName") ;
			$myDTV->setDTVName( $myDTVToTab->DTVName) ;
			$dtvName	=	$myDTV->DTVName ;
			echo $myScreenName.".".$myDTV->DTVName." = new dataTableView( \"".$myScreenName.".".$dtvName."\", "
						. "\"".$myDTV->DTVName."\", "
						. "\"".$myScr->MainObj."\", "
						. "\"".$myDTV->ObjectClass."\", null) ; ".$lineEnd ;
			echo $myScreenName.".".$myDTV->DTVName.".f1 = \"form".$myDTV->DTVName."Top\" ; ".$lineEnd ;
			echo $myScreenName.".".$myDTV->DTVName.".f2 = \"form".$myDTV->DTVName."Bot\" ; ".$lineEnd ;
			$neededEdtName	=	$myScreenName.".edt".$myDTV->ObjectClass ;
?>
	if ( <?php echo $neededEdtName ; ?>) {		// if the required editor exists ...
<?php
		echo $myScreenName.".".$myDTV->DTVName.".dataItemEditor = itemEditors['".$neededEdtName."'] ;".$lineEnd ;
?>
	}
<?php
		}
	}
?>
	/**
	 * create the selector
	 */
<?php
?>
	<?php echo $mySelName ; ?>	=	new selector( <?php echo $mySelName ; ?>, 'ModCore', '/ModCore/selector.php?scrName=<?php echo $myScreenName ; ?>&selName=sel<?php echo $myClassName ; ?>', '<?php echo $myScreenName ; ?>') ;
	<?php echo $mySelName ; ?>.action	=	"/Comon/hdlObject.php" ;
	<?php echo $mySelName ; ?>.filterForm	=	"formSel<?php echo $myClassName ; ?>Filter" ;
	<?php echo $mySelName ; ?>.onSelect	=	load<?php echo $myClassName ; ?>ById ;
	<?php echo $mySelName ; ?>.dtvAdd( "<?php echo $mySelName ; ?>.dtv", "Table<?php echo $myClassName ; ?>Survey", "<?php echo $myClassName ; ?>", "<?php echo $myClassName ; ?>", null) ;
	<?php echo $mySelName ; ?>.dtv.f1	=	"formSel<?php echo $myClassName ; ?>Top" ;
	<?php echo $mySelName ; ?>.dtv.f2	=	"formSel<?php echo $myClassName ; ?>Bot" ;
	<?php echo $mySelName ; ?>.dtv.filter	=	"formSel<?php echo $myClassName ; ?>Filter" ;
<?php ?>
	/**
	 * create the dataminers
	 */
	/**
	 *
	 */
	<?php echo $myScreenName ; ?>.link() ;
	/**
	 * process any pending data/request for this screen
	 */
	if ( pendingKey != "") {
		requestUni( 'ModBase', '<?php echo $myScr->MainObj ; ?>', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, show<?php echo $myScreenName ; ?>All) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	}
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	_debugL( 0x00000001, "regMod<?php echo $myScreenName ; ?>: end\n") ;
}
function	link<?php echo $myScreenName ; ?>() {
	_debugL( 0x00000001, "link<?php echo $myScreenName ; ?>: \n") ;
}
/**
 * 
 */
function	load<?php echo $myClassName ; ?>ById( _id) {
	_debugL( 0x00000001, "main<?php echo $myScreenName ; ?>.js::load<?php echo $myScreenName ; ?>ById(" + _id.toString() + "): begin\n") ;
	requestUni( 'ModSys', '<?php echo $myScreenName ; ?>', '/Common/hdlObject.php', 'getXMLComplete', _id, '', '', null, show<?php echo $myScreenName ; ?>All) ;
	_debugL( 0x00000001, "main<?php echo $myScreenName ; ?>.js::load<?php echo $myScreenName ; ?>ById(): end\n") ;
}
/**
 *
 */
function	show<?php echo $myScreenName ; ?>All( _response) {
	show<?php echo $myScreenName ; ?>( _response) ;
	// refresh the itemlist
	//
<?php
	$myMainTab->setIterCond( "ParentScreenName = '" . $myScreenName . "' ") ;
	$myMainTab->setIterOrder( "ORDER BY SeqNo ASC ") ;
	foreach ( $myMainTab as $key => $tab) {
		$tabName	=	$tab->TabName ;
		$myDTVToTab->setIterCond( "TabName = '" . $tabName . "' ") ;
		$myDTVToTab->setIterOrder( "ORDER BY SeqNo ") ;
		foreach( $myDTVToTab as $keyForm => $dtvToTab) {
//			error_log( "Working on relation: $dtvToTab->TabName -> $dtvToTab->DTVName") ;
			$myDTV->setDTVName( $myDTVToTab->DTVName) ;
			echo $myScreenName.".".$myDTV->DTVName.".primObjKey = ".$myScreenName.".keyField.value ;".$lineEnd ;
			echo $myScreenName.".".$myDTV->DTVName.".show( _response) ; " ;
		}
	}
?>
}
/**
 *
 */
function	show<?php echo $myScreenName ; ?>( _response) {
	var	attrs ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	myObj	=	_response.getElementsByTagName( "<?php echo $myScr->MainObj ; ?>")[0] ;
	if ( myObj) {
		attrs	=	myObj.childNodes ;
		dispAttrs( attrs, "<?php echo $formKey ; ?>") ;
	}
<?php
	$myMainTab->setIterCond( "ParentScreenName = '" . $myScreenName . "' ") ;
	$myMainTab->setIterOrder( "ORDER BY SeqNo ASC ") ;
	foreach ( $myMainTab as $key => $tab) {
		$tabName	=	$tab->TabName ;
		$myFormToTab->setIterCond( "TabName = '" . $tabName . "' ") ;
		$myFormToTab->setIterOrder( "ORDER BY SeqNo ASC ") ;
		foreach ( $myFormToTab as $key => $formToTab) {
			$tabName	=	$tab->TabName ;
			$uniqueFormName	=	"form".$modName."_".$myScreenName."_".$tabName ;
			echo "dispAttrs( attrs, \"".$uniqueFormName."\") ;".$lineEnd ;
		}
	}
?>
}
/**
 * 
 */
function	new<?php echo $myScreenName ; ?>() {
}
<?php
} else {
	
}
?>