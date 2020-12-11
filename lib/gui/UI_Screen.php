<?php
/**
 * UIScreen.php - Definition der Basis Klasses f�r Liefn Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * UIScreen - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage UIScreen
 */
class	UI_Screen	extends	UI_Object	{
	/**
	 *
	 */
	function	__construct( $_myScreenName='') {
		parent::__construct( "UI_Screen", "Id") ;
		if ( strlen( $_myScreenName) > 0) {
			$this->setScreenName( $_myScreenName) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setScreenName( $_myScreenName) {
		$this->ScreenName	=	$_myScreenName ;
		if ( strlen( $_myScreenName) > 0) {
			$this->reload() ;
		}
		return $this->_valid ;
	}
	/**
	 * methods: addDep/updDep/copyDep/delDep
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		switch ( $_val) {
			case	"UITab"	:
				$tmpObj	=	new UITab() ;
				$tmpObj->getFromPostL() ;
				$tmpObj->ParentScreenName	=	$this->ScreenName ;
				$tmpObj->storeInDb() ;
				break ;
			default	:
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				$tmpObj->getFromPostL() ;
				$tmpObj->$myKeyCol	=	$this->$myKeyCol ;
				$tmpObj->storeInDb() ;
				break ;
		}
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
	/**
	 * methods: retrieval
	 */
	/**
	 * (non-PHPdoc)
	 * @see UIObject::getTableDepAsXML()
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "UITab") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see UIObject::getTableDepAsXML()
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see UIObject::getTableDepAsXML()
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		switch ( $_val) {
			default	:
				$objName	=	$_val ;
				$tmpObj	=	new $objName() ;
				$tmpObj->setId( $_id) ;
				$_POST['_step']	=	$_id ;
				return $tmpObj->tableFromDb( "", "", "ParentScreenName = '$this->ScreenName' ", "ORDER BY SeqNo ") ;
		}
	}
	/**
	 * getJSSelectorCode()
	 */
	function	getJSSelectorCode() {
		$lineEnd	=	"\n" ;
		$ret	=	"" ;
		$modName	=	$this->ModuleName ;
		$scrName	=	"screen".$this->ScreenName ;
		$selName	=	$scrName.".sel".$this->MainObj ;
		$subDir	=	$this->SubDir ;
		$objName	=	$this->MainObj ;
		$ret	=	"//\n// create the selector\n//\n" ;
		$ret	.=	$selName."	=	new selector( ".$scrName.", '".$modName."', '/".$modName."/".$this->SubDir."/sel".$objName.".php', 'Selector".$objName."') ;".$lineEnd
				.	$selName.".action	=	\"/Common/hdlObject.php\" ;".$lineEnd
				.	$selName.".filterForm	=	\"formSel".$objName."Filter\" ;".$lineEnd
				.	$selName.".onSelect	=	load".$objName."ById ;".$lineEnd
				.	$selName.".dtvAdd( \"".$selName.".dtv\", \"Table".$objName."Survey\", \"".$objName."\", \"".$objName."\", null) ;".$lineEnd
				.	$selName.".dtv.f1	=	\"formSel".$objName."Top\" ;".$lineEnd
				.	$selName.".dtv.f2	=	\"formSel".$objName."Bot\" ;".$lineEnd
				.	$selName.".dtv.filter	=	\"formSel".$objName."Filter\" ;".$lineEnd
				;
		return $ret ;
	}
	/**
	 * getJSSelectorCode()
	 */
	function	getJSSelectorCode2() {
		error_log( "UIScreen.php::getJSSelectorCode(): begin [for: $this->ScreenName]") ;
		$lineEnd	=	"\n" ;
		$ret	=	"" ;
		$modName	=	$this->ModuleName ;
		$myMod	=	new UIModule() ;
		$myMod->setKey( $this->ModuleName) ;
		$scrName	=	"screen".$this->ScreenName ;
		$selName	=	$scrName.".sel".$this->MainObj ;
		$subDir	=	$this->SubDir ;
		$objName	=	$this->MainObj ;
		$ret	=	"//\n// create the selector\n//\n" ;
		$ret	.=	$selName."	=	new selector( ".$scrName.", '".$myMod->Dir."', '/".$myMod->Dir."/".$this->SubDir."/sel".$objName.".php', '".$objName."') ;".$lineEnd
				.	$selName.".action	=	\"/Common/hdlObject.php\" ;".$lineEnd
				.	$selName.".filterForm	=	\"formSel".$objName."Filter\" ;".$lineEnd
				.	$selName.".onSelect	=	load".$objName."ById ;".$lineEnd
				.	$selName.".dtvAdd( \"".$selName.".dtv\", \"Table".$objName."Survey\", \"".$objName."\", \"".$objName."\", null) ;".$lineEnd
				.	$selName.".dtv.f1	=	\"formSel".$objName."Top\" ;".$lineEnd
				.	$selName.".dtv.f2	=	\"formSel".$objName."Bot\" ;".$lineEnd
				.	$selName.".dtv.filter	=	\"formSel".$objName."Filter\" ;".$lineEnd
				.	$selName.".dtv.phpGetCall	=	\"getList\" ;".$lineEnd
				;
		return $ret ;
	}
	function	getJSCode() {
		$lineEnd	=	"\n" ;
		$ret	=	"" ;
		$editor	=	new UIEditor() ;
		/**
		 *
		 * Enter description here ...
		 * @var unknown_type
		 */
//		$ret	.=	"//\n// automatically generated code: create editors \n//\n" ;
//		$ret	.=	"_debugL( 0x00000001, \"creating editor ...\") ;\n" ;
//		$editor->setIterCond( "ScreenName = '$this->ScreenName' ") ;
//		foreach ( $editor as $key => $edt) {
//			$edtName	=	$edt->EditorName ;
//			echo "screen".$this->ScreenName.".".$edtName." = new objEditor( screen".$this->ScreenName.".".$edtName.", \"ModCore\", \"/ModCore/editor.php?editorName=".$edtName."&ownRef=screen".$this->ScreenName.".".$edtName."\", \"".$edt->MainObj."\", null, null) ;".$lineEnd ;
//		}
		/**
		 *
		 * Enter description here ...
		 * @var unknown_type
		 */
		$tab	=	new UITab() ;
		$tab->setIterCond( "ParentScreenName = '". $this->ScreenName."' ") ;
		$tab->setIterOrder( "ORDER BY SeqNo ") ;
		$ret	.=	"//\n// automatically generated code: create tab specific code \n//\n" ;
		reset( $tab) ;
		foreach ( $tab as $key => $actTab) {
//			error_log( "UIScreen.php::UIScreen::getJSTabCode(): working on '$actTab->TabName'") ;
			$ret	.=	$actTab->getJSCode( $this->ScreenName) ;
		}
		return $ret ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="") {
		$moduleNameCrit	=	$_POST['_SModuleName'] ;
		$screenNameCrit	=	$_POST['_SScreenName'] ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( " ;
		$filter	.=	"C.ModuleName like '%" . $moduleNameCrit . "%' " ;
		$filter	.=	"AND C.ScreenName like '%" . $screenNameCrit . "%' " ;
		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "ModuleName", "var") ;
		$myObj->addCol( "ScreenName", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.ModuleName ASC, C.ScreenName ASC ",
								"UI_Screen",
								"UI_Screen",
								"C.Id, C.ModuleName, C.ScreenName ") ;
//		error_log( $ret) ;
		return $ret ;
	}
}
?>
