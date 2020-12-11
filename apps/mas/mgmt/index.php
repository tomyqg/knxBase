<?php
/**
 * apps/bpw/DesignStudyR3/index.php
 * ================================
 *
 * fetch the global configuration
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/mas/Config/config.inc.php") ;
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
 * read the additional "mas" configuration parameters from the "mas"-system database
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
FDbg::trace( 1, FDbg::mdTrcInfo1, "index.php", "*", "main", "getting user") ;
EISSCoreObject::__setSysUser( $mySysSession->SysUser) ;
/**
 * double check if the ClientApplication is defined. If not, we need to present the login screen.
 */
if ( $mySysSession->Validity == SysSession::VALIDAPP) {
	$mySysUser	=	$mySysSession->SysUser ;
	$scriptPath	=	$_SERVER["DOCUMENT_ROOT"] . "/"
				.	"clients"
				.	$mySysSession->ClientId . "/apps/"
				.	$mySysSession->ApplicationSystemId . "/"
//
// here we need to feed in wrapper code for the specific version to be used
//
//
// end of wrapper code for version management
//
	.	$mySysSession->ApplicationId . "/scripts/"
 				;
	$appUser	=	EISSCoreObject::__getAppUser() ;
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
	<head>
		<title>mas - Administration(<?php echo $_SERVER["SERVER_ADDR"] ; ?>)</title>
		<script>
sessionId		=	"<?php echo $mySysSession->SessionId ; ?>" ;
		</script>
		<script>
var	lastSize	=	0 ;
var	lastR	=	128 ;
var	lastG	=	128 ;
var	lastB	=	128 ;
function	incFontSize	( _className) {
	var	elements	=	document.getElementsByClassName( _className) ;
	lastSize++ ;
	for ( var i=0 ; i<elements.length ; i++) {
		elements[i].style.fontSize	=	lastSize.toString() + "px" ;
	}
} ;
function	decFontSize	( _className) {
	var	elements	=	document.getElementsByClassName( _className) ;
	lastSize-- ;
	for ( var i=0 ; i<elements.length ; i++) {
		elements[i].style["fontSize"]	=	lastSize.toString() + "px" ;
	}
} ;
function	setBG( _className, _color, _disp) {
	var	elements	=	document.getElementsByClassName( _className) ;
	for ( var i=0 ; i<elements.length ; i++) {
		switch ( _color) {
		case	"R"	:
			lastR	+=	_disp ;
			break ;
		case	"G"	:
			lastG	+=	_disp ;
			break ;
		case	"B"	:
			lastB	+=	_disp ;
			break ;
		}
		elements[i].style.backgroundColor	=	"rgb( " + lastR.toString() + ", " + lastG.toString() + ", " + lastB.toString() + ")" ;
	}
} ;
function	setMe( _className, _attribute, _disp) {
	var	elements	=	document.getElementsByClassName( _className) ;
	for ( var i=0 ; i<elements.length ; i++) {
		if ( ! elements[i].style[_attribute]) {
			elements[i].style[_attribute]	=	"5px" ;
		}
		var 	actSize	=	parseInt( elements[i].style[_attribute]) ;
		elements[i].style[_attribute]	=	(actSize + _disp).toString() + "px" ;
	}
} ;
		</script>
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapBasic.css" />
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapPopup.css" />
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapBusy.css" />
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapGrid.css" />
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapTree.css" />
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

		.sprite-reload { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -64px -80px; }
		.sprite-left { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -96px -16px; }
		.sprite-right { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -32px -16px; }
		.sprite-dleft { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -48px -160px; }
		.sprite-dright { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -32px -160px; }
		.sprite-sleft { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -80px -160px; }
		.sprite-sright { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -64px -160px; }
		.sprite-search { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -160px -112px; }
		.sprite-triangle-1-n { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -0px -16px; }
		.sprite-triangle-1-s { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -64px -16px; }
		.sprite-add { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -16px -128px; }
		.sprite-tool { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -176px -112px; }
		.sprite-edit { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -64px -112px; }
		.sprite-garbage { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -176px -96px; }
		.sprite-goto { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -32px -80px; }
		.sprite-qplus { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -0px -192px; }
		.sprite-qminus { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -16px -192px; }
		.sprite-idown { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -64px -48px; }
		.sprite-iup { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -0px -48px; }
		.sprite-calc { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -128px -64px; }
		.sprite-info { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -16px -144px; }
		.sprite-folded { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -32px -16px; }
		.sprite-unfolded { width: 16px; height: 16px; background:url(/api/loadImage.php?image=ui-icons_3d80b3_256x240.png) no-repeat -64px -16px; }

		</style>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=debug.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapBusy.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapCommon.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapScreen.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapDataSource.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapEditor.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelector.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelect.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapGrid.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapPopup.js" ></script>
		<script>
"use strict"
function	onLoad() {
	showScreen( "splash") ;
//	var	myStyler	=	new wapPopup( null, "Styler", { url: "styler.html"}) ;
//	myStyler.show() ;
}
		</script>
	</head>
	<body onload="onLoad()" id="page">
		<div id="topContainer">
			<img src="/mas/rsrc/images/mas-logo.png" alt="a fancy logo is still missing ..." onclick="myInfo() ; myDebug() ;" />
		</div>
		<div style="height: 10px;">
		</div>
		<div id="toolBar">&nbsp;&nbsp;
			<img src="/rsrc/icons/16x16/row 15/13.png" title="Reload this screen" onClick="hookReload() ;"/>&nbsp;
			<img src="/rsrc/icons/16x16/row 8/4.png" title="Go Back to last screen" onClick="hookBack() ;"/>&nbsp;
			<img src="/rsrc/icons/16x16/row 2/1.png" title="Delete current data"/>&nbsp;
			<img src="/rsrc/icons/16x16/row 10/4.png" title="Mail somebody" />&nbsp;
			<img src="/rsrc/icons/16x16/row 11/5.png" title="create new" />&nbsp;
			<img src="/rsrc/icons/16x16/row 9/1.png" title="Session Info ..." onclick="hookSession() ;"/>&nbsp;
		</div>
		<div style="height: 10px;">
		</div>
		<div id="centerContainer">
			<div id="centerLeft">
				<div id="Navigator">
	<?php
		function	showLeftMenu( $_parent="", $_level=1) {
			global	$lineEnd ;
			global	$screen ;
			global	$appUser ;
			FDbg::begin( 0, "index.php", "*". "showLeftMenu( '$_parent')", "starting up") ;
	//		echo "<div data-dojo-type=\"dijit/layout/AccordionContainer\" "
	//				.	"data-dojo-props=\"minSize:30, region:'leading', splitter:false\" "
	//				.	"id=\"leftAccordion".$_parent."\">" ;
			$module	=	new UI_Module() ;
			$screen	=	new UI_Screen() ;
			$module->setIterCond( "ParentModuleName = '$_parent' ") ;
			$module->setIterOrder( "SeqNo ") ;
			reset( $module) ;
			foreach ( $module as $key => $mod) {
				if ( $appUser->isGranted( "scr", $module->ModuleName)) {
					echo "<div class=\"g".(string)$_level."p\" onClick=\"fold( '".$module->ModuleName."') ; return false ;\">" . $mod->Label . "</div>" ;
					echo "<div id=\"".$module->ModuleName."\" style=\"display: none;\" wapIsLoaded=\"true\" data-wap-fold=\"true\">" ;
					showLeftMenu( $module->ModuleName, $_level+1) ;
					$screen->setIterCond( "ModuleName = '". $mod->ModuleName."' ") ;
					$screen->setIterOrder( "SeqNo ") ;
					reset( $screen) ;
					foreach ( $screen as $key => $screen) {
						if ( $appUser->isGranted( "scr", $module->ModuleName.".".$screen->ScreenName) || $appUser->isGranted( "scr", $module->ModuleName.".*")) {
							echo "<div class=\"g".(string)($_level+1)."p\" onClick=\"showScreen( '".$screen->ScreenName."', false) ; return false ;\">" . $screen->Label . "</div>" ;
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
							<div class="block-content block-debug">
								<a href="#" class="button" onclick="dLevel( 0) ;" class="button"><span>&#x2713;</span>0</a><br/>
								<a href="#" class="button" onclick="dLevel( 9) ;" class="button"><span>&#x2717;</span>9</a><br/>
								<a href="#" class="button" onclick="dLevel( 99) ;" class="button"><span>&#x2055;</span>99</a><br/>
								<a href="#" class="button" onclick="dLevel( 999) ;" class="button"><span>&#x2055;</span>999</a>
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
			$module	=	new UI_Module() ;
			$module->setIterOrder( "SeqNo ") ;
			reset( $module) ;
			foreach ( $module as $key => $mod) {
				$screen	=	new UI_Screen() ;
				$screen->setIterCond( "ModuleName = '". $mod->ModuleName."' ") ;
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
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<title>Login to MAS</title>
</head>
<body>
	<b>Please login!</b>
<?php
	include( "login.html") ;
?>
</body>
</html>
<?php
}
?>
