<?php

/**
 * DocList.php - implements a class to deal with list of documents
 * 
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package Platform
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
/**
 * DocList - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage CuOffr
 */
class	DocList	extends EISSCoreObject {
	function	__construct() {
		$this->valid	=	true ;			// data-mminer is cvalid upon creation
		$this->_valid	=	$this->valid ;
	
	}
	/**
	 * setKey
	 *
	 * Needed in order to conform to the EISS calling standard. Not really used.
	 * Only returns the validity of the current instance, which should usually be true (means: valid)
	 *
	 * @param	string	$_key
	 * @param	int		$_id
	 * @param	mixed	$_val
	 * @return	bool	validity
	 */
	function	setKey() {
		return $this->valid ;
	}
	function	setId() {
		return $this->valid ;
	}
	/**
	 * 
	 */
	static	function	getXMLComplete( $_path, $_ptrn, $_urlPath="") {
		$myFiles	=	DocList::getFileList( $_path, $_ptrn) ;
		$buf	=	"" ;
		$buf	.=	"<TableDoc>" ;
		$buf	.=	"<StartRow>0</StartRow>" ;
		$buf	.=	"<RowCount>10</RowCount>" ;
		$buf	.=	"<PageCount>-1</PageCount>" ;
		$buf	.=	"<TotalRows>0</TotalRows>" ;
		$buf	.=	"<URLPath>".$_urlPath."</URLPath>\n" ;
		foreach ( $myFiles as $file) {
			$buf	.=	"<Doc>\n" ;
			if ( filetype( $_path . $file) == "file") {
				$parts	=	explode( ".", $file) ;
				foreach ( $parts as $value) {
					$myType	=	$value ;
				}
				$buf	.=	"<RefType>FILESYSTEM</RefType>" ;
				$buf	.=	"<RefNr>FILESYSTEM</RefNr>" ;
				$buf	.=	"<Filename>" . $file. "</Filename>\n" ;
				$buf	.=	"<Filetype>" . $myType . "</Filetype>\n" ;
				$buf	.=	"<Filesize>" . filesize( $_path . $file) . "</Filesize>\n" ;
				$buf	.=	"<FileURL>" . $_path . $file . "</FileURL>\n" ;
			}
			$buf	.=	"</Doc>\n" ;
		}
		$buf	.=	"</TableDoc>" ;
		return $buf ;
	}
	/**
	 * return a sorted list of all files matching $_ptrn in the $_path 
	 * @param string $_path
	 * @param string $_ptrn
	 * @return multitype:string
	 */
	static	function	getFileList( $_path, $_ptrn) {
		FDbg::begin( 1, "DocList.php", "DocList", "getFileList( '$_path', '$_ptrn')") ;
		$myDir	=	opendir( $_path) ;
		if ( $myDir) {
			$myFiles	=	array() ;
			while (($file = readdir( $myDir)) !== false) {
				if ( strpos( $file, $_ptrn) !== false) {
					FDbg::trace( 2, "DocList.php", "DocList", "getFileList( ...)", "File: '$file', Pattern: '$_ptrn'") ;
					$myFiles[]	=	$file ;
				}
			}
		} else {
			$e	=	new exception( "DocList.php::DocList::getFileList( ...): can't open directory '$_path'") ;
			error_log( $e) ;
			throw $e ;
		}
		closedir( $myDir);
		reset( $myFiles) ;
		uasort( $myFiles, "myRevSort") ;
		FDbg::begin( 1, "DocList.php", "DocList", "getFileList( '$_path', '$_ptrn')") ;
		return $myFiles ;
	}
}
	function	myRevSort( $_a, $_b) {
		error_log( $_a . " ... " . $_b) ;
		$myA	=	explode( ".", $_a) ;
		$myB	=	explode( ".", $_b) ;
		$myAR	=	explode( "_", $myA[0]) ;
		$myBR	=	explode( "_", $myB[0]) ;
		if ( $myAR[2][0] == "P") {
			$myARL	=	$myAR[2][1] ;
		} else {
			$myARL	=	$myAR[2][0] ;
		}
		if ( $myBR[2][0] == "P") {
			$myBRL	=	$myBR[2][1] ;
		} else {
			$myBRL	=	$myBR[2][0] ;
		}
		error_log( $myARL . " ... " . $myBRL) ;
		if ( $myARL == $myBRL) {
			$myAV	=	substr( $myAR[2], 2) ;
			$myBV	=	substr( $myBR[2], 2) ;
			if ( $myAV < $myBV) {
				return -1 ;
			} else {
				return 1 ;
			}
		} else if ( $myARL < $myBRL) {
			return -1 ;
		} else {
			return 1 ;
		}
	}