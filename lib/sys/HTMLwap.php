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
/**
 *	HTMLwap.php
 *	===========
 *
 *	Implements a class to create wap-compliant HTML pages or HTML page-fragments respectively.
 *	All WAP/XML tags have to reside in the wap namespace, i.e. the tags need to read e.g. <wap:grid>.
 *	The class generates extended HTML markup, e.g. a wap-tag wap:inputrow will create a <tr> with included
 *	<td>s and in turn included form-elements.
 *
 *	The following tags are recognized by this class:
 *
 *	wap:input
 *	wap:image			eissMod		=	domCol.getAttribute( "eissMod") ;
 *
 *	The following, wap-specific attributes are recognized:
 *
 *	data-wap-attr
 *	data-wap-type				type of the variable, can be either of:
 *							int, text, longtext, date, float, flag, option
 *	data-wap-mode				determines if an form-element is editable or not, can be either of:
 *							show (default) or edit
 *	data-wap-obj				class of an object to be dealt with
 *	data-wap-link-to			screen name of a screen to be called upon selection of an item
 *	wapConfirmDiscard	whether or not to check if a value was changed before discarding the change
 *	wapSize
 *	wapMax
 *	data-wap-functions
 *
 *	eissFnc				old, to be replaced by wapFnc!
 *						function to provide for an entry in a grid view
 *	eissAttr
 *
 * @author miskhwe
 *
 */
class	HTMLwap	extends	HTML	{
	/**
	 *
	 */
	private	static	$moduleName	=	null ;
	/**
	 *
	 * @var string name of the screen
	 */
	private	static	$screenName	=	null ;
	private	static	$mainClass	=	null ;
	private	static	$subClass	=	null ;
	static	function	trans( $_xml, $_tag, $_attributes) {
		$baseNode	=	new self( "input") ;
		return $baseNode ;
	}
	/**
	 * create an HTML input field
	 * attributes:
	 * 	req.		used as
	 * 	data-wap-attr			id, name
	 * @param unknown $_xml
	 * @param unknown $_tag
	 * @param array $_attributes
	 * @return HTMLwap
	 */
	public	static	function	moduleName() {
		return self::$moduleName ;
	}

	/**
	 *
	 */
	public	static	function	screenName() {
		return self::$screenName ;
	}

	/**
	 *
	 */
	static	function	wapscreen( $_xml, $_tag, $_attributes) {
		$divNode	=	new self( "div") ;
		$baseNode	=	$divNode ;
		$baseNode->addAttr( "class", "wapScreen") ;
		( $_attributes[ "data-wap-module"] ? $baseNode->addAttr( "data-wap-module", $_attributes[ "data-wap-module"]) : null) ;
		( $_attributes[ "data-wap-screen"] ? $baseNode->addAttr( "data-wap-screen", $_attributes[ "data-wap-screen"]) : null) ;
		( $_attributes[ "data-wap-core-object"] ? $baseNode->addAttr( "data-wap-core-object", $_attributes[ "data-wap-core-object"]) : null) ;
		( $_attributes[ "data-wap-core-object-key"] ? $baseNode->addAttr( "data-wap-core-object-key", $_attributes[ "data-wap-core-object-key"]) : null) ;
		if ( isset( $_attributes["data-wap-parent-object"])) {
			( $_attributes[ "data-wap-parent-object"] ? $baseNode->addAttr( "data-wap-parent-object", $_attributes[ "data-wap-parent-object"]) : null) ;
		}
		HTML::create( $_xml, $baseNode, "wapScreen") ;
		return $baseNode ;
	}

	/**
	 *
	 */
	static	function	wapdialog( $_xml, $_tag, $_attributes) {
		$divNode	=	new self( "div") ;
		if ( isset( $_attributes["id"])) {
			$divNode->addAttr( "id", $_attributes["id"]) ;
		}
		$baseNode	=	$divNode ;
		$baseNode->addAttr( "class", "wapDialog") ;			// wapDialog") ;
		( $_attributes[ "data-wap-module"] ? $baseNode->addAttr( "data-wap-module", $_attributes[ "data-wap-module"]) : null) ;
		( $_attributes[ "data-wap-screen"] ? $baseNode->addAttr( "data-wap-screen", $_attributes[ "data-wap-screen"]) : null) ;
		( $_attributes[ "data-wap-core-object"] ? $baseNode->addAttr( "data-wap-core-object", $_attributes[ "data-wap-core-object"]) : null) ;
		( $_attributes[ "data-wap-core-object-key"] ? $baseNode->addAttr( "data-wap-core-object-key", $_attributes[ "data-wap-core-object-key"]) : null) ;
		( $_attributes[ "data-wap-parent-object"] ? $baseNode->addAttr( "data-wap-parent-object", $_attributes[ "data-wap-parent-object"]) : null) ;
		if ( isset( $_attributes[ "w"])) {
			$baseNode->addAttr( "style", "width: ".$_attributes[ "w"]."px; height: ".$_attributes[ "h"]."") ;
		}
		HTML::create( $_xml, $baseNode, "wapDialog") ;
		return $baseNode ;
	}
	/**
	 * create an HTML input field
	 * attributes:
	 * 	req.		used as
	 * 	data-wap-attr			id, name
	 * @param unknown $_xml
	 * @param unknown $_tag
	 * @param array $_attributes
	 * @return HTMLwap
	 */
	static	function	cond( $_xml, $_tag, $_attributes) {
		$divNode	=	new self( "div") ;
		$baseNode	=	$divNode ;
		$baseNode->addAttr( "class", "wapCondition") ;
		HTML::create( $_xml, $baseNode, "cond") ;
		return $baseNode ;
	}
	/**
	 * create an HTML input field
	 * attributes:
	 * 	req.		used as
	 * 	data-wap-attr			id, name
	 * @param unknown $_xml
	 * @param unknown $_tag
	 * @param array $_attributes
	 * @return HTMLwap
	 */
	static	function	image( $_xml, $_tag, $_attributes) {
		$imageNode	=	new self( "img") ;
		$baseNode	=	$imageNode ;
		return $baseNode ;
	}

	/**
	 *
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	cellhelp( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "th") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "flHelp") ;
		if ( isset( $_attributes[ "rowspan"]))
			$cellNode->addAttr( "rowspan", $_attributes[ "rowspan"]) ;
		$imgNode	=	$cellNode->addNode( new HTML( "img")) ;
		$imgNode->addAttr( "src", "/api/loadImage.php?image=b_help.png") ;
		return $baseNode ;
	}
	static	function	celllabel( $_xml, $_tag, $_attributes) {
		$appUser	=	EISSCoreObject::__getAppUser() ;
		$cellNode	=	new self( "th") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "flMainData") ;
//		$cellNode	=	$cellNode->addNode( new HTML( "p")) ;
		/**
		 *
		 */
		if ( FTr::tr( $_attributes[ "data-wap-label"]) != $_attributes[ "data-wap-label"]) {
			$cellNode->addAttr( "class", "flTranslated") ;
		} else if ( $appUser->isGranted( "scr", "Translate_en_de")) {
			$imgNode	=	$cellNode->addNode( new HTML( "img")) ;
			$imgNode->addAttr( "src", "/api/loadImage.php?image=b_usredit.png") ;
			$imgNode->addAttr( "onclick", "screenLinkTo( 'AppTrans', '".FTr::getId( $_attributes[ "data-wap-label"])."', '".FTr::getLocale()."');") ;
		}
		if ( isset( $_attributes[ "rowspan"]))
			$cellNode->addAttr( "rowspan", $_attributes[ "rowspan"]) ;
		$cellNode->addText( FTr::tr( $_attributes[ "data-wap-label"]) . ":") ;
		return $baseNode ;
	}
	static	function	label( $_xml, $_tag, $_attributes) {
		$appUser	=	EISSCoreObject::__getAppUser() ;
		$labelNode	=	new self( "span") ;
		$baseNode	=	$labelNode ;
		$labelNode->addAttr( "class", "flMainData") ;
		/**
		 *
		 */
		if ( FTr::tr( $_attributes[ "data-wap-label"]) != $_attributes[ "data-wap-label"]) {
			$labelNode->addAttr( "class", "flTranslated") ;
		} else if ( $appUser->isGranted( "scr", "Translate_en_de")) {
			$imgNode	=	$cellNode->addNode( new HTML( "img")) ;
			$imgNode->addAttr( "src", "/api/loadImage.php?image=b_usredit.png") ;
			$imgNode->addAttr( "onclick", "screenLinkTo( 'AppTrans', '".FTr::getId( $_attributes[ "data-wap-label"])."', '".FTr::getLocale()."');") ;
		}
		$labelNode->addText( FTr::tr( $_attributes[ "data-wap-label"]) . ":") ;
		if ( isset( $_attributes[ "x"])) {
			$labelNode->addAttr( "style", "position: absolute; top:	".$_attributes[ "y"]."px; left: ".$_attributes[ "x"]."") ;
		}
		return $baseNode ;
	}
	static	function	input( $_xml, $_tag, $_attributes) {
		$inputNode	=	new HTML( "input") ;
		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField") ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$inputNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		if ( isset( $_attributes[ "value"]))
			$inputNode->addAttr( "value", "sdkjfhsdkjhk" . $_attributes[ "value"]) ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-type", "text") ;
		if ( isset( $_attributes[ "data-wap-type"]))
			$inputNode->addAttr( "data-wap-type", $_attributes[ "data-wap-type"]) ;
		if ( isset( $_attributes[ "data-wap-confirm-discard"]))
			$inputNode->addAttr( "data-wap-confirm-discard", $_attributes[ "data-wap-confirm-discard"]) ;
		$inputNode->addAttr( "data-wap-mode", "edit") ;
		$inputNode->addAttr( "maxlength", "64") ;
		if ( isset( $_attributes[ "max"]))
			$inputNode->addAttr( "maxlength", $_attributes[ "max"]) ;
		if ( isset( $_attributes[ "size"]))
			$inputNode->addAttr( "size", $_attributes[ "size"]) ;
		else
			$inputNode->addAttr( "size", "32") ;
		$inputNode->addAttr( "value", "") ;
		if ( isset( $_attributes[ "onkeypress"]))
			$inputNode->addAttr( "onkeypress", $_attributes[ "onkeypress"]) ;
		if ( isset( $_attributes[ "onkeyup"]))
			$inputNode->addAttr( "onkeyup", $_attributes[ "onkeyup"]) ;
		if ( isset( $_attributes[ "x"])) {
			$inputNode->addAttr( "style", "position: absolute; top:	".$_attributes[ "y"]."px; left: ".$_attributes[ "x"]."") ;
		}
		return $inputNode ;
	}
	static	function	hidden( $_xml, $_tag, $_attributes) {
		$inputNode	=	new HTML( "input") ;
		$inputNode->addAttr( "type", "hidden") ;
		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField") ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$inputNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		if ( isset( $_attributes[ "value"]))
			$inputNode->addAttr( "value", "sdkjfhsdkjhk" . $_attributes[ "value"]) ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-type", "text") ;
		if ( isset( $_attributes[ "data-wap-type"]))
			$inputNode->addAttr( "data-wap-type", $_attributes[ "data-wap-type"]) ;
		if ( isset( $_attributes[ "data-wap-confirm-discard"]))
			$inputNode->addAttr( "data-wap-confirm-discard", $_attributes[ "data-wap-confirm-discard"]) ;
		$inputNode->addAttr( "data-wap-mode", "edit") ;
		$inputNode->addAttr( "maxlength", "64") ;
		if ( isset( $_attributes[ "max"]))
			$inputNode->addAttr( "maxlength", $_attributes[ "max"]) ;
		if ( isset( $_attributes[ "size"]))
			$inputNode->addAttr( "size", $_attributes[ "size"]) ;
		else
			$inputNode->addAttr( "size", "32") ;
		$inputNode->addAttr( "value", "") ;
		if ( isset( $_attributes[ "onkeypress"]))
			$inputNode->addAttr( "onkeypress", $_attributes[ "onkeypress"]) ;
		if ( isset( $_attributes[ "onkeyup"]))
			$inputNode->addAttr( "onkeyup", $_attributes[ "onkeyup"]) ;
		if ( isset( $_attributes[ "x"])) {
			$inputNode->addAttr( "style", "position: absolute; top:	".$_attributes[ "y"]."px; left: ".$_attributes[ "x"]."") ;
		}
		return $inputNode ;
	}

	static	function	textarea( $_xml, $_tag, $_attributes) {
		$inputNode	=	new HTML( "textarea") ;
		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField") ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$inputNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		if ( isset( $_attributes[ "value"]))
			$inputNode->addAttr( "value", "sdkjfhsdkjhk" . $_attributes[ "value"]) ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-type", "text") ;
		if ( isset( $_attributes[ "data-wap-type"]))
			$inputNode->addAttr( "data-wap-type", $_attributes[ "data-wap-type"]) ;
		if ( isset( $_attributes[ "data-wap-confirm-discard"]))
			$inputNode->addAttr( "data-wap-confirm-discard", $_attributes[ "data-wap-confirm-discard"]) ;
		$inputNode->addAttr( "data-wap-mode", "edit") ;
		$inputNode->addAttr( "maxlength", "64") ;
		if ( isset( $_attributes[ "max"]))
			$inputNode->addAttr( "maxlength", $_attributes[ "max"]) ;
		if ( isset( $_attributes[ "size"]))
			$inputNode->addAttr( "size", $_attributes[ "size"]) ;
		else
			$inputNode->addAttr( "size", "32") ;
		$inputNode->addAttr( "value", "") ;
		if ( isset( $_attributes[ "onkeypress"]))
			$inputNode->addAttr( "onkeypress", $_attributes[ "onkeypress"]) ;
		if ( isset( $_attributes[ "onkeyup"]))
			$inputNode->addAttr( "onkeyup", $_attributes[ "onkeyup"]) ;
		if ( isset( $_attributes[ "x"])) {
			$inputNode->addAttr( "style", "position: absolute; top:	".$_attributes[ "y"]."px; left: ".$_attributes[ "x"]."") ;
		}
		return $inputNode ;
	}

	static	function	linkTo( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$btnNode->addAttr( "type", "image") ;
		$btnNode->addAttr( "src", "/api/loadImage.php?image=licon/Green/18/Forward.png") ;
		$btnNode->addAttr( "onclick", "screenLinkTo( '" . $_attributes[ "data-wap-link-to"] . "', document.getElementById( '".$_attributes[ "data-wap-link-ref-field"]."').value)") ;
		return $btnNode ;
	}
	static	function	cellinput( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$cellNode->addAttr( "width", "360") ;
		if ( isset( $_attributes[ "colspan"]))
			$cellNode->addAttr( "colspan", $_attributes[ "colspan"]) ;
		if ( isset( $_attributes[ "cellWidth"]))
			$cellNode->addAttr( "width", $_attributes[ "cellWidth"]) ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit") ;
		$cellNode->addNode( self::input( $_xml, $_tag, $_attributes)) ;
		if ( isset( $attributes[ "data-wap-link-to"])) {
			$cellNode->addNode( self::linkTo( $_xml, $_tag, $_attributes)) ;
		}
		return $cellNode ;
	}
	static	function	celldisplay( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$cellNode->addAttr( "width", "240") ;
		if ( isset( $_attributes[ "colspan"]))
			$cellNode->addAttr( "colspan", $_attributes[ "colspan"]) ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fDisplay") ;
		$inputNode	=	$cellNode->addNode( new HTML( "input")) ;
//		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField") ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$inputNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-type", "text") ;
		if ( isset( $_attributes[ "data-wap-type"]))
			$inputNode->addAttr( "data-wap-type", $_attributes[ "data-wap-type"]) ;
		$inputNode->addAttr( "data-wap-confirm-discard", "false") ;
		$inputNode->addAttr( "data-wap-mode", "show") ;
		$inputNode->addAttr( "maxlength", "64") ;
		$inputNode->addAttr( "size", "32") ;
		$inputNode->addAttr( "readonly", "true") ;
		if ( isset( $_attributes[ "max"]))
			$inputNode->addAttr( "maxlength", $_attributes[ "max"]) ;
		if ( isset( $_attributes[ "size"]))
			$inputNode->addAttr( "size", $_attributes[ "size"]) ;
		$inputNode->addAttr( "value", "") ;
		if ( isset( $_attributes[ "data-wap-link-to"])) {
			$btnNode	=	new self( "input") ;
			$btnNode->addAttr( "type", "image") ;
			$btnNode->addAttr( "src", "/api/loadImage.php?image=licon/Green/18/Forward.png") ;
			$btnNode->addAttr( "onclick", "screenLinkTo( '" . $_attributes[ "data-wap-link-to"] . "', getElementById( '".$inputNode->id."').value);") ;
			$baseNode->addNode( $btnNode) ;
		}
		return $baseNode ;
	}
	static	function	celltext( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit") ;
		$cellNode->addAttr( "width", "240") ;
		$inputNode	=	$cellNode->addNode( new HTML( "textarea")) ;
		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		if ( isset( $_attributes[ "id"]))
			$inputNode->addAttr( "id", $_attributes[ "id"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField") ;
		$inputNode->addAttr( "cols", $_attributes[ "cols"]) ;
		$inputNode->addAttr( "rows", $_attributes[ "rows"]) ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$inputNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-type", "longtext") ;
		if ( isset( $_attributes[ "data-wap-type"]))
			$inputNode->addAttr( "data-wap-type", $_attributes[ "data-wap-type"]) ;
		$inputNode->addAttr( "data-wap-mode", "edit") ;
		if ( isset( $_attributes[ "data-wap-mode"]))
			$inputNode->addAttr( "data-wap-mode", $_attributes[ "data-wap-mode"]) ;
		return $baseNode ;
	}
	static	function	cellrtf( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit") ;
		$cellNode->addAttr( "width", "360") ;
		$inputNode	=	$cellNode->addNode( new HTML( "textarea")) ;
		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		if ( isset( $_attributes[ "id"]))
			$inputNode->addAttr( "id", $_attributes[ "id"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField wapRTF") ;
		$inputNode->addAttr( "cols", $_attributes[ "cols"]) ;
		$inputNode->addAttr( "rows", $_attributes[ "rows"]) ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$inputNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-type", "longtext") ;
		if ( isset( $_attributes[ "data-wap-type"]))
			$inputNode->addAttr( "data-wap-type", $_attributes[ "data-wap-type"]) ;
		$inputNode->addAttr( "data-wap-mode", "edit") ;
		if ( isset( $_attributes[ "data-wap-mode"]))
			$inputNode->addAttr( "data-wap-mode", $_attributes[ "data-wap-mode"]) ;
		return $baseNode ;
	}
	static	function	cellhtml( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit wapField") ;
		$cellNode->addAttr( "width", "240") ;
		$divNode	=	$cellNode->addNode( new HTML( "div")) ;
		$divNode->addAttr( "id", $_attributes[ "id"]) ;
		$divNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$divNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		$divNode->addAttr( "data-wap-type", "html") ;
//		$divNode->addAttr( "height", "250px") ;
		return $baseNode ;
	}
	static	function	celloption( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit") ;
		$selectNode	=	$cellNode->addNode( new HTMLwap( "select")) ;
//		$selectNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$selectNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$selectNode->addAttr( "class", "wapField") ;
		$selectNode->addAttr( "size", "1") ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$selectNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		$selectNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$selectNode->addAttr( "data-wap-type", "option") ;
		if ( isset( $_attributes[ "data-wap-type"]))
			$selectNode->addAttr( "data-wap-type", $_attributes[ "data-wap-type"]) ;
		$selectNode->addAttr( "data-wap-mode", "edit") ;
		if ( isset( $_attributes[ "data-wap-mode"]))
			$selectNode->addAttr( "data-wap-mode", $_attributes[ "data-wap-mode"]) ;
		$_a	=	$_attributes ;
		$selectNode->_addOptions( $_a) ;
		/**
		 * process any "Link-To" attribute
		 */
		if ( isset( $_attributes[ "data-wap-link-to"])) {
			$btnNode	=	new self( "input") ;
			$btnNode->addAttr( "type", "image") ;
			$btnNode->addAttr( "src", "/api/loadImage.php?image=licon/Green/18/Forward.png") ;
			$btnNode->addAttr( "onclick", "screenLinkTo( '" . $_attributes[ "data-wap-link-to"] . "', getElementById( '".$selectNode->id."').value);") ;
			$baseNode->addNode( $btnNode) ;
		}
		return $baseNode ;
	}
	static	function	option( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "div") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit") ;
		$selectNode	=	$cellNode->addNode( new HTMLwap( "select")) ;
//		$selectNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$selectNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$selectNode->addAttr( "class", "wapField") ;
		if ( isset( $_attributes[ "x"])) {
			$cellNode->addAttr( "style", "position: absolute;top:".$_attributes[ "y"]."px;left:".$_attributes[ "x"]."px") ;
		}
		$selectNode->addAttr( "size", "1") ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$selectNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		$selectNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$selectNode->addAttr( "data-wap-type", "option") ;
		if ( isset( $_attributes[ "data-wap-type"]))
			$selectNode->addAttr( "data-wap-type", $_attributes[ "data-wap-type"]) ;
		$selectNode->addAttr( "data-wap-mode", "edit") ;
		if ( isset( $_attributes[ "data-wap-mode"]))
			$selectNode->addAttr( "data-wap-mode", $_attributes[ "data-wap-mode"]) ;
		$_a	=	$_attributes ;
		$selectNode->_addOptions( $_a) ;
		/**
		 * process any "Link-To" attribute
		 */
		if ( isset( $_attributes[ "data-wap-link-to"])) {
			$btnNode	=	new self( "input") ;
			$btnNode->addAttr( "type", "image") ;
			$btnNode->addAttr( "src", "/api/loadImage.php?image=licon/Green/18/Forward.png") ;
			$btnNode->addAttr( "onclick", "screenLinkTo( '" . $_attributes[ "data-wap-link-to"] . "', getElementById( '".$selectNode->id."').value);") ;
			$baseNode->addNode( $btnNode) ;
		}
		return $baseNode ;
	}
	static	function	cellflag( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit") ;
		$_a	=	$_attributes ;
		$cellNode->_addFlags( $_a) ;
		return $baseNode ;
	}
	static	function	cellcheck( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit") ;
		$_a	=	$_attributes ;
		$cellNode->_addCheck( $_a) ;
		return $baseNode ;
	}
	static	function	celldate( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fDate") ;
		$inputNode	=	$cellNode->addNode( new HTML( "input")) ;
		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField wapDate") ;
		$inputNode->addAttr( "type", "input") ;
		$inputNode->addAttr( "maxlength", "10") ;
		$inputNode->addAttr( "size", "10") ;
//		$inputNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		$inputNode->addAttr( "data-wap-type", "date") ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-mode", "edit") ;
		if ( isset( $_attributes[ "data-wap-mode"]))
			$inputNode->addAttr( "data-wap-mode", $_attributes[ "data-wap-mode"]) ;
		return $baseNode ;
	}
	static	function	celldatetime( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fDate") ;
		$inputNode	=	$cellNode->addNode( new HTML( "input")) ;
		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField wapDate input") ;
		$inputNode->addAttr( "type", "input") ;
		$inputNode->addAttr( "maxlength", "19") ;
		$inputNode->addAttr( "size", "19") ;
//		$inputNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		$inputNode->addAttr( "data-wap-type", "datetime") ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-mode", "edit") ;
		if ( isset( $_attributes[ "data-wap-mode"]))
			$inputNode->addAttr( "data-wap-mode", $_attributes[ "data-wap-mode"]) ;
		return $baseNode ;
	}
	static	function	celltime( $_xml, $_tag, $_attributes) {
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fDate wapTime") ;
		$inputNode	=	$cellNode->addNode( new HTML( "input")) ;
		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField wapDate") ;
		$inputNode->addAttr( "type", "input") ;
		$inputNode->addAttr( "maxlength", "8") ;
		$inputNode->addAttr( "size", "8") ;
//		$inputNode->addAttr( "data-wap-obj", $_attributes[ "data-wap-obj"]) ;
		$inputNode->addAttr( "data-wap-type", "date") ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-mode", "edit") ;
		if ( isset( $_attributes[ "data-wap-mode"]))
			$inputNode->addAttr( "data-wap-mode", $_attributes[ "data-wap-mode"]) ;
		return $baseNode ;
	}
	static	function	cellimage( $_xml, $_tag, $_attributes) {
		$imageId	=	sprintf( "%010d", rand(0,999999999)) ;
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit") ;
		$buttonNode	=	$cellNode->addNode( new HTML( "button")) ;
		$buttonNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$buttonNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$buttonNode->addAttr( "class", "wapField") ;
		$buttonNode->addAttr( "data-wap-type", "image") ;
		$buttonNode->addAttr( "data-wap-confirm-discard", "false") ;
		$buttonNode->addAttr( "data-wap-image-ref", $imageId) ;
		$buttonNode->addAttr( "data-wap-image-obj", $_attributes[ "data-wap-image-obj"]) ;
		$buttonNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$imageNode	=	$buttonNode->addNode( new HTML( "img")) ;
		$imageNode->addAttr( "id", $imageId) ;
		return $baseNode ;
	}
	static	function	cellupload( $_xml, $_tag, $_attributes) {
		$formId	=	$_attributes[ "data-wap-attr"] . "_" . sprintf( "%010d", rand(0,999999999)) ;
		$cellNode	=	new self( "td") ;
		$baseNode	=	$cellNode ;
		$cellNode->addAttr( "class", "fEdit") ;
		$formNode	=	$cellNode->addNode( new HTML( "form")) ;
		$formNode->addAttr( "id",  $formId) ;
		$uploadNode	=	$formNode->addNode( new HTML( "input")) ;
		$uploadNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$uploadNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$uploadNode->addAttr( "type", "file") ;
		$uploadNode->addAttr( "data-wap-type", "upload") ;
		$uploadNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$uploadNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$doitNode	=	$formNode->addNode( new HTML( "input")) ;
		$doitNode->addAttr( "type", "button") ;
		$doitNode->addAttr( "value", FTr::tr( "Upload!")) ;
		$doitNode->addAttr( "class", "wapBtn wapBtnUpload") ;
		if ( isset( $_attributes[ "data-wap-upload-function"]))
			$doitNode->addAttr( "data-wap-upload-function", $_attributes[ "data-wap-upload-function"]) ;
		return $baseNode ;
	}
	/**
	 *
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	row( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		HTML::create( $_xml, $rowNode, "row") ;
		return $baseNode ;
	}
	static	function	rowdisplay( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		// create the label
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celldisplay( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowinput( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		// create the label
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::cellinput( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowtext( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		// create the label
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celltext( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowrtf( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		// create the label
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::cellrtf( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowhtml( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		// create the label
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::cellhtml( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowoption( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celloption( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowflag( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::cellflag( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowcheck( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::cellcheck( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowdate( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celldate( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowdatetime( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celldatetime( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowtime( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celltime( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowimage( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::cellimage( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	rowupload( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		$rowNode->addNode( HTMLwap::cellhelp( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::cellupload( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	/**
	 *
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	key(  $_xml, $_tag, $_attributes) {
		$divNodeContent	=	new self( "div") ;
		$baseNode	=	$divNodeContent ;
		$divNodeContent->addAttr( "id", "content") ;
		$divNodeMainData	=	$divNodeContent->addNode( new HTML( "div")) ;
		$divNodeMainData->addAttr( "id", "keydata") ;
		HTML::create( $_xml, $divNodeMainData, "key") ;
		return $baseNode ;
	}
	static	function	keyform( $_xml, $_tag, $_attributes) {
		$formNode	=	new self( "div") ;
		$baseNode	=	$formNode ;
		$formNode->addAttr( "id", $_attributes[ "id"]) ;
		$formNode->addAttr( "name", $_attributes[ "id"]) ;
		$formNode->addAttr( "class", "wapForm wapKeyForm") ;
//		$formNode->addAttr( "onsubmit", "return false ;") ;
		$formNode->addAttr( "data-wap-confirm-discard", "false") ;
		$tableNode	=	$formNode->addNode( new HTML( "table")) ;
		HTML::create( $_xml, $tableNode, "keyform") ;
		return $baseNode ;
	}
	static	function	keyrow( $_xml, $_tag, $_attributes) {
		$appUser	=	EISSCoreObject::__getAppUser() ;
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		// create the label
		$thNode	=	$rowNode->addNode( new HTML( "th")) ;
		if ( FTr::tr( $_attributes[ "data-wap-label"]) != $_attributes[ "data-wap-label"]) {
			$thNode->addAttr( "class", "flTranslated") ;
		} else if ( $appUser->isGranted( "scr", "Translate_en_de")) {
			$imgNode	=	$thNode->addNode( new HTML( "img")) ;
			$imgNode->addAttr( "src", "/api/loadImage.php?image=b_usredit.png") ;
			$imgNode->addAttr( "title", "Translate this Label") ;
			$thNode->addAttr( "class", "flTranslate") ;
		}
		$thNode->addText( FTr::tr( $_attributes[ "data-wap-label"])) ;
		// if required: create the prev-button
		if ( isset( $_attributes[ "data-wap-prev"]) && $_attributes[ "data-wap-prev"] == "true") {
			$tdNode	=		$rowNode->addNode( new HTML( "td")) ;
			$tdNode->addAttr( "class", "space") ;
			$inputNode	=	$tdNode->addNode( new HTML( "input")) ;
			$inputNode->addAttr( "type", "image") ;
			$inputNode->addAttr( "src", "/api/loadImage.php?image=licon/yellow/18/left.png") ;
//			$inputNode->addAttr( "onclick", "hookPrevObject() ; return false ;") ;
			$inputNode->addAttr( "class", "prevObject") ;
		}
		// create the input field
		$tdNode	=	$rowNode->addNode( new HTML( "td")) ;
		$inputNode	=	$tdNode->addNode( new HTML( "input")) ;
		$inputNode->addAttr( "type", "text") ;
		$inputNode->addAttr( "id", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "name", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "class", "wapField wapPrimKey") ;
//		$inputNode->addAttr( "onkeypress", "return hookEnterKey( event) ;") ;
		$inputNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		$inputNode->addAttr( "data-wap-type", "text") ;
		if ( isset( $_attributes[ "data-wap-type"]))
			$inputNode->addAttr( "data-wap-type", $_attributes[ "data-wap-type"]) ;
		// if required: create the next-button
		if ( isset( $_attributes[ "data-wap-next"]) && $_attributes[ "data-wap-next"] == "true") {
			$tdNode	=		$rowNode->addNode( new HTML( "td")) ;
			$tdNode->addAttr( "class", "space") ;
			$inputNode	=	$tdNode->addNode( new HTML( "input")) ;
			$inputNode->addAttr( "type", "image") ;
			$inputNode->addAttr( "src", "/api/loadImage.php?image=licon/yellow/18/right.png") ;
//			$inputNode->addAttr( "onclick", "hookNextObject() ; return false ;") ;
			$inputNode->addAttr( "class", "nextObject") ;
		}
		//
		if ( isset( $_attributes[ "data-wap-select"])) {
			$tdNode	=		$rowNode->addNode( new HTML( "td")) ;
			$tdNode->addAttr( "class", "space") ;
			$inputNode	=	$tdNode->addNode( new HTML( "input")) ;
			$inputNode->addAttr( "type", "image") ;
			$inputNode->addAttr( "src", "/api/loadImage.php?image=licon/yellow/18/zoom.png") ;
			$inputNode->addAttr( "onclick", "screenCurrent.select.startSelect( '', -1, '') ;") ;
		}
		return $baseNode ;
	}
	static	function	keyinput( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		// create the label
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::cellinput( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	static	function	keydisplay( $_xml, $_tag, $_attributes) {
		$rowNode	=	new self( "tr") ;
		$baseNode	=	$rowNode ;
		// create the label
		$rowNode->addNode( HTMLwap::celllabel( $_xml, $_tag, $_attributes)) ;
		$rowNode->addNode( HTMLwap::celldisplay( $_xml, $_tag, $_attributes)) ;
		return $baseNode ;
	}
	/**
	 *
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	data( $_xml, $_tag, $_attributes) {
		$divNodeContent	=	new self( "div") ;
		$baseNode	=	$divNodeContent ;
		$divNodeContent->addAttr( "id", "content") ;
		$divNodeMainData	=	$divNodeContent->addNode( new HTML( "div")) ;
		$divNodeMainData->addAttr( "id", "maindata") ;
		HTML::create( $_xml, $divNodeMainData, "data") ;
		return $baseNode ;
	}
	static	function	dataform( $_xml, $_tag, $_attributes) {
		$formNode	=	new self( "div") ;
		if ( isset( $_attributes[ "w"])) {
			$formNode->addAttr( "style", "width: ".$_attributes[ "w"]."px; height: ".$_attributes[ "h"]."") ;
		}
		$baseNode	=	$formNode ;
		$formNode->addAttr( "id", $_attributes[ "id"]) ;
		$formNode->addAttr( "class", "wapForm") ;
		if ( isset( $_attributes["class"])) {
			$formNode->appAttr( "class", $_attributes["class"]) ;
		}
		$formNode->addAttr( "name", $_attributes[ "id"]) ;
//		$formNode->addAttr( "onsubmit", "return false ;") ;
		$formNode->addAttr( "data-wap-confirm-discard", "true") ;
		$tableNode	=	$formNode->addNode( new HTML( "table")) ;
		HTML::create( $_xml, $tableNode, "dataform") ;
		return $baseNode ;
	}
	static	function	realform( $_xml, $_tag, $_attributes) {
		$formNode	=	new self( "form") ;
		$formNode->addAttr( "method", "post") ;
		$formNode->addAttr( "enctype", "multipart/form-data") ;
		if ( isset( $_attributes[ "w"])) {
			$formNode->addAttr( "style", "width: ".$_attributes[ "w"]."px; height: ".$_attributes[ "h"]."") ;
		}
		$baseNode	=	$formNode ;
		$formNode->addAttr( "id", $_attributes[ "id"]) ;
		$formNode->addAttr( "class", "wapForm") ;
		if ( isset( $_attributes["class"])) {
			$formNode->appAttr( "class", $_attributes["class"]) ;
		}
		$formNode->addAttr( "name", $_attributes[ "id"]) ;
		//		$formNode->addAttr( "onsubmit", "return false ;") ;
		$formNode->addAttr( "data-wap-confirm-discard", "true") ;
		$tableNode	=	$formNode->addNode( new HTML( "table")) ;
		HTML::create( $_xml, $tableNode, "realform") ;
		return $baseNode ;
	}
	
	/**
	 *
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	btnnew( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "class", "wapBtn wapBtnNew") ;
		if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		} else {
			//			$btnNode->addAttr( "id", "wapBtnCreate") ;
		}
		if ( isset( $_attributes["data-wap-label"])) {
			$btnNode->addAttr( "value", $_attributes["data-wap-label"]) ;
		} else {
			$btnNode->addAttr( "value", "New") ;
		}
		return $baseNode ;
	}

	/**
	 *
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	btncreate( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "value", "Create") ;
		$btnNode->addAttr( "class", "wapBtn wapBtnCreate") ;
		if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		} else {
//			$btnNode->addAttr( "id", "wapBtnCreate") ;
		}
		if ( isset( $_attributes["data-wap-forms"])) {
			$myForms	=	explode( ",", $_attributes["data-wap-forms"]) ;
			$myVar	=	"" ;
			foreach ( $myForms as $form) {
				if ( $myVar != "")
					$myVar .=	"," ;
				$myVar	.=	"'".$form."'" ;
			}
//			$btnNode->addAttr( "onclick", "screenCurrent.onCreate( true, 'upd', [".$myVar."]) ;") ;
		}
		if ( isset( $_attributes["data-wap-label"])) {
			$btnNode->addAttr( "value", $_attributes["data-wap-label"]) ;
		} else {
			$btnNode->addAttr( "value", FTr::tr( "Create")) ;
		}
		return $baseNode ;
	}
	static	function	btnupdate( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "value", "Update") ;
		$btnNode->addAttr( "class", "wapBtn wapBtnUpdate") ;
		if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		} else {
//			$btnNode->addAttr( "id", "wapBtnUpdate") ;
		}
		if ( isset( $_attributes["data-wap-forms"])) {
			$myForms	=	explode( ",", $_attributes["data-wap-forms"]) ;
			$myVar	=	"" ;
			foreach ( $myForms as $form) {
				if ( $myVar != "")
					$myVar .=	"," ;
				$myVar	.=	"'".$form."'" ;
			}
//			$btnNode->addAttr( "onclick", "screenCurrent.onUpdate( true, 'upd', [".$myVar."]) ;") ;
		}
		$btnNode->addText( FTr::tr( "Update")) ;
		return $baseNode ;
	}

	/**
	 *
	 */
	static	function	btndelete( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "value", "Delete") ;
		$btnNode->addAttr( "class", "wapBtn wapBtnDelete") ;
		if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		} else {
//			$btnNode->addAttr( "id", "wapBtnDelete") ;
		}
//		$btnNode->addAttr( "onclick", "screenCurrent.onDelete( true, 'del', null) ;") ;
		$btnNode->addText( FTr::tr( "Update")) ;
		return $baseNode ;
	}

	/**
	 *
	 */
	static	function	btnmisc( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input", $_attributes) ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "value", $_attributes["data-wap-label"]) ;
		$btnNode->addAttr( "class", "wapBtn wapBtnMisc") ;
		if ( isset( $_attributes[ "x"])) {
			$btnNode->addAttr( "style", "position: absolute; top:	".$_attributes[ "y"]."px; left: ".$_attributes[ "x"]."") ;
		}
		if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		} else {
//			$btnNode->addAttr( "id", "wapBtnMisc") ;
		}
		$val	=	"" ;
		if ( isset( $_attributes["data-wap-val"])) {
			$val	=	 $_attributes["data-wap-val"] ;
		}
		$btnNode->addAttr( "data-wap-misc-function", $_attributes[ "data-wap-misc-function"]) ;
		if ( isset( $_attributes["data-wap-forms"])) {
			$myForms	=	explode( ",", $_attributes["data-wap-forms"]) ;
			$myVar	=	"" ;
			foreach ( $myForms as $form) {
				if ( $myVar != "")
					$myVar .=	"," ;
				$myVar	.=	"'".$form."'" ;
			}
//			$btnNode->addAttr( "onclick", "screenCurrent.onMisc( true, '".$_attributes["data-wap-misc-function"]."', [".$myVar."], '$val') ;") ;
		} else {
//			$btnNode->addAttr( "onclick", "screenCurrent.onMisc( true, '".$_attributes["data-wap-misc-function"]."', null, '$val') ;") ;
		}
		$btnNode->addText( $_attributes["data-wap-label"]) ;
		return $baseNode ;
	}

	/**
	 *
	 */
	static	function	btncancel( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "value", "Cancel") ;
		$btnNode->addAttr( "class", "wapBtn wapBtnCancel") ;
		if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		} else {
//			$btnNode->addAttr( "id", "wapBtnDelete") ;
		}
		$btnNode->addText( FTr::tr( "Cancel")) ;
		return $baseNode ;
	}

	/**
	 *
	 */
	static	function	btnjs( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "value", $_attributes["data-wap-label"]) ;
		$btnNode->addAttr( "class", "wapBtn") ;
			if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		}
		if ( isset( $_attributes["data-wap-forms"])) {
			$myForms	=	explode( ",", $_attributes["data-wap-forms"]) ;
			$myVar	=	"" ;
			foreach ( $myForms as $form) {
				if ( $myVar != "")
					$myVar .=	"," ;
				$myVar	.=	"'".$form."'" ;
			}
			$btnNode->addAttr( "onclick", "screenCurrent.onJS( true, '".$_attributes["data-wap-misc-function"]."', [".$myVar."]) ;") ;
		} else {
			$btnNode->addAttr( "onclick", "screenCurrent.onJS( true, '".$_attributes["data-wap-misc-function"]."', null) ;") ;
		}
		$btnNode->addText( $_attributes["data-wap-label"]) ;
		return $baseNode ;
	}
	/**
	 *
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	btncreatedep( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "value", "Create") ;
		$btnNode->addAttr( "class", "wapBtn") ;
		$btnNode->addAttr( "onclick", "editorCurrent.onCreate( true, 'add', '".$_attributes["form"]."') ;") ;
		if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		}
		$btnNode->addText( "Create") ;
		return $baseNode ;
	}
	static	function	btnupdatedep( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "value", "Update") ;
		$btnNode->addAttr( "class", "wapBtn") ;
		if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		}
		if ( isset( $_attributes["data-wap-forms"])) {
			$myForms	=	explode( ",", $_attributes["data-wap-forms"]) ;
			$myVar	=	"" ;
			foreach ( $myForms as $form) {
				if ( $myVar != "")
					$myVar .=	"," ;
				$myVar	.=	"'".$form."'" ;
			}
			$btnNode->addAttr( "onclick", "editorCurrent.onUpdate( true, 'upd', [".$myVar."]) ;") ;
		}
		$btnNode->addText( "Update") ;
		return $baseNode ;
	}
	static	function	btncanceldep( $_xml, $_tag, $_attributes) {
		$btnNode	=	new self( "input") ;
		$baseNode	=	$btnNode ;
		$btnNode->addAttr( "type", "button") ;
		$btnNode->addAttr( "value", "Cancel") ;
		$btnNode->addAttr( "class", "wapBtn") ;
		if ( isset( $_attributes["id"])) {
			$btnNode->addAttr( "id", $_attributes["id"]) ;
			$btnNode->addAttr( "name", $_attributes["id"]) ;
		}
		$btnNode->addAttr( "onclick", "editorCurrent.onCancel( true, 'cancel', '".$_attributes["form"]."') ;") ;
		$btnNode->addText( "Cancel") ;
		return $baseNode ;
	}
	static	function	canvas( $_xml, $_tag, $_attributes) {
		$canvasNode	=	new self( "canvas") ;
		$canvasNode->addAttr( "style", "border: 1px solid black;") ;
		if ( isset( $_attributes["id"])) {
			$canvasNode->addAttr( "id", $_attributes["id"]) ;
			$canvasNode->addAttr( "name", $_attributes["id"]) ;
		}
		return $canvasNode ;
	}
	/**
	 * create a DataTableView node
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	dtv( $_xml, $_tag, $_attributes) {
		$divNodeContent	=	new HTML( "div") ;
		$baseNode	=	$divNodeContent ;
		$divNodeContent->addAttr( "id", "content") ;
		$divNodeMainData	=	$divNodeContent->addNode( new HTML( "div")) ;
		$divNodeMainData->addAttr( "id", "depdata") ;
		$divTable	=	$divNodeMainData->addNode( new HTML( "div")) ;
		$divTable->addAttr( "id", "Table".$_attributes[ "class"]."Root") ;
		/**
		 *
		 */
		self::$globals["class"]	=	$_attributes[ "class"] ;
		$myTree	=	HTML::createFromFile( "fragDtvTop.xml", null) ;
		$divTable->addNode( $myTree) ;
		/**
		 *
		 */
		$tableNode	=	$divTable->addNode( new HTML( "table")) ;
		$tableNode->addAttr( "id", "Table".$_attributes[ "class"]) ;
		$tableNode->addAttr( "wapClass", $_attributes[ "class"]) ;
		$tableNode->addAttr( "width", "100%") ;
		$tableHead	=	$tableNode->addNode( new HTML( "thead")) ;
		$tableBody	=	$tableNode->addNode( new HTML( "tbody")) ;
		$tableRow	=	$tableHead->addNode( new HTML( "tr")) ;
		$tableRow->addAttr( "wapRowType", "header") ;
		HTML::create( $_xml, $tableRow, "dtv") ;
		/**
		 *
		 */
		$myTree	=	HTML::createFromFile( "fragDtvBot.xml", null) ;
		$divTable->addNode( $myTree) ;
		return $baseNode ;
	}
	static	function	dtvst( $_xml, $_tag, $_attributes) {
		$divNodeContent	=	new HTML( "div") ;
		$baseNode	=	$divNodeContent ;
		$divNodeContent->addAttr( "id", "content") ;
		$divNodeMainData	=	$divNodeContent->addNode( new HTML( "div")) ;
		$divNodeMainData->addAttr( "id", "depdata") ;
		$divTable	=	$divNodeMainData->addNode( new HTML( "div")) ;
		$divTable->addAttr( "id", "Table".$_attributes["wapFnc"]."Root") ;
		/**
		 *
		 */
		$tableNode	=	$divTable->addNode( new HTML( "table")) ;
		$tableNode->addAttr( "id", "Table".$_attributes[ "wapFnc"]) ;
		$tableNode->addAttr( "wapClass", $_attributes[ "wapClass"]) ;
		$tableNode->addAttr( "wapFnc", $_attributes[ "wapFnc"]) ;
		$tableNode->addAttr( "width", "100%") ;
		$tableHead	=	$tableNode->addNode( new HTML( "thead")) ;
		$tableBody	=	$tableNode->addNode( new HTML( "tbody")) ;
		/**
		 *
		 */
		return $baseNode ;
	}
	static	function	dtvcol( $_xml, $_tag, $_attributes) {
		$colNode	=	new HTML( "th") ;
		$baseNode	=	$colNode ;
		if ( isset( $_attributes[ "data-wap-attr"]))
			$colNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		if ( isset( $_attributes[ "data-wap-functions"]))
			$colNode->addAttr( "data-wap-functions", $_attributes[ "data-wap-functions"]) ;
		if ( isset( $_attributes[ "data-wap-sort"]))
			$colNode->addAttrNI( "onClick", "wapGrids['".$_attributes[ "data-wap-sort"]."'].sort( '".$_attributes[ "data-wap-attr"]."') ;") ;
		if ( isset( $_attributes[ "data-wap-functions"])) {
			$colNode->addAttr( "data-wap-functions", $_attributes[ "data-wap-functions"]) ;
			if ( isset( $_attributes[ "data-wap-size"])) {
				$sp	=	explode( ",", $_attributes[ "wapSize"]) ;
				$colNode->addAttr( "data-wap-size", $sp[ 0]) ;
				$colNode->addAttr( "data-wap-max", $sp[ 1]) ;
			}
		}
		$colNode->addText( $_attributes[ "data-wap-label"]) ;
		if ( isset( $_attributes[ "data-wap-sort"])) {
			$myDiv	=	new HTML( "span") ;
			$myDiv->addAttr( "class", "sprite-triangle-1-n") ;
			$colNode->addNode( $myDiv) ;
		}
		return $baseNode ;
	}
	/**
	 * create a DataTableView node
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	grid( $_xml, $_tag, $_attributes) {
		$divNodeContent	=	new HTML( "div") ;
		$baseNode	=	$divNodeContent ;
		$divNodeContent->addAttr( "id", "content") ;
		$divNodeContent->addAttr( "data-wap-type", "grid") ;
		$divNodeMainData	=	$divNodeContent->addNode( new HTML( "div")) ;
		$divNodeMainData->addAttr( "id", "depdata") ;
		$divTable	=	$divNodeMainData->addNode( new HTML( "div")) ;
		$divTable->addAttr( "class", "wapGridDiv") ;
		if ( self::$subClass != null) {
			$divTable->addAttr( "id", "Table".self::$subClass."Root") ;
		} else if ( self::$mainClass != null) {
			$divTable->addAttr( "id", "Table".self::$mainClass."Root") ;
		} else {
			$divTable->addAttr( "id", "Table".$_attributes[ "class"]."Root") ;
		}
		if ( self::$mainClass != null) {
			$divTable->addAttr( "wapClass", self::$mainClass) ;
		}
		if ( self::$moduleName != null) {
			$divTable->addAttr( "wapModule", self::$moduleName) ;
		}
		if ( self::$screenName != null) {
			$divTable->addAttr( "wapScreen", self::$screenName) ;
		}
		/**
		 *
		 */
		self::$globals["class"]	=	$_attributes[ "class"] ;
		if ( isset( $_attributes[ "wapFormName"]))
			self::$globals["wapFormName"]	=	$_attributes[ "wapFormName"] ;
		else
			self::$globals["wapFormName"]	=	$_attributes[ "class"] ;
		$divTable->addAttr( "wapFormName", self::$globals["wapFormName"]) ;
		if ( isset( $_attributes[ "data-wap-grid-name"]))
			self::$globals["data-wap-grid-name"]	=	$_attributes[ "data-wap-grid-name"] ;
		else
			self::$globals["wapFormName"]	=	"grid" + $_attributes[ "class"] ;
		$myTree	=	HTML::createFromFile( "fragGridTop.xml", null) ;
		$divTable->addNode( $myTree) ;
		/**
		 *
		 */
		$tableNode	=	$divTable->addNode( new HTML( "table")) ;
		$tableNode->addAttr( "id", "Table".$_attributes[ "class"]) ;
		$tableNode->addAttr( "data-wap-type", "gridDataTable") ;
		if ( isset( $_attributes[ "wapTableName"])) {
			$tableNode->addAttr( "id", $_attributes[ "wapTableName"]) ;
			$divTable->addAttr( "wapTableName", $_attributes[ "wapTableName"]) ;
		}
		$tableNode->addAttr( "wapClass", $_attributes[ "class"]) ;
		$tableNode->addAttr( "width", "100%") ;
		$tableHead	=	$tableNode->addNode( new HTML( "thead")) ;
		$tableHead->addAttr( "id", $_attributes[ "wapTableName"]."Head") ;
		$tableBody	=	$tableNode->addNode( new HTML( "tbody")) ;
		$tableRow	=	$tableHead->addNode( new HTML( "tr")) ;
		$tableRow->addAttr( "wapRowType", "header") ;
		HTML::create( $_xml, $tableRow, "grid") ;
		/**
		 *
		 */
		if ( isset( $_attributes[ "data-wap-adder"])) {
			$myTree	=	HTML::createFromFile( "fragGridBot.xml", null) ;
			$divTable->addNode( $myTree) ;
		}
		return $baseNode ;
	}
	static	function	gridcol( $_xml, $_tag, $_attributes) {
		$colNode	=	new HTML( "th") ;
		$baseNode	=	$colNode ;
		if ( isset( $_attributes[ "data-wap-attr"])) {
			$colNode->addAttr( "data-wap-attr", $_attributes[ "data-wap-attr"]) ;
		}
		if ( isset( $_attributes[ "data-wap-functions"]))
			$colNode->addAttr( "data-wap-functions", $_attributes[ "data-wap-functions"]) ;
		if ( isset( $_attributes[ "data-wap-link-to"]))
			$colNode->addAttr( "data-wap-link-to", $_attributes[ "data-wap-link-to"]) ;
		if ( isset( $_attributes[ "data-wap-sort-by"])) {
			$colNode->addAttrNI( "onMouseUp", "event.preventDefault() ; wapGrids['".$_attributes[ "data-wap-sort-by"]."'].sort( event, '".$_attributes[ "data-wap-attr"]."', this) ; return false ;") ;
			$colNode->addAttr( "data-wap-sort-by", $_attributes[ "data-wap-sort-by"]) ;
		}
		$colNode->addAttr( "data-wap-group-by", "false") ;
		if ( isset( $_attributes[ "data-wap-group-by"]))
			$colNode->addAttr( "data-wap-group-by", $_attributes[ "data-wap-group-by"]) ;
		$colNode->addAttrNI( "oncontextmenu", "event.preventDefault() ; return false ;") ;
		if ( isset( $_attributes[ "data-wap-functions"])) {
			$colNode->addAttr( "data-wap-functions", $_attributes[ "data-wap-functions"]) ;
			if ( isset( $_attributes[ "wapSize"])) {
				$sp	=	explode( ",", $_attributes[ "wapSize"]) ;
				$colNode->addAttr( "data-wap-size", $sp[ 0]) ;
				$colNode->addAttr( "data-wap-ax", $sp[ 1]) ;
			}
		}
		if ( isset( $_attributes[ "data-wap-can-open"]))
			$colNode->addAttr( "data-wap-can-open", $_attributes[ "data-wap-can-open"]) ;
		$colNode->addText( $_attributes[ "data-wap-label"]) ;
		return $baseNode ;
	}
	static	function	file( $_xml, $_tag, $_attributes) {
		$divNode	=	new HTML( "div") ;
		$divNode->addText( file_get_contents( $_attributes["file"], true)) ;
		return $divNode ;
	}
	/**
	 * internal section
	 */
	/** return a string with all HTML tagged options matching the given criteria
	 * @param unknown $_opt
	 * @param unknown $_indexCol
	 * @param unknown $_optCol
	 * @param string $_crit
	 * @param string $_order
	 * @param string $_def
	 */
	function	_addOptions( $_a) {
		FDbg::begin( 1, "HTMLwap.php", "HTMLwap", "_addOptions( ...)") ;
		$_db		=	$_a["data-wap-db"] ;
		$_indexCol	=	$_a["data-wap-key"] ;
		$_optCol	=	$_a["data-wap-value"] ;
		$_crit		=	$_a["data-wap-cond"] ;
		$_order		=	$_a["data-wap-order"] ;
		$_def		=	$_a["data-wap-def"] ;
		$onClick	=	"" ;
		if ( isset( $_a["onclick"])) {
			$onClick	=	$_a["onclick"] ;
		}
//		$myObj	=	new FDbObject( $_db)  ;
		$myObj	=	new $_db  ;
		$myObj->setIterCond( $_crit) ;
		$myObj->setIterOrder( $_order) ;
		$buffer	=	"" ;
		$lastKey	=	null ;
		foreach ( $myObj as $key => $obj) {
			$addIt	=	false ;
			if ( $lastKey == null) {
				$addIt	=	true ;
			} else if ( $lastKey != $obj->$_indexCol) {
				$addIt	=	true ;
			}
			if ( $addIt) {
				$myOpt	=	$this->addNode( new HTML( "option")) ;
				$myOpt->addAttr( "value", $obj->$_indexCol) ;
				if ( $onClick != "") {
					$myOpt->addAttr( "onclick", $onClick) ;
				}
				$myOpt->addText( $obj->$_optCol) ;
				if ( $obj->$_indexCol == $_def) {
					$myOpt->addAttr( "selected", "1") ;
				}
				$lastKey	=	$obj->$_indexCol ;
			}
		}
		FDbg::end() ;
	}
	/** return a string with all HTML tagged options matching the given criteria
	 * @param unknown $_opt
	 * @param unknown $_indexCol
	 * @param unknown $_optCol
	 * @param string $_crit
	 * @param string $_order
	 * @param string $_def
	 */
	function	_addFlags( $_a) {
		FDbg::begin( 1, "HTMLwap.php", "HTMLwap", "_addFlags( ...)") ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$_wapObj	=	$_a["data-wap-obj"] ;
		else
			$_wapObj	=	"MISSING ATTRIBUTE" ;
		$_wapVar	=	$_a["data-wap-attr"] ;
		if ( isset( $_a["data-wap-mode"])) {
			$_wapMode	=	$_a["data-wap-mode"] ;
		}
		$_db		=	$_a["data-wap-db"] ;
		$_indexCol	=	$_a["data-wap-key"] ;
		$_optCol	=	$_a["data-wap-value"] ;
		$_crit		=	$_a["data-wap-cond"] ;
		$_order		=	$_a["data-wap-order"] ;
		$_def		=	$_a["data-wap-def"] ;
//		$myObj	=	new FDbObject( $_db)  ;
		$myObj	=	new $_db  ;
		$myObj->setIterCond( $_crit) ;
		$myObj->setIterOrder( $_order) ;
		$buffer	=	"" ;
		$randId	=	sprintf( "%05d", rand(0,99999)) ;
		$randName	=	sprintf( "%05d", rand(0,99999)) ;
		foreach ( $myObj as $key => $obj) {
			$myOpt	=	$this->addNode( new HTML( "input")) ;
			$myOpt->close	=	false ;
			$myOpt->addAttr( "id", $_wapVar . "_" . $randId) ;		// need to add random number so "un-group" distinct input groups
			$myOpt->addAttr( "name", $_wapVar . "_" . $randName) ;
			$myOpt->addAttr( "class", "wapField") ;
			$myOpt->addAttr( "type", "radio") ;
			$myOpt->addAttr( "value", $obj->$_indexCol) ;
			$myOpt->addAttr( "data-wap-obj", $_wapObj) ;
			$myOpt->addAttr( "data-wap-attr", $_wapVar) ;
			$myOpt->addAttr( "data-wap-type", "flag") ;
			$myOpt->addAttr( "data-wap-mode", "edit") ;
			if ( $obj->$_indexCol == $_def) {
				$myOpt->addAttr( "checked", "true") ;
			}
			$myOpt->addText( $obj->$_optCol) ;
		}
		FDbg::end() ;
	}
	/** return a string with all HTML tagged options matching the given criteria
	 * @param array	$_a	arguments from XML node
	 */
	function	_addCheck( $_a) {
		FDbg::begin( 1, "HTMLwap.php", "HTMLwap", "_addCheck( ...)") ;
		if ( isset( $_attributes[ "data-wap-obj"]))
			$_wapObj	=	$_a["data-wap-obj"] ;
		else
			$_wapObj	=	"MISSING ATTRIBUTE" ;
		$_wapVar	=	$_a["data-wap-attr"] ;
		if ( isset( $_a["data-wap-mode"])) {
			$_wapMode	=	$_a["data-wap-mode"] ;
		}
		$_db		=	$_a["data-wap-db"] ;
		$_indexCol	=	$_a["data-wap-key"] ;
		$_optCol	=	$_a["data-wap-value"] ;
		$_crit		=	$_a["data-wap-cond"] ;
		$_order		=	$_a["data-wap-order"] ;
		$_def		=	$_a["data-wap-def"] ;
//		$myObj	=	new FDbObject( $_db)  ;
		$myObj	=	new $_db  ;
		$myObj->setIterCond( $_crit) ;
		$myObj->setIterOrder( $_order) ;
		$buffer	=	"" ;
		foreach ( $myObj as $key => $obj) {
			$myOpt	=	$this->addNode( new HTML( "input")) ;
			$myOpt->close	=	false ;
			$myOpt->addAttr( "id", $_wapVar) ;
			$myOpt->addAttr( "name", $_wapVar) ;
			$myOpt->addAttr( "class", "wapField") ;
			$myOpt->addAttr( "type", "checkbox") ;
			$myOpt->addAttr( "value", $obj->$_indexCol) ;
			$myOpt->addAttr( "data-wap-obj", $_wapObj) ;
			$myOpt->addAttr( "data-wap-attr", $_wapVar) ;
			$myOpt->addAttr( "data-wap-type", "check") ;
			$myOpt->addAttr( "data-wap-mode", "edit") ;
			if ( $obj->$_indexCol == $_def) {
				$myOpt->addAttr( "checked", "true") ;
			}
			$myOpt->addText( $obj->$_optCol) ;
			$myOpt->addNode( new HTML( "br")) ;
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	static	function	tabContainer( $_xml, $_tag, $_attributes) {
		$tabContainer	=	new HTML( "div", $_attributes) ;
		$tabContainer->addAttr( "id", $_attributes["id"]) ;
		$tabContainer->addAttr( "class", "wapTabContainer") ;
		/**
		 *
		 */
		$tabControllerList	=	new HTML( "ul") ;
		$tabControllerList->addAttr( "class", "wapTabMenu") ;
		$tabControllerList->addAttr( "data-wap-tab-controller-id", $_attributes["id"]) ;
		$tabContainer->addNode( $tabControllerList) ;
		/**
		 *
		 */
		$tabContentClear	=	new HTML( "div") ;
		$tabContentClear->addAttr( "class", "clear") ;
		$tabContainer->addNode( $tabContentClear) ;
		/**
		 * read and evaluate all tabCOntroller content
		 */
		$tabContent	=	HTML::create( $_xml, $tabContainer, "tabContainer") ;
		/**
		 *
		 */
		$allTabPages	=	$tabContent->getElementsByTagName( "div") ;
		$count	=	sizeof( $allTabPages) ;
		error_log( "COUNT := " . $count) ;
		for ( $il0=0 ; $il0 < $count ; $il0++) {
			if ( strpos( "wapTabContent", $allTabPages[$il0]->attrs[ "class"]) !== false) {
				$tabSelector	=	new HTML( "li") ;
				$tabSelector->addAttr( "id", $allTabPages[$il0]->attrs[ "id"]."Entry") ;
				$tabSelector->addAttr( "data-wap-content-id", $allTabPages[$il0]->attrs[ "id"]) ;
				$tabSelector->addText( $allTabPages[$il0]->attrs[ "data-wap-heading"]) ;
				$tabSelector->addAttr( "class", "wapTabPage") ;
				$tabSelector->addAttr( "onClick", "showTab( '".$allTabPages[$il0]->attrs[ "id"]."Entry') ;") ;
				$tabControllerList->addNode( $tabSelector) ;
			}
		}
		return $tabContainer ;
	}
	static	function	tabContentPane( $_xml, $_tag, $_attributes) {
		$tabContentPane	=	new HTML( "div") ;
		$tabContentPane->addAttr( "id", $_attributes["id"]) ;
		$tabContentPane->addAttr( "class", "wapTabContent") ;
		$tabContentPane->addAttr( "data-wap-heading", $_attributes["data-wap-heading"]) ;
		HTML::create( $_xml, $tabContentPane, "tabContentPane") ;							//		( 8)
		return $tabContentPane ;
	}
	/**
	 * system section
	 */
	/**
	 *
	 * @param string $_tag
	 */
	function	__construct( $_tag) {
		parent::__construct( $_tag) ;
	}
	/**
	 *
	 */
	static	function	module( $_xml, $_tag, $_attributes) {
		if ( isset( $_attributes[ "name"])) {
			self::$moduleName	=	$_attributes[ "name"] ;
		}
		return null ;
	}
	/**
	 *
	 */
	static	function	screen( $_xml, $_tag, $_attributes) {
		if ( isset( $_attributes[ "name"])) {
			self::$screenName	=	$_attributes[ "name"] ;
		}
		return null ;
	}
	/**
	 *
	 */
	static	function	mainClass( $_xml, $_tag, $_attributes) {
		if ( isset( $_attributes[ "name"])) {
			self::$mainClass	=	$_attributes[ "name"] ;
		}
		return null ;
	}
	/**
	 *
	 * @param string $_fnc
	 * @param array $_val
	 * @return HTMLwap
	 */
	static	function	__callStatic( $_fnc, $_val) {
		$_tag	=	$_val[0] ;
		$_attributes	=	$_val[1] ;
		$_ref	=	&$_val[2] ;
		switch ( $_fnc) {
		default	:
			$node	=	new self( "DEFAULT_".$_tag) ;
			$baseNode	=	$node ;
			break ;
		}
		return $baseNode ;
	}
}
/**
 *	wapGrid
 *
 *	Class which creates a wapGrid
 *

+---------------------------------------------------------------------------------------------------------------+
| <div id="<data-wap-grid-name>" class="wapGrid> data-wap-type="wapGrid"								( 1)	|
|	+-----------------------------------------------------------------------------------------------------------+
|	| <div id="<rnd>"class="depdata">																	( 2)	|
|	|	+-------------------------------------------------------------------------------------------------------+
|	|	| <div id="div<data-wap-grid-name>Root" class="wapGridRoot">									( 3)	|
|	|	|	+---------------------------------------------------------------------------------------------------+
|	|	|	| <form id="form<data-wap-grid-name>Top" wapConfirmDiscard="false" class="wapGridFormTop">	( 4)	|
|	|	|	|	+-----------------------------------------------------------------------------------------------+
|	|	|	|	| <table id="table<data-wap-grid-name>Top												( 4a)	|
|	|	|	+---+-----------------------------------------------------------------------------------------------+
|	|	|	| <table id="table<data-wap-grid-name>Data" class="wapGridData" data-wap-type="wapGridData">( 5)	|
|	|	|	|	+-----------------------------------------------------------------------------------------------+
|	|	|	|	| <thead id="table<data-wap-grid-name>Head												( 6)	|
|	|	|	|	+-----------------------------------------------------------------------------------------------+
|	|	|	|	|	| <tr id="<rnd>">																	( 7)	|
|	|	|	|	+-----------------------------------------------------------------------------------------------+
|	|	|	|	|	|	| <th id="<rnd>" onContextMenu="..." data-wap-group-by="false/true" watAttr="<data-wap-attr>	( 8)	|
|	|	|	|	+-----------------------------------------------------------------------------------------------+
|	|	|	|	| <tbody id="<rnd>"	>																	( 9)	|
|	|	|	+---------------------------------------------------------------------------------------------------+
	-----vvvvv-----	OPTIONAL PART	-----
|	|	|	+---------------------------------------------------------------------------------------------------+
|	|	|	| <form id="form<data-wap-grid-name>Bot" wapConfirmDiscard="false" class="wapGridFormBot">	(10)	|
|	|	|	|	+-----------------------------------------------------------------------------------------------+
|	|	|	|	| <table id="table<data-wap-grid-name>Bot												(10a)	|
	-----^^^^^-----	OPTIONAL PART	-----
+---+---+---+---+-----------------------------------------------------------------------------------------------+
 */
class HTMLwapGrid extends HTML	{
	private	static	$wapGridName	=	"" ;
	private	static	$wapClass	=	"" ;
	/**
	 *
	 */
	/**
	 * create a DataTableView node
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	grid( $_xml, $_tag, $_attributes) {
		/**
		 * get the needed attributes
		 */
		self::$wapGridName	=	$_attributes[ "data-wap-grid-name"] ;
		self::$wapClass	=	$_attributes[ "data-wap-object"] ;

		$divNode	=	new HTML( "div", $_attributes) ;									//		( 1)
		$divNode->addAttr( "id", self::$wapGridName) ;
		$divNode->addAttr( "class", "wapGrid") ;
		$divNode->addAttr( "data-wap-type", "wapGrid") ;


		$divNodeClass	=	$divNode->addNode( new HTML( "div")) ;			//		( 2)
		$divNodeClass->addAttr( "class", "depdata") ;

		$divNodeRoot	=	$divNodeClass->addNode( new HTML( "div")) ;		//		( 3)
		$divNodeRoot->addAttr( "id", "div".self::$wapGridName."Root") ;
		$divNodeRoot->addAttr( "class", "wapGridRoot") ;

		if ( isset( $_attributes[ "paginate"]) && $_attributes[ "paginate"] == "false") {
		} else {
			HTMLwapGrid::_gridFormTop( $divNodeRoot) ;
		}

		$tableNodeData	=	$divNodeRoot->addNode( new HTML( "table")) ;	//		( 5)
		$tableNodeData->addAttr( "id", "table".self::$wapGridName."Data") ;
		$tableNodeData->addAttr( "class", "wapGridData") ;
		$tableNodeData->addAttr( "data-wap-type", "wapGrid") ;

		$tableHead	=	$tableNodeData->addNode( new HTML( "thead")) ;			//		( 6)
		$tableRow	=	$tableHead->addNode( new HTML( "tr")) ;				//		( 7)
		$tableRow->addAttr( "data-wap-row-type", "header") ;

		HTML::create( $_xml, $tableRow, "grid") ;							//		( 8)

		$tableBody	=	$tableNodeData->addNode( new HTML( "tbody")) ;			//		( 9)

		HTMLwapGrid::_gridFormBottom( $divNodeRoot) ;
		return $divNode ;
	}
	static	function	gridcol( $_xml, $_tag, $_attributes) {
		$colNode	=	new HTML( "th", $_attributes) ;
		$baseNode	=	$colNode ;
		if ( isset( $_attributes[ "data-wap-sort-by"])) {
			$colNode->addAttrNI( "onMouseUp", "event.preventDefault() ; wapGrids['".$_attributes[ "data-wap-sort-by"]."'].sort( event, '".$_attributes[ "data-wap-attr"]."', this) ; return false ;") ;
			$colNode->addAttr( "data-wap-sort-by", $_attributes[ "data-wap-sort-by"]) ;
		}
		if ( isset( $_attributes[ "data-wap-group-by"])) {
			$colNode->addAttrNI( "oncontextmenu", "event.preventDefault() ; return false ;") ;
			$colNode->addAttr( "data-wap-group-by", $_attributes[ "data-wap-group-by"]) ;
		}
		if ( isset( $_attributes[ "wapSize"])) {
			$sp	=	explode( ",", $_attributes[ "wapSize"]) ;
			$colNode->addAttr( "data-wap-size", $sp[ 0]) ;
			$colNode->addAttr( "data-wap-max", $sp[ 1]) ;
		}
		$colNode->addText( $_attributes[ "data-wap-label"]) ;
		return $baseNode ;
	}
	static	function	_gridFormTop( $_parent) {
		$form	=	$_parent->addNode( new HTML( "div")) ;
		$form->addAttr( "id", "form".self::$wapGridName."Top") ;
		$form->addAttr( "name", "form".self::$wapGridName."Top") ;
		$form->addAttr( "class", "wapForm") ;
		$form->addAttr( "data-wap-confirm-discard", "false") ;
		$form->addAttr( "data-wap-type", "text") ;
		$table	=	$form->addNode( new HTML( "table")) ;
		$table->addAttr( "class", "wapGridFormTop") ;
		$thead	=	$table->addNode( new HTML( "thead")) ;
		$tr		=	$thead->addNode( new HTML( "tr")) ;
		$td		=	$tr->addNode( new HTML( "td")) ;
		$input	=	$td->addNode( new HTML( "input")) ;
		$input->addAttr( "name", "_SStartRow") ;
		$input->addAttr( "class", "wapField") ;
		$input->addAttr( "data-wap-attr", "StartRow") ;
		$input->addAttr( "data-wap-mode", "edit") ;
		$input->addAttr( "data-wap-type", "text") ;
//		$input->addAttr( "oninput", "wapGrids['".self::$wapGridName."']._onRefresh( 'form".self::$wapGridName."Top') ;") ;
		$select	=	$td->addNode( new HTML( "select")) ;
		$select->addAttr( "name", "_SRowCount") ;
		$select->addAttr( "class", "wapField") ;
		$select->addAttr( "data-wap-attr", "RowCount") ;
		$select->addAttr( "data-wap-mode", "edit") ;
		$select->addAttr( "data-wap-type", "option") ;
		$select->addAttr( "onchange", "wapGrids['".self::$wapGridName."']._onRefresh( 'form".self::$wapGridName."Top') ;") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "1"])) ;
		$option->addText( "1") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "2"])) ;
		$option->addText( "2") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "5"])) ;
		$option->addText( "5") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "10"])) ;
		$option->addText( "10") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "20"])) ;
		$option->addText( "20") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "25", "selected" => ""])) ;
		$option->addText( "25") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "50"])) ;
		$option->addText( "50") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "100"])) ;
		$option->addText( "100") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "500"])) ;
		$option->addText( "500") ;
		$option	=	$select->addNode( new HTML( "option", [ "value" => "1000"])) ;
		$option->addText( "1000") ;
		$input	=	$td->addNode( new HTML( "input")) ;
		$input->addAttr( "name", "_TotalRows") ;
		$input->addAttr( "class", "wapField") ;
		$input	=	$td->addNode( new HTML( "input")) ;
		$input->addAttr( "name", "_SortOrder") ;
		$input->addAttr( "class", "wapField") ;
		$input->addAttr( "data-wap-attr", "SortOrder") ;
		$input->addAttr( "data-wap-mode", "edit") ;
		$input->addAttr( "data-wap-type", "text") ;
		$td		=	$tr->addNode( new HTML( "td")) ;
		$input	=	$td->addNode( new HTML( "input")) ;
		$input->addAttr( "name", "Search") ;
		$input->addAttr( "class", "wapField") ;
		$input->addAttr( "data-wap-attr", "Search") ;
		$input->addAttr( "data-wap-mode", "edit") ;
		$input->addAttr( "data-wap-type", "text") ;
		$input->addAttr( "onkeyup", "wapGrids['".self::$wapGridName."'].search( event) ;") ;
		$td		=	$tr->addNode( new HTML( "td")) ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-sleft"])) ;
		$div->addAttr( "onclick", "wapGrids['".self::$wapGridName."']._onFirstPage( 'form".self::$wapGridName."Top') ;") ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-dleft"])) ;
		$div->addAttr( "onclick", "wapGrids['".self::$wapGridName."']._onPreviousPage( 'form".self::$wapGridName."Top') ;") ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-left"])) ;
		$div->addAttr( "onclick", "wapGrids['".self::$wapGridName."']._onOneBackward( 'form".self::$wapGridName."Top') ;") ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-reload"])) ;
		$div->addAttr( "onclick", "wapGrids['".self::$wapGridName."']._onRefresh( 'form".self::$wapGridName."Top') ;") ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-right"])) ;
		$div->addAttr( "onclick", "wapGrids['".self::$wapGridName."']._onOneForward( 'form".self::$wapGridName."Top') ;") ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-dright"])) ;
		$div->addAttr( "onclick", "wapGrids['".self::$wapGridName."']._onNextPage( 'form".self::$wapGridName."Top') ;") ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-sright"])) ;
		$div->addAttr( "onclick", "wapGrids['".self::$wapGridName."']._onLastPage( 'form".self::$wapGridName."Top') ;") ;
	}
	static	function	_gridFormBottom( $_parent) {
		$form	=	$_parent->addNode( new HTML( "div")) ;
		$form->addAttr( "id", "form".self::$wapGridName."Bottom") ;
		$form->addAttr( "name", "form".self::$wapGridName."Bottom") ;
		$form->addAttr( "class", "wapForm") ;
		$form->addAttr( "data-wap-confirm-discard", "false") ;
		$form->addAttr( "data-wap-type", "text") ;
		$table	=	$form->addNode( new HTML( "table")) ;
		$table->addAttr( "class", "wapGridFormBottom") ;
		$thead	=	$table->addNode( new HTML( "thead")) ;
		$tr		=	$thead->addNode( new HTML( "tr")) ;
		$td		=	$tr->addNode( new HTML( "td")) ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-add"])) ;
		$div->addAttr( "onclick", "wapGrids['".self::$wapGridName."'].addItem() ;") ;
	}
}
class HTMLwapTree extends HTML	{
	private	static	$wapTreeName	=	"" ;
	private	static	$wapClass	=	"" ;
	/**
	 *
	 */
	/**
	 * create a DataTableView node
	 * @param unknown $_tag
	 * @param unknown $_attributes
	 * @param unknown $_ref
	 * @return HTMLwap
	 */
	static	function	tree( $_xml, $_tag, $_attributes) {
		/**
		 * get the needed attributes
		 */
		self::$wapTreeName	=	$_attributes[ "data-wap-tree-name"] ;
		self::$wapClass	=	$_attributes[ "data-wap-object"] ;

		$divNode	=	new HTML( "div", $_attributes) ;									//		( 1)
		$divNode->addAttr( "id", self::$wapTreeName) ;
		$divNode->addAttr( "class", "wapTree") ;
		$divNode->addAttr( "data-wap-type", "wapTree") ;

		$divNodeClass	=	$divNode->addNode( new HTML( "div")) ;			//		( 2)
		$divNodeClass->addAttr( "class", "depdata") ;

		$divNodeRoot	=	$divNodeClass->addNode( new HTML( "div")) ;		//		( 3)
		$divNodeRoot->addAttr( "id", "div".self::$wapTreeName."Root") ;
		$divNodeRoot->addAttr( "class", "wapTreeRoot") ;

		HTMLwapTree::_treeFormTop( $divNodeRoot) ;

		$tableNodeData	=	$divNodeRoot->addNode( new HTML( "table")) ;	//		( 5)
		$tableNodeData->addAttr( "id", "table".self::$wapTreeName."Data") ;
		$tableNodeData->addAttr( "class", "waptreedata") ;
		$tableNodeData->addAttr( "data-wap-type", "wapTree") ;

		$tableHead	=	$tableNodeData->addNode( new HTML( "thead")) ;			//		( 6)

		HTML::create( $_xml, $tableHead, "tree") ;							//		( 8)

		$tableBody	=	$tableNodeData->addNode( new HTML( "tbody")) ;			//		( 9)

		HTMLwapTree::_treeFormBottom( $divNodeRoot) ;
		return $divNode ;
	}
	static	function	treerow( $_xml, $_tag, $_attributes) {
		$rowNode	=	new HTML( "tr", $_attributes) ;
		$rowNode->addAttr( "data-wap-row-type", "header") ;
		$baseNode	=	$rowNode ;
		HTML::create( $_xml, $baseNode, "treerow") ;							//		( 8)
		return $baseNode ;
	}
	static	function	treecol( $_xml, $_tag, $_attributes) {
		$colNode	=	new HTML( "th", $_attributes) ;
		$baseNode	=	$colNode ;
		if ( isset( $_attributes[ "wapSize"])) {
			$sp	=	explode( ",", $_attributes[ "wapSize"]) ;
			$colNode->addAttr( "data-wap-size", $sp[ 0]) ;
			$colNode->addAttr( "data-wap-max", $sp[ 1]) ;
		}
		$colNode->addText( $_attributes[ "data-wap-label"]) ;
		return $baseNode ;
	}
	static	function	_treeFormTop( $_parent) {
		$form	=	$_parent->addNode( new HTML( "div")) ;
		$form->addAttr( "id", "form".self::$wapTreeName."Top") ;
		$form->addAttr( "name", "form".self::$wapTreeName."Top") ;
		$form->addAttr( "class", "wapForm") ;
		$form->addAttr( "data-wap-confirm-discard", "false") ;
		$form->addAttr( "data-wap-type", "text") ;
		$table	=	$form->addNode( new HTML( "table")) ;
		$table->addAttr( "class", "wapTreeFormTop") ;
		$thead	=	$table->addNode( new HTML( "thead")) ;
		$tr		=	$thead->addNode( new HTML( "tr")) ;
		$td		=	$tr->addNode( new HTML( "td")) ;
		$input	=	$td->addNode( new HTML( "input")) ;
		$input->addAttr( "name", "Search") ;
		$input->addAttr( "class", "wapField") ;
		$input->addAttr( "data-wap-attr", "Search") ;
		$input->addAttr( "data-wap-mode", "edit") ;
		$input->addAttr( "data-wap-type", "text") ;
		$input->addAttr( "onkeyup", "wapTrees['".self::$wapTreeName."'].search( event) ;") ;
		$td		=	$tr->addNode( new HTML( "td")) ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-reload"])) ;
		$div->addAttr( "onclick", "wapTrees['".self::$wapTreeName."']._onRefresh( 'form".self::$wapTreeName."Top') ;") ;
	}
	static	function	_treeFormBottom( $_parent) {
		$form	=	$_parent->addNode( new HTML( "div")) ;
		$form->addAttr( "id", "form".self::$wapTreeName."Bottom") ;
		$form->addAttr( "name", "form".self::$wapTreeName."Bottom") ;
		$form->addAttr( "class", "wapForm") ;
		$form->addAttr( "data-wap-confirm-discard", "false") ;
		$form->addAttr( "data-wap-type", "text") ;
		$table	=	$form->addNode( new HTML( "table")) ;
		$table->addAttr( "class", "wapTreeFormBottom") ;
		$thead	=	$table->addNode( new HTML( "thead")) ;
		$tr		=	$thead->addNode( new HTML( "tr")) ;
		$td		=	$tr->addNode( new HTML( "td")) ;
		$div	=	$td->addNode( new HTML( "div", [ "class" => "memu-icon sprite-add"])) ;
		$div->addAttr( "onclick", "wapTrees['".self::$wapTreeName."'].addItem() ;") ;
	}
}
?>
