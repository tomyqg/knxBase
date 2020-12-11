<?php

$pathC	=	"../phpconfig" ;
$pathI	=	"../phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

require_once( "config.inc.php") ;

//

$newMail	=	new mimeMail( "khw@wimtecc.de",
					"khw@wimtecc.de",
					"khw@wimtecc.de",
					"Daily Testmail - bitte ignorieren",
					"") ;

$myText	=	new mimeData( "multipart/alternative") ;
$myText->addData( "text/plain", "(1) Hello, world \n") ;
$myText->addData( "text/html", "<html><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">(2)Hello, world</body></html>\n") ;

$myBody	=	new mimeData( "multipart/mixed") ;
$myBody->addData( "multipart/mixed", $myText->getAll()) ;
//$myBody->addData( "application/pdf", $myConfig->path->Archive . "Mahnungen/000001.pdf", "Erinnerung.pdf", true) ;

$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
$newMail->send() ;

exit( 0) ;

?>
