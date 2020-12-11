<?php
/**
 * Description:	interface between class methods
 *
 * dispatchXML.php is the only receiving php script. All requests towards objects need to
 * go through this particular script.
 * 
 * dispatchXML will analyze the complete URL, extract GET variables and use these to determine:
 * 
 * class	:=	the class to act on
 * key		:=	the key of the object to work on, this is a GET parameter either:
 * 					key - in which case the key comprises only a single attribute - or
 * 					key0..key9 - in which case the key comprises multiple attributes.
 * 					this key must ALWAYS address a single instance in the database only.
 * 					if multiple objects exist there is a fault within the calling instance!
 * id		:=	the id of the object to work on.
 * value	:=	an additional value important to the call
 * 
 * All application level methods of application classes MUST adhere to the calling standard:
 * 		method( $_key, $_id, $_value, $_reply=null)
 * and return a Reply-object.
 * 
 *
 *
 *
 */
?>
