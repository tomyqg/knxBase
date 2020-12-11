#!/usr/bin/php
<?php

$pathC	=	"../../phpconfig" ;
$pathI	=	"../../phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;

require_once( "config.inc.php") ;
require_once( "base/DbObject.php") ;

FDbg::setLevel( 0xffffffff) ;
FDbg::enable() ;

/**
 * 
 */
DbObject::showDict() ;
?>