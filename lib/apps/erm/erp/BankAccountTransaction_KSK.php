<?php
/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 *	Modul
 */
class	BankAccountTransaction_KSK	extends	BankAccountTransaction	{
	/**
	 * 
	 */
	function	__construct( $_key=-1) {
		parent::__construct( "BankAccountTransaction", "Id") ;
		if ( $_id >= 0) {
			$this->setId( $_id) ;
		} else {
		}
	}
	/**
	 * 
	 */
	public	function	fromFile( $_ERPNo, $_accountNo, $_file) {
		/**
		 *	IF moving uploaded file ok, proceed with reading the XML content
		 *	ELSE
		 */
		$this->ERPNo	=	$_ERPNo ;
		$buffer	=	"" ;
		$text	=	"" ;
		$myFile	=	fopen( $_file, "r") ;
		$myXMLText	=	fread( $myFile, 32768) ;
		fclose( $myFile) ;
		if ( strlen( $myXMLText) == 0) {
			$buffer	=	"XML File is empty" ;
			return $buffer ;
		}
		$xml	=	new XMLReader() ;
		$xml->XML( $myXMLText) ;
		while ( $xml->read()) {
			switch ( $xml->nodeType) {
				case	XMLReader::ELEMENT	:			// start element
					switch ( $xml->name) {
						case	"Umsatz"	:
							break ;
					}
				case	XMLReader::TEXT	:			// text node
					$text	.=	trim( $xml->value, "\n\t") ;
					break ;
				case	XMLReader::CDATA	:
					$myCData	=	$xml->value ;
					break ;
				case	XMLReader::WHITESPACE	:			// whitespace node
					break ;
				case	XMLReader::END_ELEMENT	:			// end element
					switch ( $xml->name) {
						case "Umsatz"	:
							$this->storeInDb() ;
							$this->book( $_accountNo) ;
							break ;
						case	"Kontonummer"	:
							$this->AccountNoOwn	=	$text ;
							break ;
						case	"Datum"	:
							$this->Date	=	substr( $text, 6, 4) . "-" . substr( $text, 3, 2) . "-" . substr( $text, 0, 2) ;
							break ;
						case	"Valuta"	:
							$this->DateValidity	=	substr( $text, 6, 4) . "-" . substr( $text, 3, 2) . "-" . substr( $text, 0, 2) ;
							break ;
						case	"Geschaeftsvorfall"	:
							$this->Action	=	$text ;
							break ;
						case	"Verwendungszweck"	:
							$this->Description	=	$text ;
							break ;
						case	"Betrag"	:
							/**
							 * strip "." character (thousand seperator) and replace "," with "." (decimal separator)
							 */
							$this->Amount	=	str_replace( ",", ".", str_replace( ".", "",$text))  ;
							break ;
						case	"Name"	:
						case	"Auszugsnummer"	:
							$this->StatementNo	=	$text ;
							break ;
					}
					$text	=	"" ;
					break ;
			}
		}
	}
}
?>
