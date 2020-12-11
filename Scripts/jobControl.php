#!/usr/bin/php
<?php
/**
 *
 */
error_log( "jobControl.php: starting ...") ;
if ( !isset( $argv[1])) {
	error_log( "jobControl.php: terminating pre-maturely due to missing parameter <cycle> ...") ;
	die() ;
}
$schedule	=	$argv[1] ;
/**
 *
 */

if ( !isset( $argv[2])) {
	$script	=	"%" ;
} else {
	$script	=	$argv[2] ;
}
if ( isset( $argv[3])) {
	$_POST["ClientId"]	=	$argv[3] ;
	if ( isset( $argv[4])) {
		$_POST["ApplicationSystemId"]	=	$argv[4] ;
	} else {
		error_log( "jobControl.php: terminating recursion pre-maturely due to missing parameter(s) ...") ;
		die() ;
	}
	if ( isset( $argv[5])) {
		$_POST["ApplicationId"]	=	$argv[5] ;
	} else {
		error_log( "jobControl.php: terminating recursion pre-maturely due to missing parameter(s) ...") ;
		die() ;
	}
	if ( isset( $argv[6])) {
		$_POST["UserId"]	=	$argv[6] ;
	} else {
		error_log( "jobControl.php: terminating recursion pre-maturely due to missing parameter(s) ...") ;
		die() ;
	}
	if ( isset( $argv[7])) {
		$_POST["Password"]	=	$argv[7] ;
	} else {
		error_log( "jobControl.php: terminating recursion pre-maturely due to missing parameter(s) ...") ;
		die() ;
	}
}
/**
 *
 */
$_SERVER["DOCUMENT_ROOT"]	=	__DIR__ ;
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "MimeMail.php") ;
/**
 *
 */
if ( isset( $_POST["UserId"])) {
//	infoMail2( "khwelter@icloud.com", "Hello, there") ;
//	infoMail2( "lukas.pfenning@hellmig-edv.de", "Hello, there") ;
	error_log( "recursing ... ") ;
	$dateUnix	=	mktime() ;
	$myJob	=	new Jobs() ;
	$myJob->clearIterCond() ;
	$myJob->setIterCond( [ "Schedule = '".$schedule."'", "Status = 0"]) ;
	$myJob->setIterOrder( "Position") ;
	foreach ( $myJob as $actJob) {
		$myJob->Status	=	9 ;
		$myJob->updateColInDb( "Status") ;
		echo( " J.JobName  ....... : '" . $actJob->JobName . "'\n") ;
		echo( "       Command .... : '" . $actJob->Script . "'\n") ;
		$cmd	=	$actJob->Script
			.	" "	.	$schedule
			.	" "	.	"%"
			.	" "	.	$_POST["ClientId"]
			.	" "	.	$_POST["ApplicationSystemId"]
			.	" "	.	$_POST["ApplicationId"]
			.	" "	.	"__jobs__"
			.	" "	.	"__steve__"
			.	" >>"	.	$actJob->Logfile . " 2>&1 " ;
		error_log( "       Command .... : '" . $cmd . "'\n") ;
		$result	=	0 ;
		system( " >" . $actJob->Logfile . " ") ;						// log file loeschen
		system( $cmd, $result) ;	// job ausfuehren und loggen
		echo( "       Result ..... : '" . $result . "'\n") ;
		$myJob->Status	=	0 ;
		$myJob->updateColInDb( "Status") ;
	}
//$myJobIds	=	array() ;
//for ( $myJob->_firstFromDb( $crit) ; $myJob->isValid() ; $myJob->_nextFromDb()) {
//	$myJobIds[]	=	$myJob->Id ;
//}
//foreach ( $myJobIds as $ndx => $jobId) {
//	$myJob->setId( $jobId) ;
//	$startTime	=	mktime() ;
//
//	$myJob->Status	=	9 ;
//	$myJob->updateInDb() ;
//
//	$result	=	0 ;
//	system( " >" . $myJob->Logfile . " ") ;						// log file loeschen
//	system( $myJob->Script . " >>" . $myJob->Logfile . " 2>&1 ", $result) ;	// job ausfuehren und loggen
//
//	infoMail( $myJob->MailRcvr, "Result of ['$myJob->Script':'$myJob->JobName'] run", $myJob->Logfile, "") ;
//
//	$endTime	=	mktime() ;
//
//	$myJob->LastDuration	=	round( ( $endTime - $startTime ) / 60, 0) ;
//	$myJob->LastRun	=	$dateUnix ;
//	$myJob->Status	=	0 ;
//	$myJob->updateInDb() ;
//
} else {
	$myApplicationSystem	=	new FDbObject( "ApplicationSystem", "Id", "sys") ;
	$myApplication	=	new FDbObject( "Application", "Id", "sys") ;
	$myClientApplication	=	new FDbObject( "ClientApplication", "Id", "sys") ;
	$myApplicationSystem->clearIterCond() ;
	foreach ( $myApplicationSystem as $actApplicationSystem) {
error_log( "AS.ApplicationSystem name ....... : " . $actApplicationSystem->Description1) ;
		$myApplication->clearIterCond() ;
		$myApplication->setIterCond( "ApplicationSystemId = '".$actApplicationSystem->ApplicationSystemId."'") ;
		foreach ( $myApplication as $actApplication) {
error_log( "\t A.Application name ..... : " . $actApplication->Description1) ;
			$myClientApplication->clearIterCond() ;
			$myClientApplication->setIterCond( [ "ApplicationSystemId = '".$actApplicationSystem->ApplicationSystemId."'"
									,	"ApplicationId = '".$actApplication->ApplicationId."'"
									,	"UserId = '__jobs__'"
									]) ;
			foreach ( $myClientApplication as $actClientApplication) {
error_log( "\t\tCA.Client UserId ................ : " . $actClientApplication->UserId) ;
				$cmd	=	"./jobControl.php "
					." ".	$schedule
					." ".	$script
					." ".	$actClientApplication->ClientId
					." ".	$actApplicationSystem->ApplicationSystemId
					." ".	$actApplication->ApplicationId
					." ".	"__jobs__"
					." ".	"__steve__"
					;	// job ausfuehren und loggen
error_log( "....> '".$cmd."'\n") ;
				system( $cmd) ;
			}
		}
	}
}
error_log( "jobControl.php: finishing ...") ;
//}

//

exit() ;

//

function	infoMail( $_rcvr, $_subject, $_textFile, $_htmlFile) {

	$newMail	=	new mimeMail( "frieda@hellmig-edv.de",
						$_rcvr,
						"frieda@hellmig-edv.de",
						$_subject,
						"") ;

	$myText	=	new mimeData( "multipart/alternative") ;

	if ( strlen( $_textFile) > 0) {
		$myText->addData( "text/plain", file_get_contents( $_textFile)) ;
	} else {
		$myText->addData( "text/plain", "Kein plain text, siehe HTML fuer weitere Daten ... \n") ;
	}
	if ( strlen( $_htmlFile) > 0) {
		$myText->addData( "text/html", file_get_contents( $_htmlFile)) ;
	} else {
		$myText->addData( "text/html", "<html><body>".file_get_contents( $_textFile)."</body></html>") ;
	}

	$myBody	=	new mimeData( "multipart/mixed") ;
	$myBody->addData( "multipart/mixed", $myText->getAll(), "", true) ;

	$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
	$newMail->send() ;

}
function	infoMail2( $_rcvr, $_subject) {

	$newMail	=	new mimeMail( "frieda@hellmig-edv.de",
						$_rcvr,
						"frieda@hellmig-edv.de",
						$_subject,
						"") ;

	$myText	=	new mimeData( "multipart/alternative") ;

	$myText->addData( "text/plain", "HELLO TEXT\nDo not reply to this automatically generated e-Mail") ;
	$myText->addData( "text/html", "HELLO HTML<br/>Do not reply to this automatically generated e-Mail") ;

	$myBody	=	new mimeData( "multipart/mixed") ;
	$myBody->addData( "multipart/mixed", $myText->getAll(), "", true) ;

	$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
	$newMail->send() ;

}

?>
