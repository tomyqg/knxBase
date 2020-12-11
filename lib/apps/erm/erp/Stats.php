<?php
/**
 * Stats.php - Basic class to retrieve data in a datamining fashion
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * requires mostly platform stuff
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
/**
 * Stats - User-Level Klasse
 *
 * This class acts as an interface for data-mining in general.
 * The following core methods are provided.
 *
 *
 * @package Application
 * @subpackage Stats
 */
class	Stats	extends	EISSCoreObject	{
	public	$objName ;
	var	$quarters	=	array( "Q'1", "Q'2", "Q'3", "Q'4") ;
	var	$months	=	array( "Januar","Februar","Maerz","April","May","Juni","Juli","August","September","Oktober","November","Dezember") ;
	/**
	 * __construct
	 * 
	 * Creates an instance of a dataminer for an object of class <$_objName>.
	 *
	 * @param	string	$_objName	class for which a dataminer shall be created
	 */
	function	__construct( $_objName="") {
		if ( $_objName == "") {
			parent::__construct( "Stats:" . $_objName) ;
		} else {
			parent::__construct( $_objName) ;
		}
		$this->valid	=	true ;			// data-mminer is cvalid upon creation
		$this->_valid	=	$this->valid ;
		$this->objName	=	$_objName ;
	}
	/**
	 * setKey
	 * 
	 * Needed in order to conform to the EISS calling standard. Not really used.
	 * Only returns the validity of the current instance, which should usually be true (means: valid)
	 * 
	 * @param	string	$_key
	 * @param	int		$_id
	 * @param	mixed	$_val
	 * @return	bool	validity
	 */
	function	setKey( $_key) {
		$this->key	=	$_key ;
		return $this->valid ;
	}
	function	setId( $_id) {
		$this->id	=	$_id ;
		return $this->valid ;
	}
	/**
	 * 
	 */
	function	createGraph( $_fileName, $_ord, $_ordName, $_data, $_name, $_data1=null, $_name1=null) {
		FDbg::begin( 1, "Stats.php", "Stats", "createGraphM( <array>)") ;
		$width	=	800 ;
		$height	=	400 ;
		/* pChart library inclusions */
		include("class/pData.class.php");
		include("class/pDraw.class.php");
		include("class/pImage.class.php");
		
		/* Create and populate the pData object */
		$MyData = new pData();
		$MyData->loadPalette("palettes/light.color",TRUE);
		$MyData->addPoints( $_data,$_name);
		if ( $_data1 != null)
			$MyData->addPoints( $_data1,$_name1);
		$MyData->setAxisName(0,$_ordName);
		$MyData->addPoints( $_ord, $_ordName);
//		$MyData->setSerieDescription("Months","Month");
		$MyData->setAbscissa( $_ordName);
		
		/* Create the pChart object */
		$myPicture = new pImage($width,$height,$MyData);
		$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
		$myPicture->drawGradientArea(0,0,$width,$height,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
		$myPicture->setFontProperties(array("FontName"=>"../lib/pchart_2.1.3/fonts/pf_arma_five.ttf","FontSize"=>8));
		
		/* Draw the scale  */
		$myPicture->setGraphArea(50,30,$width-50,$height-30);
		$myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10));
		
		/* Turn on shadow computing */
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		
		/* Draw the chart */
		$settings = array("Gradient"=>TRUE,"GradientMode"=>GRADIENT_EFFECT_CAN,"DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255,"DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>10);
		$myPicture->drawBarChart($settings);
		
		/* Write the chart legend */
		$myPicture->drawLegend($width-200,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
		
		/* Render the picture (choose the best way) */
		$myPicture->render( $_fileName);		
		FDbg::end( 1, "Stats.php", "Stats", "createGraphM( <array>)") ;
	}
}
?>
