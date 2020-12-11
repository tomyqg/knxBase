<?php

/**
 * Class _FibuLog
 */
class   MIPZuzahlung  extends FDbObject {

    /**
     * @var array   tranlation/mapping table for translating selfexplaining database attributes to real world shit
     */

    /**
     * _FibuLog constructor.
     */
    public function __construct() {
        parent::__construct( "zuzahlung", "id", "mip") ;
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
