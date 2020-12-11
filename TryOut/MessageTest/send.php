<?php
$myQueue	=	null ;
$myQueueKey	=	"12345" ;
$myQueue	=	msg_get_queue( $myQueueKey) ;
$message	=	"" ;
msg_send( $myQueue, 2, "Hello, world ... ") ;
?>
