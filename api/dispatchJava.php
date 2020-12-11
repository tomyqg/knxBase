<?php
/**
 * Created by PhpStorm.
 * User: miskhwe
 * Date: 02.03.18
 * Time: 21:22
 */

$reply  =   "<xml>\n" ;

foreach ( $_GET as $key => $val) {
	$reply  .=  "<$key>$val</$key>\n" ;
}

$reply  .=   "</xml>\n" ;

error_log( $reply) ;

echo $reply ;

?>