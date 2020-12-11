<?php
interface	iPay	{
	/**
	 * 
	 */
	const	payStatOk					=	 0 ;		// payment ok, ship order out
	const	payStatPending				=	 1 ;
	const	payStatRefused				=	 2 ;
	const	payStatCancelledByUser		=	 3 ;
}
?>