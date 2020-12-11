<?php
// contains utility functions mb_stripos_all() and apply_highlight()
$debugBoot	=	true ;
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;

// prevent direct access
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
  $user_error = 'Access denied - not an AJAX request...';
  trigger_error($user_error, E_USER_ERROR);
}

// get what user typed in autocomplete input
$term = trim($_GET['term']);

$a_json = array();
$a_json_row = array();

$a_json_invalid = array(array("id" => "#", "value" => $term, "label" => "Only letters and digits are permitted..."));
$json_invalid = json_encode($a_json_invalid);

// replace multiple spaces with one
//$term = preg_replace('/\s+/', ' ', $term);

// SECURITY HOLE ***************************************************************
// allow space, any unicode letter and digit, underscore and dash
//if(preg_match("/[^\040\pL\pN_-]/u", $term)) {
//  print $json_invalid;
//  exit;
//}
// *****************************************************************************
error_log( "Hello, world .......................................:: '$term'") ;
$myKunde	=	new Kunde() ;
$myKunde->setIterCond( "Vorname like '%{$term}%' OR Name1 like '%{$term}%' ") ;
$il0	=	0 ;
foreach ( $myKunde as $kunde) {
	if ( $il0 < 50) {
		$a_json_row["id"]		=	$kunde->Id ;
		$a_json_row["value"]	=	$kunde->Vorname ;
		$a_json_row["label"]	=	$kunde->KundeNr . " " . $kunde->Vorname . " " . $kunde->Name1 ;
		$a_json_row["kundeNr"]	=	$kunde->KundeNr ;
		$a_json_row["Vorname"]	=	$kunde->Vorname ;
		$a_json_row["Name1"]	=	$kunde->Name1 ;
		array_push( $a_json, $a_json_row);
	}
	$il0++ ;
}

$json = json_encode($a_json);
error_log( $json) ;
print $json;
?>
