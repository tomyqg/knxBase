<?php
/**
 * Copyright (c) 2015, 2016 wimtecc, Karl-Heinz Welter
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class	HTMLdojo	extends	HTML	{
	function	__construct( $_tag) {
		parent::__construct( $_tag) ;
	}
	static	function	dojoButton( $_label, $_onclick) {
		$myBtn	=	new HTML( "button") ;
		$myBtn->addAttr( "data-dojo-type", "dijit/form/Button") ;
		$myBtn->addAttr( "onclick", $_onclick) ;
		$myBtn->addText( $_label) ;
		return $myBtn ;
	}
	static	function	dojoLabel( $_label, $_onclick="") {
//		$myLbl	=	new HTML( "th") ;
//		$myLbl->addText( $_label) ;
		return $_label ;
	}
	static	function	dojoInput( $_type, $_name, $_len, $_maxlen, $_value="") {
		switch ( $_type) {
		case	"number"	:
			break ;
		case	"float"	:
			break ;
		case	"text"	:
			$myInp	=	new HTML( "input") ;
			$myInp->addAttr( "id", $_name) ;
			$myInp->addAttr( "name", $_name) ;
			$myInp->addAttr( "value", $_value) ;
			$myInp->addAttr( "size", $_len) ;			// visible length
			$myInp->addAttr( "maxlength", $_maxlen) ;	// max. input length
			break ;
		}
		return $myInp ;
	}
	static	function	lbc( $_xml, $_tag, $_attributes) {
//		echo( "HTMLdojo.php::lbc( <_xml>, '$_tag', <_attributes>): begin\n") ;
		$node	=	new self( "div") ;
		$node->addAttr( "data-dojo-type", "dijit/layout/BorderContainer") ;
		$node->addAttr( "style", $_attributes["style"]) ;
		HTML::create( $_xml, $node, "lbc") ;
//		echo( "HTMLdojo.php::lbc( <_xml>, '$_tag', <_attributes>): end\n") ;
		return $node ;
	}
	static	function	lcp( $_xml, $_tag, $_attributes) {
		FDbg::begin( 1, "HTMLdojo.php", "HTMLdojo", "lcp( <XML>', '$_tag', '".print_r( $_attributes, true)."')") ;
		$node	=	new self( "div") ;
		$node->addAttr( "data-dojo-type", "dijit/layout/ContentPane") ;
		if ( isset( $_attributes["data-dojo-props"]))
			$node->addAttr( "data-dojo-props", $_attributes["data-dojo-props"]) ;
		if ( isset( $_attributes["title"])) {
			$title	=	$_attributes["title"] ;
			$node->addAttr( "title", $_attributes["title"]) ;
		}
		if ( isset( $_attributes["title"])) {
			$title	=	$_attributes["title"] ;
			$myCall	=	"if ( screenCurrent) screenCurrent.onShowTabEntry( '".$title."') ;" ;
			$node->addAttr( "onShow", $myCall) ;
		} else {
			$myCall	=	"if ( screenCurrent) screenCurrent.onShowTabEntry( 'UNDEFINED-TITLE') ;" ;
			$node->addAttr( "onShow", $myCall) ;
		}
		if ( isset( $_attributes["id"]))
			$node->addAttr( "id", $_attributes["id"]) ;
		if ( isset( $_attributes["title"])) {
		}
		HTML::create( $_xml, $node, "lcp") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "HTMLdojo.php", "HTMLdojo", "lcp( <XML>', '$_tag', ')", print_r( $_attributes, true)) ;
		FDbg::end() ;
		return $node ;
	}
	static	function	ltc( $_xml, $_tag, $_attributes) {
		$node	=	new self( "div") ;
		$node->addAttr( "data-dojo-type", "dijit/layout/TabContainer") ;
		if ( isset( $_attributes["data-dojo-props"]))
			$node->addAttr( "data-dojo-props", $_attributes["data-dojo-props"]) ;
		if ( isset( $_attributes["title"]))
			$node->addAttr( "title", $_attributes["title"]) ;
		if ( isset( $_attributes["id"]))
			$node->addAttr( "id", $_attributes["id"]) ;
		HTML::create( $_xml, $node, "ltc") ;
		return $node ;
	}
}
?>
