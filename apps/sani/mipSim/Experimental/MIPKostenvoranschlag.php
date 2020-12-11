<?php

/**
 * Class _FibuLog
 */
class   MIPKostenvoranschlag	extends FDbObject {

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */

    /**
     * _FibuLog constructor.
     */
    public function __construct() {
        parent::__construct( "kostenvoranschlag", "ERPNr", "mip") ;
    }

	/**
	 *
	 */
	public	function	assignERPNr() {
	    $temp   =   new MIPKostenvoranschlag() ;
		if ( $temp->first( "1 = 1", "ERPNr DESC")) {
            $this->ERPNr    =   sprintf( "%012d", ( intval($temp->ERPNr, 10) + 1)) ;
        } else {
            $this->ERPNr    =   sprintf( "%012d", ( intval( "001000000000", 10) + 1)) ;
        }
	}
    /**
     * @param string $_le
     * @return  void
     */
    public function    __dump( $_le="\n") {
        error_log( parent::__dump( $_le)) ;
    }

    /**
     *
     */
    public function    _postInstantiate() {

    }

     /**
      *
      */
    public function    _postLoad() {

    }
}
