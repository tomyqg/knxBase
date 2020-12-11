<?php
/**
 * FException
 */
class	FException	extends	Exception	{
	protected	$file ;
	protected	$module ;
	protected	$method ;
	protected	$message ;
	public	function	__construct( $_file, $_module, $_method, $_mesg, $_code=0, $_ec1=-1, $_ec2=-1) {
		parent::__construct( $_mesg, $_code) ;
		$this->file	=	$_file ;
		$this->module	=	$_module ;
		$this->method	=	$_method ;
		$this->ec1	=	$_ec1 ;
		$this->ec2	=	$_ec2 ;
		$this->message	=	$_file . "::" . $_module . "::" . $_method .": " . $_mesg ;
		error_log( ">>>>> " . $this->message) ;
	}
	public	function	__toString() {
		return $this->message ;
	}
}

try {
        throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)", "my exception") ;
} catch (FException $e) {
    echo "FException ... $e\n" ;
} catch (Exception $e) {
    echo "regular Exception ... $e\n" ;
}
?>
