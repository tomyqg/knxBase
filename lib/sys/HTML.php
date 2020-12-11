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
class	HTML	extends	EISSCoreObject	{
	static	$rootNode	=	null ;
	static	$currNode	=	null ;
	static	$nest	=	0 ;
	static	$myRoot ;
	static	$level	=	0 ;
	static	$idNdx	=	0 ;
	var	$tag	=	array() ;
	var	$attrs	=	array() ;
	var	$nodes	=	array() ;
	var	$root	=	null ;
	var	$close	=	false ;
	/**
	 *
	 * @param unknown $_tag
	 */
	function	__construct( $_tag, $_attrs=null) {
		FDbg::begin( 1, "HTML.php", "HTML", "__construct( '$_tag', '".print_r( $_attrs, true)."')") ;
		$this->tag	=	$_tag ;
//		$this->addAttr( "id", md5( date( DATE_RSS).sprintf( "%08d", rand(0,99999)))) ;
		$this->id	=	sprintf( "%010d", rand(0,999999999)) ;
		$this->addAttr( "id", $this->id) ;
		if ( $_attrs != null) {
			foreach ( $_attrs as $key => $val) {
				$this->addAttr( $key, $val) ;
			}
		}
		switch ( $_tag) {
		case	"br"	:
		case	"input"	:
			$this->close	=	true ;
			break ;
		}
		FDbg::end() ;
	}
	function	addAttr( $_attr, $_value) {
		switch ( $_attr) {
		case	"title"	:
			$this->attrs[ $_attr]	=	FTr::tr( $_value) ;
			break ;
		default	:
			$this->attrs[ $_attr]	=	$this->interpret( $_value) ;
			break ;
		}
	}
	function	addAttrNI( $_attr, $_value) {
			$this->attrs[ $_attr]	=	FTr::tr( $_value) ;
	}
	function	appAttr( $_attr, $_value, $_sc=" ") {
		if ( $this->attrs[ $_attr]) {
			$this->attrs[ $_attr]	.=	$_sc . $this->interpret( $_value) ;
		} else {
			$this->addAttr( $_attr, $this->interpret( $_value)) ;
		}
	}
	function	addNode( $_node) {
		$this->nodes[]	=	$_node ;
		$_node->root	=	$this ;
		return $_node ;
	}
	function	addText( $_text) {
		$this->nodes[]	=	$_text ;
	}
	function	getElementsByTagName( $_tag) {
		$result	=	array() ;
		foreach ( $this->nodes as $node) {
//			error_log( $node->id) ;
			if ( $node->tag == $_tag) {
				$result[]	=	$node ;
			}
		}
		return $result ;
	}
	function	__toString() {
		$buf	=	str_repeat( "\t", self::$nest) . "<" . $this->tag ;
		foreach ( $this->attrs as $attr => $val) {
			$buf	.=	" " . $attr . "=\"".$val."\"" ;
		}
		if ( $this->close) {
			$buf	.=	" />\n" ;
		} else {
			$buf	.=	">\n" ;
			self::$nest++ ;
			foreach ( $this->nodes as $node) {
				$buf	.=	$node ;
			}
			self::$nest-- ;
			$buf	.=	str_repeat( "\t", self::$nest) . "</".$this->tag.">\n" ;
		}
		return $this->interpret( $buf) ;
		return $buf ;
	}
	/**
	 *
	 */
	static	function	create( $_xml, $_parent="null", $_endTag="html") {
		FDbg::begin( 1, "HTML.php", "HTML", "create( '<_xml>', <HTML>. '$_endTag')") ;
		self::$level++ ;
		$myRoot	=	null ;
		$attributes	=	array() ;
		$end	=	false ;
		$currNode	=	$_parent ;
//			error_log( "should add this text, too ................................:   " . trim( $_xml->value, "\n\t")) ;
		while ( $_xml->read() && ! $end) {
//			echo sprintf( "%03d:", self::$level) . "reading node " . $_xml->name . "\n" ;
//			error_log( "...........................>>>>>>>>>>>>>>>>>>>>>>>>>".$_xml->name.".>.".$_xml->nodeType) ;
			switch ( $_xml->nodeType) {
			case	XmlReader::ELEMENT	:			// start element
				/**
				 * disect the tag name for namespace:tag
				 */
				$v	=	explode( ":", $_xml->name) ;
				if ( isset( $v[1])) {
					$ns	=	$v[0] ;
					$tag	=	$v[1] ;
				} else {
//					echo "defaulting to namespace html\n" ;
					$ns	=	"html" ;
					$tag	=	$v[0] ;
				}
				while ( $_xml->moveToNextAttribute()) {
					$attributes[ $_xml->name]	=	$_xml->value ;
				}
				$_xml->moveToElement() ;
				/**
				 * perform action depending on namespace
				 */
				switch ( $ns) {
				case	"html"	:
					$class	=	"HTML" ;
					if ( $_xml->isEmptyElement) {
						$end	=	true ;
					}
					$newNode	=	new $class( $tag, $attributes) ;
					HTML::create( $_xml, $newNode, $tag) ;
					/**
					 * in case we are on the root node, i.e. currNode === null, do not verify any access rights;
					 */
					if ( $currNode == null) {
//						echo "drilling down ...\n" ;
						$currNode	=	$newNode ;
					} else {
						/**
						 * verify access rights and return null in case access not granted
						 */
						$appUser	=	EISSCoreObject::__getAppUser() ;
						if ( $appUser && isset( $attributes[ "data-wap-auth-object"])) {
							if ( ! $appUser->isGranted( "scr", HTMLwap::moduleName().".".HTMLwap::screenName().".".$attributes[ "wap-auth-object"], "", true)) {
								$newNode	=	null ;
							}
						}
						if ( $newNode != null) {
							$currNode->addNode( $newNode) ;
						}
					}
					break ;
				case	"dojo"	:
					$class	=	"HTMLdojo" ;
					if ( $_xml->isEmptyElement) {
						$end	=	true ;
					}
					$newNode	=	HTMLdojo::$tag( $_xml, $tag, $attributes) ;
					$currNode->addNode( $newNode) ;
					break ;
				case	"wap"	:
					if ( $tag == "grid" || $tag == "gridcol") {
						$class	=	"HTMLwapGrid" ;
						$newNode	=	HTMLwapGrid::$tag( $_xml, $tag, $attributes) ;
					} else if ( $tag == "tree" || $tag == "treerow" || $tag == "treecol") {
						$class	=	"HTMLwapTree" ;
						$newNode	=	HTMLwapTree::$tag( $_xml, $tag, $attributes) ;
					} else if ( $tag == "plain") {
						$currNode->addText( $_xml->readInnerXML()) ;
						$newNode	=	null ;
						$_xml->next() ;
					} else {
						$class	=	"HTMLwap" ;
						$newNode	=	HTMLwap::$tag( $_xml, $tag, $attributes) ;
					}
					/**
					 * verify access rights and "null" the newNode in case access is not granted
					 */
					$appUser	=	EISSCoreObject::__getAppUser() ;
					if ( $appUser && isset( $attributes[ "data-wap-auth-object"])) {
						if ( ! $appUser->isGranted( "scr", HTMLwap::moduleName().".".HTMLwap::screenName().".".$attributes[ "data-wap-auth-object"], "", true)) {
							$newNode	=	null ;
						}
					}
					if ( $newNode != null) {
						$currNode->addNode( $newNode) ;
					}
					break ;
				default	:
					break ;
				}
				foreach ( $attributes as $ndx => $val) {
					unset( $attributes[ $ndx]) ;
				}
				break ;
			case	XmlReader::TEXT	:			// text node
				$text	=	trim( $_xml->value, "\n\t") ;
				$currNode->addText( $text) ;
				break ;
			case	XmlReader::CDATA	:
				$myCData	=	$xml->value ;
				break ;
			case	XmlReader::SIGNIFICANT_WHITESPACE	:			// whitespace node
				break ;
			case	XmlReader::END_ELEMENT	:			// end element
				/**
				 * disect the tag name for namespace:tag
				 */
				$v	=	explode( ":", $_xml->name) ;
				if ( isset( $v[1])) {
					$ns	=	$v[0] ;
					$tag	=	$v[1] ;
				} else {
					$ns	=	"html" ;
					$tag	=	$v[0] ;
				}
//				echo "comparing tag := '" . $tag . "' against _endTag := '" . $_endTag . "' \n" ;
				if ( $tag == $_endTag)
					$end	=	true ;
				break ;
			default	:
				break ;
			}
		}
		if ( ! $end) {
//			echo "exiting due to end of xml\n" ;
			$end	=	true ;
		}
		self::$level-- ;
		FDbg::end() ;
		return $currNode ;
	}

	/**
	 *
	 */
	static	function	createFromFile( $_fileName, $_parent) {
//		$file	=	fopen( $_fileName, "r") ;
//		if ( $file) {
//			$xmlText	=	fread( $file, 65535) ;
//			fclose( $file) ;
//		} else {
//			echo "no valid xml-file\n" ;
//			die() ;
//		}
		$xmlText	=	file_get_contents( $_fileName, true) ;
		/**
		 * create XML reader, assign text, create HTML tree and output it
		 */
		$xml	=	new XmlReader() ;
		$xml->XML( $xmlText) ;
		$newTree	=	HTML::create( $xml, $_parent) ;
		return $newTree->nodes[0] ;
	}
}
?>
