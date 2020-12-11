/**
 * 
 */
<?php 
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$myScr	=	new UI_Screen( "screenScreen") ;
$myMainObj	=	$myScr->MainObj ;
$myMod	=	new UI_Module( $myScr->ModuleName) ;
$modName	=	$myMod->ModuleName ;
$scrName	=	$myScr->ScreenName ;
$myMainTab	=	new UI_ScreenTab() ;
$mySubTab	=	new UI_ScreenTab() ;
$myField	=	new UI_FormField() ;
if ( $myScr) {
	$scMain	=	"SC" . $myScr->ScreenName . "Main" ;
	$cpKey	=	"CP" . $myScr->ScreenName . "Key" ;
	$cpData	=	"CP" . $myScr->ScreenName . "Data" ;
	$formKey	=	"form" . $myScr->ScreenName . "Key" ;
	$selector	=	"screen" . $myScr->ScreenName . ".selector" ;
?>
function	regMod<?php echo $myScr->ScreenName ; ?>() {
	_debugL( 0x00000001, "regMod<?php echo $myScr->ScreenName ; ?>: begin\n") ;
	myScr	=	screenAdd( "screen<?php echo $myScr->ScreenName ; ?>", link<?php echo $myScr->ScreenName ; ?>, "<?php echo $myScr->MainObj ; ?>", "<?php echo $myScr->ScreenName ; ?>KeyData", "_I<?php echo $myScr->ScreenName ; ?>Nr", show<?php echo $myScr->ScreenName ; ?>All, null) ;
	myScr.fncNew	=	new<?php echo $myScr->ScreenName ; ?> ;
	myScr.package	=	"ModBase" ;
	myScr.module	=	"<?php echo $myScr->ScreenName ; ?>" ;
	myScr.coreObject	=	"<?php echo $myScr->MainObj ; ?>" ;
	myScr.showFunc	=	show<?php echo $myScr->ScreenName ; ?>All ;
	myScr.keyField	=	getFormField( "<?php echo $formKey ; ?>", "_I<?php echo $myScr->MainObjKey ; ?>") ;
	myScr.delConfDialog	=	"<?php echo $modName."/".$scrName."/conf".$myMainObj."Del.php" ; ?>" ;
	// create the dataTableViews
	// create the editors
	// create the selector
	_debugL( 0x00000001, "main<?php echo $myScr->ScreenName ; ?>.js::regMod<?php echo $myScr->ScreenName ; ?>(): creating selector for <?php echo $myScr->ScreenName ; ?>\n") ;
	myScr.sel<?php echo $myScr->ScreenName ; ?>	=	new selector( myScr, 'ModSys', '/ModSys/<?php echo $myScr->ScreenName ; ?>/sel<?php echo $myScr->ScreenName ; ?>.php', '<?php echo $myScr->ScreenName ; ?>') ;
	myScr.sel<?php echo $myScr->ScreenName ; ?>.action	=	"/ModSys/<?php echo $myScr->ScreenName ; ?>/sel<?php echo $myScr->ScreenName ; ?>_action.php" ;
	myScr.sel<?php echo $myScr->ScreenName ; ?>.filterForm	=	"sel<?php echo $myScr->ScreenName ; ?>" ;
	myScr.sel<?php echo $myScr->ScreenName ; ?>.onSelect	=	load<?php echo $myScr->ScreenName ; ?>ById ;
	myScr.sel<?php echo $myScr->ScreenName ; ?>.dtvAdd( "myScr.sel<?php echo $myScr->ScreenName ; ?>.dtv", "Table<?php echo $myScr->ScreenName ; ?>Survey", "<?php echo $myScr->ScreenName ; ?>", "<?php echo $myScr->ScreenName ; ?>", null) ;
	myScr.sel<?php echo $myScr->ScreenName ; ?>.dtv.f1	=	"formSel<?php echo $myScr->ScreenName ; ?>Top" ;
	myScr.sel<?php echo $myScr->ScreenName ; ?>.dtv.f2	=	"formSel<?php echo $myScr->ScreenName ; ?>Bot" ;
	// create the dataminers
	// make sure everything is setup fo this module, e.g. menuitems enables/disables etc.
	myScr.link() ;
	if ( pendingKey != "") {
		requestUni( 'ModBase', '<?php echo $myScr->ScreenName ; ?>', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, show<?php echo $myScr->ScreenName ; ?>All) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	}
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	screen<?php echo $myScr->ScreenName ; ?>	=	myScr ;
	_debugL( 0x00000001, "regMod<?php echo $myScr->ScreenName ; ?>: end\n") ;
}
function	link<?php echo $myScr->ScreenName ; ?>() {
	_debugL( 0x00000001, "link<?php echo $myScr->ScreenName ; ?>: \n") ;
}
/**
 * 
 */
function	load<?php echo $myScr->ScreenName ; ?>ById( _id) {
	_debugL( 0x00000001, "main<?php echo $myScr->ScreenName ; ?>.js::load<?php echo $myScr->ScreenName ; ?>ById(" + _id.toString() + "): begin\n") ;
	requestUni( 'ModSys', '<?php echo $myScr->ScreenName ; ?>', '/Common/hdlObject.php', 'getXMLComplete', '', _id, '', null, show<?php echo $myScr->ScreenName ; ?>All) ;
	_debugL( 0x00000001, "main<?php echo $myScr->ScreenName ; ?>.js::load<?php echo $myScr->ScreenName ; ?>ById(): end\n") ;
}
/**
 *
 */
function	show<?php echo $myScr->ScreenName ; ?>All( _response) {
	show<?php echo $myScr->ScreenName ; ?>( _response) ;
//	showKundeAdrs( _response, "form<?php echo $myScr->ScreenName ; ?>Kunde", "form<?php echo $myScr->ScreenName ; ?>KundeKontakt", "form<?php echo $myScr->ScreenName ; ?>LiefKunde", "form<?php echo $myScr->ScreenName ; ?>LiefKundeKontakt", "form<?php echo $myScr->ScreenName ; ?>RechKunde", "form<?php echo $myScr->ScreenName ; ?>RechKundeKontakt") ;
	// refresh the itemlist
//	myScr.dtv<?php echo $myScr->ScreenName ; ?>Posten.primObjKey	=	myScr.keyField.value ;
//	myScr.dtv<?php echo $myScr->ScreenName ; ?>Posten.show( _response) ;
	//
}
/**
 *
 */
function	show<?php echo $myScr->ScreenName ; ?>( _response) {
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
	$myMainTab->setIterCond( "ParentScreenName = '" . $myScr->ScreenName . "' ") ;
	$myMainTab->setIterOrder( "ORDER BY SeqNo ASC ") ;
	foreach ( $myMainTab as $key => $tab) {
		$tabName	=	$tab->TabName ;
		echo "dispAttrs( attrs, \"form".$modName."_".$scrName."_".$tabName."\") ;".$lineEnd ;
	}
?>
}
/**
 * 
 */
function	new<?php echo $myScr->ScreenName ; ?>() {
}
<?php
} else {
	
}
?>