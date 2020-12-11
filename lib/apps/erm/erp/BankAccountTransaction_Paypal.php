<?php
/**
 * BankAccountTransaction_Paypal.php Base class for PDF-format printed matters
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
class	BankAccountTransaction_Paypal	extends	BankAccountTransaction	{
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
		$myCSVFile	=	fopen( $_file, "r") ;
		while ( ( $myFields = fgetcsv( $myCSVFile)) !== FALSE) {
			if ( $myFields[0] != "Datum") {
				
				error_log( iconv( 'windows-1252//TRANSLIT', 'UTF-8', $myFields[4])) ;
				$this->Date	=	substr( $myFields[0], 6, 4) . "-" . substr( $myFields[0], 3, 2) . "-" . substr( $myFields[0], 0, 2) ;
				$this->DateValidity	=	substr( $myFields[0], 6, 4) . "-" . substr( $myFields[0], 3, 2) . "-" . substr( $myFields[0], 0, 2) ;
				$this->Action	=	iconv( 'windows-1252//TRANSLIT', 'UTF-8', $myFields[3]) ;
				$this->Description	=	iconv( 'windows-1252//TRANSLIT', 'UTF-8', $myFields[4]) ;
				/**
				 * strip "." character (thousand seperator) and replace "," with "." (decimal separator)
				 */
				$this->Amount	=	str_replace( ",", ".", str_replace( ".", "", $myFields[7]))  ;
				$this->Fee		=	str_replace( ",", ".", str_replace( ".", "", $myFields[8]))  ;
				$this->StatementNo	=	"" ;
				$this->TransferStatus	=	$myFields[5] ;
				$this->storeInDb() ;
				$this->book( $_accountNo) ;
			}
		}
		fclose( $myCSVFile) ;
	}
}
?>
