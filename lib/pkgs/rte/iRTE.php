<?php

interface	iRTE {
	public	function	setKey( $_key) ;
	public	function	getKey() ;
	public	function	setId( $_id=-1) ;
	public	function	add( $_key, $_id, $_val) ;
	public	function	upd( $_key, $_id, $_val) ;
	public	function	del( $_key, $_id, $_val) ;
}

?>
