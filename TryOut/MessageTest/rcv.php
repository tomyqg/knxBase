<?php
$myQueue	=	null ;
$myQueueKey	=	"12345" ;
$myQueue	=	msg_get_queue( $myQueueKey) ;
$message	=	"" ;
$sleep	=	1 ;
$maxSleep	=	5 ;
while ( 1) {
	/**
	 *
	 */
	$myQueueInfo	=	msg_stat_queue( $myQueue) ;
	echo "Messages in queue..............: " . $myQueueInfo[ msg_qnum] . "\n" ;
	echo "Max. bytes in queue............: " . $myQueueInfo[ msg_qbytes] . "\n" ;
	if ( msg_receive( $myQueue, 1, $rcvdMsgType, 1024, $message, false, MSG_IPC_NOWAIT)) {
		$sleep	=	1 ;
		echo "received a message at priority 1\n" ;
		echo "Message content.......: '" . $message . "\n" ;
	} else if ( msg_receive( $myQueue, 2, $rcvdMsgType, 1024, $message, false, MSG_IPC_NOWAIT)) {
		$sleep	=	1 ;
		echo "received a message at priority 2\n" ;
	} else if ( msg_receive( $myQueue, 3, $rcvdMsgType, 1024, $message, false, MSG_IPC_NOWAIT)) {
		$sleep	=	1 ;
		echo "received a message at priority 3\n" ;
	} else {
		echo "Sleeping for " . $sleep . " seconds ... \n" ;
		sleep( $sleep++) ;
	}
	( $sleep > $maxSleep ? $sleep = $maxSleep : "") ;
}
?>
