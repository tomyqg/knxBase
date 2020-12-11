<?php

$str	=	"Hello_world_123" ;
$parts	=	explode( "_", $str) ;
echo( "'$str' contains '".count($parts)."' parts\n") ;

$str	=	"___" ;
$parts	=	explode( "_", $str) ;
echo( "'$str' contains '".count($parts)."' parts\n") ;

$str	=	"Hello_world_123" ;
$parts	=	explode( "_", $str) ;
echo( "'$str' contains '".count($parts)."' parts\n") ;

?>