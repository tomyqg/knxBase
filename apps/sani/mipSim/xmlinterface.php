<?php
error_log( "starting ...") ;

foreach ( $_GET as $id => $val) {
	if ( $val == "") {
		$cmd	=	$id ;
	}
}

include_once( "ExperimentalBase/expClasses.php");
include_once( "Experimental/expClasses.php");

FDb::registerDb( "localhost:7188", "root", "", "mipsim", "mip", "mysql") ;

$myMIPKostenvoranschlag  =   new MIPKostenvoranschlag() ;

error_log( "Command ... : " . $cmd) ;

switch ( $cmd) {
case 'postKostenvoranschlagData'	:
	$xmlObject	=   new SimpleXMLElement( file_get_contents('php://input')) ;
	$intKvID	=   $xmlObject->kostenvoranschlaege->kostenvoranschlag->intKvID ;
	if ( $myMIPKostenvoranschlag->fetchFromDbWhere( "intKvID = '".$intKvID."' ")) {
		error_log( "der eKV ist bereits eingereicht ...") ;
	} else {
        $myMIPKostenvoranschlag->assignERPNr() ;
		$myMIPKostenvoranschlag->intKvID	=	$intKvID ;
		$myMIPKostenvoranschlag->storeInDb() ;

		$myMIPVersorgung	=	new MIPVersorgung() ;
		$myMIPVersorgung->preset( $myMIPKostenvoranschlag->ERPNr, $xmlObject->kostenvoranschlaege->kostenvoranschlag->versorgungsdaten) ;
		foreach ( $xmlObject->kostenvoranschlaege->kostenvoranschlag->positionen->position as $position) {
			error_log( "......" . $position->hmvz) ;
		}
	}
	break ;
case 'getKostenvoranschlagData'	:
	$xmlObject   =   new SimpleXMLElement( file_get_contents('php://input')) ;
	$od_iKVId  =   $xmlObject->kostenvoranschlaege->kostenvoranschlag->intKvID ;
	if ( $myMIPKostenvoranschlag->setKey( $od_iKVId)) {
	} else {
		error_log( "Dieser eKV wurde noch nicht eingereicht ...") ;
	}
	break ;
case 'getWebKostenvoranschlagData'	:
	break ;
case 'postMIPRequest'	:
	break ;
case 'getDefinitionData'	:
	break ;
case 'getNachrichtenData'	:
	break ;
}
?>
