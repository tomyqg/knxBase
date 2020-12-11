<?php

class	Fields	{

	private	static	$rLength	=	array(
						"_HId"		=>   8,
						"_IArtikelNr"		=>   20,
						"_IArtikelBez1"		=>	 64,
						"_IArtikelBez2"		=>	 64,
						"_IeMail"			=>	 64,
						"_IEMail"			=>	 64,
						"_IPDFFile"			=>	 64
					) ;

	function	getSize( $_fieldName) {
		if ( isset( self::$rLength[ $_fieldName])) {
			return self::$rLength[ $_fieldName] ;
		} else {
			return 0 ;
		}
	}

}

?>
