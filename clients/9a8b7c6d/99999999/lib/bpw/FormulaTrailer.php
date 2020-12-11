<?php
class   FormulaTrailer  extends FormulaPhysics {

    function    __construct( $_trailer) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <Trailer>)") ;
        FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "trailerId = " . $_trailer->TrailerId) ;
        $this->trailer  =   $_trailer ;
        $this->detBasicValues() ;
        FDbg::end() ;
    }
}
?>
