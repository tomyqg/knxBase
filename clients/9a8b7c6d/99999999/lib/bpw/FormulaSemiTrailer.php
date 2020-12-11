<?php
/**
 * FormulaECESemiTrailer
 *
 *
 */
class   FormulaSemiTrailer  extends FormulaTrailer {
    function    __construct( $_trailer) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <Trailer>)") ;
        FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "trailerId = " . $_trailer->TrailerId) ;
        parent::__construct( $_trailer) ;
        /**
         * calculate dynamic axle load movement (dynamische Achslastverlagerung)
         */
         FDbg::end() ;
    }
    /**
     *
     */
    function    detBasicValues() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( void)") ;
        $trailer    =   $this->trailer ;
        /**
         * determine the total load on the axle-group
         */
        $this->TrailerAxle	=	new TrailerAxle() ;
        $this->TrailerAxle->setIterCond( "TrailerId = '".$trailer->TrailerId."' AND AxleGroupNo = 2 ") ;
        $trailer->rearWeightEmpty	=	0 ;
        $trailer->rearWeightLoaded	=	0 ;
        FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "iterating axles; trailerId = " . $trailer->TrailerId) ;
        foreach ( $this->TrailerAxle as $axle) {
            FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "...") ;
            $trailer->rearWeightEmpty	+=	$axle->WeightEmpty ;
            $trailer->rearWeightLoaded	+=	$axle->WeightLoaded ;
        }
        FDbg::end() ;
    }
    /**
     * Required Brake Torque (erforderliches Bremsmoment)
     *
     */
    function    detRequiredBrakeTorque( $_desiredBraking=0.55) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_desiredBraking)") ;
        /**
         *
         */

        FDbg::end() ;
    }
}
?>
