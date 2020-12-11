<?php
/**
 * apps/erm/index.php
 * ==================
 *
 * fetch the global configuration
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$lineEnd	=	"\n" ;
if ( isset( $_GET['screen'])) {
	require_once( $_GET['screen']) ;
	die() ;
} else if ( isset( $_GET['code'])) {
	require_once( $_GET['code']) ;
	die() ;
} else if ( isset( $_GET['js'])) {
	require_once( $_GET['js']) ;
	die() ;
}
/**
 * read the additional "mas" configuration parameters from the database
 */
$mySysConfig	=	EISSCoreObject::__getSysConfig() ;
error_log( $mySysConfig->dump( "mySysConfig->", true)) ;
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
		<title><?php echo $mySysSession->ClientId ; ?></title>
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

		.sprite-reload { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -64px -80px; }
		.sprite-left { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -96px -16px; }
		.sprite-right { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -32px -16px; }
		.sprite-dleft { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -48px -160px; }
		.sprite-dright { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -32px -160px; }
		.sprite-search { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -160px -112px; }
		.sprite-triangle-1-n { margin-top: -4px; width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -0px -16px; }
		.sprite-triangle-1-s { margin-top: 4px; margin-left: -26px; width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -64px -16px; }
		.sprite-add { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -16px -128px; }
		.sprite-tool { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -176px -112px; }
		.sprite-edit { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -64px -112px; }
		.sprite-garbage { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -176px -96px; }
		.sprite-goto { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -32px -80px; }
		.sprite-qplus { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -0px -192px; }
		.sprite-qminus { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -16px -192px; }
		.sprite-idown { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -64px -48px; }
		.sprite-iup { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -0px -48px; }
		.sprite-calc { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -128px -64px; }

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
showScreen( "splash") ;
}
	</script>
</head>
	<body onload="onLoad()" id="page">
	<div id="topContainer">
		<img src="" alt="a fancy logo is still missing ..." onclick="myInfo() ; myDebug() ;" />
	</div>
	<div style="height: 10px;">
	</div>
	<div id="toolBar">&nbsp;&nbsp;
		<img src="/clients/99999999/rsrc/icons/16x16/row 8/4.png" title="Go Back to last screen" onClick="hookBack() ;"/>&nbsp;
		<img src="/clients/99999999/rsrc/icons/16x16/row 9/1.png" title="Session Info ..." onclick="hookSession() ;"/>&nbsp;
	</div>
	<div style="height: 10px;">
	</div>
	<!--																-->
	<!--																-->
	<!--																-->
	<div id="centerContainer">
		<div id="centerLeft">
			<div id="Navigator">
	<?php
		function	showLeftMenu( $_parent="", $_level=1) {
			global	$lineEnd ;
			global	$screen ;
			global	$appUser ;
			global	$mySysConfig ;
			global	$mySysSession ;
			FDbg::begin( 0, "index.php", "*". "showLeftMenu( '$_parent')", "starting up") ;
	//		echo "<div data-dojo-type=\"dijit/layout/AccordionContainer\" "
	//				.	"data-dojo-props=\"minSize:30, region:'leading', splitter:false\" "
	//				.	"id=\"leftAccordion".$_parent."\">" ;
			$module	=	new UI_Module() ;
			$screen	=	new UI_Screen() ;
			$module->setIterCond( "ApplicationId = '".$mySysSession->ApplicationId."' AND ParentModuleName = '$_parent' ") ;
			$module->setIterOrder( "SeqNo ") ;
			reset( $module) ;
			foreach ( $module as $key => $mod) {
				if ( $appUser->isGranted( "scr", $module->ModuleName)) {
					echo "<div class=\"g".(string)$_level."p\" onClick=\"fold( '".$module->ModuleName."') ; return false ;\">" . $mod->Label . "</div>" ;
					echo "<div id=\"".$module->ModuleName."\" style=\"display: none;\" wapIsLoaded=\"true\" data-wap-fold=\"true\">" ;
					showLeftMenu( $module->ModuleName, $_level+1) ;
					$screen->setIterCond( "ApplicationId = '".$mySysSession->ApplicationId."' AND ModuleName = '". $mod->ModuleName."' ") ;
					$screen->setIterOrder( "SeqNo ") ;
					reset( $screen) ;
					foreach ( $screen as $key => $screen) {
						if ( $appUser->isGranted( "scr", $module->ModuleName.".".$screen->ScreenName) || $appUser->isGranted( "scr", $module->ModuleName.".*")) {
							echo "<div class=\"g".(string)($_level+1)."p\" onClick=\"showScreen( '".$screen->ScreenName."', true) ; return false ;\">" . $screen->Label . "</div>" ;
						}
					}
					echo "</div>" ;
				}
			}
			FDbg::end() ;
		}
		if ( $mySysConfig->ui->showLeftMenu) {
			showLeftMenu() ;
		}
	?>
			</div>
		</div>
		<div id="centerRight">
			<div id="infos">
				<div class="block block-list block-compare">
					<div class="block block-cart">
						<div class="block-title">
							<strong> <span>Help</span>
							</strong>
						</div>
						<div class="block-content">
							<button onclick="dLevel( 0) ;">0</button><br/>
							<button onclick="dLevel( 9) ;">9</button><br/>
							<button onclick="dLevel( 99) ;">99</button><br/>
							<button onclick="dLevel( 999) ;">999</button>
						</div>
					</div>
				</div>
				<div class="block block-list block-compare">
					<div class="block block-cart">
						<div class="block-title">
							<strong> <span>Some text ...</span>
							</strong>
						</div>
						<div class="block-content" id="CartInfo"></div>
					</div>
				</div>
				<div class="block block-list block-compare">
					<div class="block block-cart">
						<div class="block-title"></div>
						<div class="block-content" id="CartInfo"></div>
					</div>
				</div>
			</div>
		</div>
		<div id="center">
			<div id="splash" data-wap-screen="splash.html"></div>
	<?php
		function	showPanels( $_mod) {
			global	$mySysSession ;
			global	$lineEnd ;
			global	$mySysConfig ;
			global	$mySysSession ;
			$module	=	new UI_Module() ;
			$module->setIterOrder( "SeqNo ") ;
			reset( $module) ;
			foreach ( $module as $key => $mod) {
				$screen	=	new UI_Screen() ;
				$screen->setIterCond( "ApplicationId = '".$mySysSession->ApplicationId."' AND ModuleName = '". $mod->ModuleName."' ") ;
				$screen->setIterOrder( "SeqNo ") ;
				reset( $screen) ;
				foreach ( $screen as $key => $screen) {
					echo "<div id=\"".$screen->ScreenName."\" style=\"display: none;\" data-wap-module-dir=\"".$module->Dir."\" data-wap-screen-dir=\"".$screen->SubDir."\" data-wap-screen=\"".$screen->MainPHPFile."\"></div>" ;
				}
			}
		}
		showPanels( "") ;
	?>
			</div>
		</div>
		<div id="footer">
			<center>
				<p>&copy; 2007-<?php echo EISSCoreObject::today() ; ?></p>
				The content of this website is intended solely for wimtecc, Karl-Heinz Welter.<br />
			</center>
		</div>
	</body>
</html>
<?php
} else {
	require_once( "login.html") ;
	die() ;
}
?>
