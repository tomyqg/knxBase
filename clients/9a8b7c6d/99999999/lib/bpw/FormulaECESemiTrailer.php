<?php
/**
 * FormulaECESemiTrailer
 *
 *
 */
class   FormulaECESemiTrailer  extends FormulaSemiTrailer {
    function    __construct( $_trailer) {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <Trailer>)") ;
        FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( void)", "trailerId = " . $_trailer->TrailerId) ;
        parent::__construct( $_trailer) ;
        FDbg::end() ;
    }
    function    detBasicValues() {
        FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( void)") ;
        /**
         * determine basic values which are comon to all Semi-Trailers
         */
        parent::detBasicValues() ;
        $trailer    =   $this->trailer ;
        error_log( "min. wheelbase cases ...: ") ;
        $delta_P_z	=	0.55 / ( $trailer->WheelbaseMin + 0.55 * $trailer->HeightKingpin) * ( $trailer->TotalWeightEmpty * ( $trailer->CoGMin - 950) + $trailer->rearWeightEmpty * $trailer->HeightKingpin) ;
        error_log( "delta_P_z := " . $delta_P_z) ;
        $delta_P_z	=	0.55 / ( $trailer->WheelbaseMin + 0.55 * $trailer->HeightKingpin) * ( $trailer->TotalWeightEmpty * ( $trailer->CoGMax - 950) + $trailer->rearWeightEmpty * $trailer->HeightKingpin) ;
        error_log( "delta_P_z := " . $delta_P_z) ;
        $delta_P_z	=	0.55 / ( $trailer->WheelbaseMin + 0.55 * $trailer->HeightKingpin) * ( $trailer->TotalWeightLoaded * ( $trailer->CoGMin - 950) + $trailer->rearWeightLoaded * $trailer->HeightKingpin) ;
        error_log( "delta_P_z := " . $delta_P_z) ;
        $delta_P_z	=	0.55 / ( $trailer->WheelbaseMin + 0.55 * $trailer->HeightKingpin) * ( $trailer->TotalWeightLoaded * ( $trailer->CoGMax - 950) + $trailer->rearWeightLoaded * $trailer->HeightKingpin) ;
        error_log( "delta_P_z := " . $delta_P_z) ;

        error_log( "max. wheelbase cases ...: ") ;
        $delta_P_z	=	0.55 / ( $trailer->WheelbaseMax + 0.55 * $trailer->HeightKingpin) * ( $trailer->TotalWeightEmpty * ( $trailer->CoGMin - 950) + $trailer->rearWeightEmpty * $trailer->HeightKingpin) ;
        error_log( "delta_P_z := " . $delta_P_z) ;
        $delta_P_z	=	0.55 / ( $trailer->WheelbaseMax + 0.55 * $trailer->HeightKingpin) * ( $trailer->TotalWeightEmpty * ( $trailer->CoGMax - 950) + $trailer->rearWeightEmpty * $trailer->HeightKingpin) ;
        error_log( "delta_P_z := " . $delta_P_z) ;
        $delta_P_z	=	0.55 / ( $trailer->WheelbaseMax + 0.55 * $trailer->HeightKingpin) * ( $trailer->TotalWeightLoaded * ( $trailer->CoGMin - 950) + $trailer->rearWeightLoaded * $trailer->HeightKingpin) ;
        error_log( "delta_P_z := " . $delta_P_z) ;
        $delta_P_z	=	0.55 / ( $trailer->WheelbaseMax + 0.55 * $trailer->HeightKingpin) * ( $trailer->TotalWeightLoaded * ( $trailer->CoGMax - 950) + $trailer->rearWeightLoaded * $trailer->HeightKingpin) ;
        error_log( "delta_P_z := " . $delta_P_z) ;

        error_log( "dynamic axle loads ...: ") ;
        $this->TrailerAxle	=	new TrailerAxle() ;
        $this->TrailerAxle->setIterCond( "TrailerId = '".$this->trailer->TrailerId."' AND AxleGroupNo = 2 ") ;
        foreach ( $this->TrailerAxle as $axle) {
            $axle->P_dyn	=	$axle->WeightLoaded - $delta_P_z / $trailer->TrailerConfig->AxlesRear ;
            error_log( "delta_P_z := " . $axle->P_dyn) ;
            $axle->M_B_erf	=	( 0.55 * $axle->WeightLoaded * $axle->Rdyn * 9.81) ;
            error_log( "M_B_erf := " . $axle->M_B_erf) ;
        }
        FDbg::end() ;
    }
}
?>
