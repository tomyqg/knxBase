<?php
/**
 *
 *
 * @author miskhwe
 */
class	Navigator	extends	Page	{
	/**
	 *
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 * @return unknown
	 */
	function	run( $_pageNo, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		FDbg::begin( 2, "Navigator.php", "Navigator", "run( '$_pageNo', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		$myProductGroup	=	new ProductGroup( "00000000") ;
		$buffer	=	self::writeLink( $myProductGroup, false, false) ;
		if ( $_prodGrNo == "BAA") {
			$buffer	.=	$this->writeMenu( "00000000") ;
		} else {
			$buffer	.=	$this->writeMenu( $_prodGrNo) ;
		}
		FDbg::end() ;
		return $buffer ;
	}
	function	writeMenu( $_prodGrNo, $_lang="de", $_country="de", $_subBuf="", $_subProductGroupNo="") {
		FDbg::begin( 2, "Navigator.php", "Navigator", "writeMenu( '$_prodGrNo', '$_lang', '$_country', '<_subBuf>', '$_subProductGroupNo')") ;
		/**
		 *
		 */
		$buffer	=	"" ;
		$myTexte	=	new Texte() ;
		$subProductGroup	=	new ProductGroup() ;
		$myProductGroup	=	new ProductGroup( $_prodGrNo) ;
		$myProductGroupItem	=	new ProductGroupItem() ;
		/**
		 * jetzt das menu fuer die linke spalte zusammenbasteln
		 */
		$prodGrItem	=	new ProductGroupItem() ;
		$prodGrItem->setIterCond( "ProductGroupNo = '" . $myProductGroup->ProductGroupNo . "' ") ;
		$prodGrItem->setIterOrder( "ItemNo ") ;
		foreach ( $prodGrItem as $key => $obj) {
			//
			$subProductGroup->setProductGroupNo( $prodGrItem->CompProductGroupNo) ;
			FDbg::trace( 2, "Navigator.php", "Navigator", "writeMenu(...)", "$subProductGroup->ProductGroupNo --> $subProductGroup->ProductGroupName") ;
			if ( $subProductGroup->MenuEntry == 1) {
				/**
				 * PageType's:
				 *	0	= entry page (corresponds to index.html)
				 *	1	= normal survey of product group
				 *	2	=
				 *	3	=
				 *	4	=
				 *	5	= external page in new browser window
				 */
				$active	=	false ;
				if ( $subProductGroup->ProductGroupNo == $_subProductGroupNo && $_subBuf == "") {
					$active	=	true ;
				}
				if ( $subProductGroup->PageType == 1) {
					if ( $subProductGroup->Level < 5) {
						$buffer	.=	self::writeLink( $subProductGroup, $active, 0) ;
					}
				} else if ( $subProductGroup->PageType == 2) {
					$buffer	.=	self::writeLink( $subProductGroup, $myProductGroup, 0) ;
				} else if ( $subProductGroup->PageType == 3 && strlen( $subProductGroup->Condition) > 0) {
					$buffer	.=	self::writeCondLink( $subProductGroup, $myProductGroup, 0, $subProductGroup->Condition) ;
				} else if ( $subProductGroup->PageType == 3) {
					$buffer	.=	self::writeLink( $subProductGroup, $myProductGroup, 0) ;
				} else if ( $subProductGroup->PageType == 4) {
					$buffer	.=	self::writeLink( $subProductGroup, $myProductGroup, 0) ;
				} else if ( $subProductGroup->PageType == 5) {
					$buffer	.=	"<a class=\"g" . $subProductGroup->Level . "p\" href=\"$subProductGroup->TargetURL\" target=\"new\" title=\"MODIS auf ebay (&oouml;ffnet in eineme neuen Fenster/Tabulator\">" ;
					$buffer	.=	htmlentities( $subProductGroup->ProductGroupName, ENT_COMPAT, "UTF-8") ;
					$buffer	.=	"</a>\n" ;
				}
				if ( $subProductGroup->ProductGroupNo == $_subProductGroupNo) {
					$buffer	.=	$_subBuf ;
				}
			}
		}
		$myProductGroupItem->fetchFromDbWhere( "CompProductGroupNo = '" . $_prodGrNo . "' ") ;
		if ( $myProductGroupItem->isValid()) {
			if ( $myProductGroupItem->ProductGroupNo != "") {
				$buffer	=	$this->writeMenu( $myProductGroupItem->ProductGroupNo, $_lang, $_country, $buffer, $_prodGrNo) ;
			}
		}
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 *
	 * @param stringe $_linkProductGroup url of target
	 * @param ProductGroup $_prodGr
	 * @param unknown_type $_absMode
	 * @param unknown_type $_lang
	 * @param unknown_type $_country
	 * @return string
	 */
	function	writeLink( $_linkProductGroup, $_active, $_absMode, $_lang='de', $_country='de') {
		FDbg::begin( 2, "Navigator.php", "Navigator", "writeLink( ...)") ;
		$buffer	=	"" ;
		if ( $_linkProductGroup->TargetURL != "") {
			$ziel	=	$_linkProductGroup->TargetURL ;
		} else {
			$ziel	=	$_linkProductGroup->ProductGroupName ;
		}
		if ( $_active) {
			$buffer	.=	"<a class=\"g" . $_linkProductGroup->Level . "a\" href=\"/$_linkProductGroup->ProductGroupNameStripped.html\">" ;
		} else {
			$buffer	.=	"<a class=\"g" . $_linkProductGroup->Level . "p\" href=\"/$_linkProductGroup->ProductGroupNameStripped.html\">" ;
		}
		$buffer	.=	htmlentities( $_linkProductGroup->ProductGroupName, ENT_COMPAT, "UTF-8") ;
		$buffer	.=	"</a> \n" ;
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 *
	 * @param unknown_type $_indent
	 * @param unknown_type $_link
	 * @param unknown_type $_linkProductGroup
	 * @param unknown_type $_prodGr
	 * @param unknown_type $_absMode
	 * @param unknown_type $_cond
	 * @param unknown_type $_lang
	 * @param unknown_type $_country
	 * @return string
	 */
	function	writeCondLink( $_indent, $_link, $_linkProductGroup, $_prodGr, $_absMode, $_cond, $_lang='de', $_country='de') {
		global	$myCustomer ;
		global	$myCustomerContact ;
		$buffer	=	"" ;
		$buffer	.=	"<!-- evaluating conditional link display	-->\n" ;
		if ( $_linkProductGroup->TargetURL != "") {
			$ziel	=	$_linkProductGroup->TargetURL ;
		} else {
			$ziel	=	$_linkProductGroup->ProductGroupName ;
		}
		$show	=	false ;
		switch ( $_cond) {
		case	"validUser"	:
			if ( $myCustomer->_valid) {
				$show	=	true ;
			}
			break ;
		case	"validMasterUser"	:
			if ( $myCustomer->_valid) {
				if ( strcmp( $myCustomerContact->CustomerContactNo, "000") == 0) {
					$show	=	true ;
				}
			}
			break ;
		case	"nodisplay"	:
			$show	=	false ;
			break ;
		}
		if ( $show) {
			if ( strcmp( $_linkProductGroup->ProductGroupNo, $_prodGr->ProductGroupNo) == 0) {
				$buffer	.=	"<a class=\"g" . $_indent . "a\" href=\"/$_linkProductGroup->ProductGroupNameStripped.html\">" ;
			} else {
				$buffer	.=	"<a class=\"g" . $_indent . "p\" href=\"/$_linkProductGroup->ProductGroupNameStripped.html\">" ;
			}
			$buffer	.=	$_linkProductGroup->ProductGroupName ;
			$buffer	.=	"</a> \n" ;
		}
		$buffer	.=	"<!-- done with: evaluating conditional link display	-->\n" ;
		return $buffer ;
	}
}
?>
