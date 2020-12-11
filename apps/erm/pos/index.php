<?php
/**
 * apps/erm/index.php
 * ==================
 *
 * fetch the global configuration
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
/**
 * read the additional "mas" configuration parameters from the database
 */
$mySysConfig	=	EISSCoreObject::__getSysConfig() ;
$mySysConfig->addFromSysDb( "mas") ;
/**
 * We rely on getting all data through POST variables for the login process. Therefor, copy the value from
 * $_GET to $_POST which have been transmitted.
 */
if ( ! isset( $_POST['selEnv']))
	if ( isset( $_GET['selEnv']))
		$_POST['selEnv']	=	$_GET['selEnv'] ;
if ( ! isset( $_POST['selLang']))
	if ( isset( $_GET['selLang']))
		$_POST['selLang']	=	$_GET['selLang'] ;
/**
 * double check if the ClientApplication is defined. If not, we need to present the login screen.
 */
if ( $mySysSession->Validity == SysSession::VALIDAPP) {
	$mySysUser	=	$mySysSession->SysUser ;
	$scriptPath	=	$_SERVER["DOCUMENT_ROOT"] . "/"
				.	"clients"
				.	$mySysSession->ClientId . "/apps/"
				.	$mySysSession->ApplicationSystemId . "/"
 				.	$mySysSession->ApplicationId . "/scripts/"
 				;
	$appUser	=	EISSCoreObject::__getAppUser() ;
?>
<html dir="ltr">
	<meta charset="utf-8" />
	<!--																														-->
	<!--																														-->
	<!--																														-->
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>ERM/ERP - <?php echo $mySysSession->ClientId ; ?></title>
		<script>
sessionId		=	"<?php echo $mySysSession->SessionId ; ?>" ;
		</script>
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=Styles.css" media="screen"
			title="Default screen stylesheet" />
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=R3.css" media="screen"
			title="Default screen stylesheet" />
		<style type="text/css" media="screen">

		.memu-icon {
			margin: 0;
			padding: 0;
			position: relative;
			width: 16px;
			height: 16px;
			margin: 4px 10px 0px 0px;
			float: left;
		}

		.sprite-triangle-1-s {
		    background-position: -64px -16px;
		}

		.sprite-reload { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -64px -80px; }
		.sprite-left { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -96px -16px; }
		.sprite-right { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -32px -16px; }
		.sprite-dleft { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -48px -160px; }
		.sprite-dright { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -32px -160px; }
		.sprite-sleft { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -80px -160px; }
		.sprite-sright { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -64px -160px; }
		.sprite-search { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -160px -112px; }
		.sprite-triangle-1-n { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -0px -16px; }
		.sprite-triangle-1-s { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -64px -16px; }
		.sprite-add { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -16px -128px; }
		.sprite-tool { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -176px -112px; }
		.sprite-edit { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -64px -112px; }
		.sprite-garbage { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -176px -96px; }
		.sprite-goto { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -32px -80px; }
		.sprite-qplus { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -0px -192px; }
		.sprite-qminus { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -16px -192px; }
		.sprite-idown { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -64px -48px; }
		.sprite-iup { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -0px -48px; }
		.sprite-calc { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -128px -64px; }
		.sprite-info { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png)) no-repeat -16px -144px; }

		.sprite-cash-1 { width: 40px; height: 40px; background:url(/erm/pos/rsrc/icons/assorted_glass_buttons.png) no-repeat -39px -82px; }
		.sprite-cash-2 { width: 40px; height: 40px; background:url(/erm/pos/rsrc/icons/assorted_glass_buttons.png) no-repeat -76px -82px; }
		.sprite-cash-3 { width: 40px; height: 40px; background:url(/erm/pos/rsrc/icons/assorted_glass_buttons.png) no-repeat -113px -82px; }
		.sprite-cash-4 { width: 40px; height: 40px; background:url(/erm/pos/rsrc/icons/assorted_glass_buttons.png) no-repeat -150px -82px; }
		.sprite-cash-5 { width: 40px; height: 40px; background:url(/erm/pos/rsrc/icons/assorted_glass_buttons.png) no-repeat -187px -82px; }
		.sprite-cash-6 { width: 40px; height: 40px; background:url(/erm/pos/rsrc/icons/assorted_glass_buttons.png) no-repeat -229px -82px; }
		.sprite-cash-7 { width: 40px; height: 40px; background:url(/erm/pos/rsrc/icons/assorted_glass_buttons.png) no-repeat -152px -82px; }
		.sprite-cash-8 { width: 40px; height: 40px; background:url(/erm/pos/rsrc/icons/assorted_glass_buttons.png) no-repeat -190px -82px; }
		.sprite-cash-9 { width: 40px; height: 40px; background:url(/erm/pos/rsrc/icons/assorted_glass_buttons.png) no-repeat -229px -82px; }

		</style>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=debug.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=common.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapDataSource.js" ></script>	<!-- nothing to translate -->
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapEditor.js" ></script>	<!-- nothing to translate -->
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapGrid.js" ></script>	<!-- nothing to translate -->
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapScreen.js" ></script>	<!-- nothing to translate -->
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelector.js" ></script>	<!-- nothing to translate -->
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelect.js" ></script>	<!-- nothing to translate -->
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapPopup.js" ></script>	<!-- nothing to translate -->
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=chkeditor/chkeditor.js" ></script>	<!-- nothing to translate -->
		<script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>
		<!--
		 --
		 -- get the stylesheets
		 --
		 -->
		<link rel="stylesheet" type="text/css" media="screen" href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=erm_erp.css" title="Default" />
		<!--				-->
		<script>
function	onLoad() {
//	dOpen( 9) ;
	showScreen( "CashSale") ;
}
		</script>
	</head>
	<!--																														-->
	<!--																														-->
	<!--																														-->
	<body onload="onLoad()" id="page">
		<div id="center">
			<div id="CashSale" data-wap-screen="mainCashSale.xml"></div>
			</div>
		</div>
	</body>
</html>
<?php
} else {
	require_once( "login.html") ;
	die() ;
}
?>
