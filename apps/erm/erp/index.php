<?php
/**
 * apps/erm/index.php
 * ==================
 *
 * fetch the global configuration
 */
$debugBoot	=	true ;
error_log( "including config.inc.php") ;
require_once( $_SERVER["DOCUMENT_ROOT"]."/mas/Config/config.inc.php") ;
error_log( "inclusion of config.inc.php finished") ;

FDbg::setAppToTrace( "") ;
FDbg::setApp( "index.php") ;

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

error_log( "mySysSession->Validity = " . $mySysSession->Validity) ;

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
        <link rel="stylesheet" type="text/css" href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapBasic.css">
        <link rel="stylesheet" type="text/css" href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapPopup.css">
        <link rel="stylesheet" type="text/css" href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapBusy.css">
        <link rel="stylesheet" type="text/css" href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapGrid.css">
        <link rel="stylesheet" type="text/css" href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapTree.css">
		<link rel="stylesheet" type="text/css" href="/rsrc/styles/semanticUI/semantic.min.css">
        <!--
			Load 3rd party JavaScript stuff and related stylesheets
		-->
        <link rel="stylesheet" href="http://www.khwelter.de.local/rsrc/js/jQuery/jquery-ui-1.12.1.custom/jquery-ui.css">
        <script src="/rsrc/js/jQuery/jquery-3.2.1.min.js"></script>
        <script src="/rsrc/js/jQuery/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
        <script src="/rsrc/js/jQuery/jquery.ui.autocomplete.html.js"></script>
		<script src="/rsrc/js/semanticUI/semantic.min.js"></script>
        <!--
			Load my own JavaScript stuff
		-->
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
        <!--
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=debug.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapCommon.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapDataSource.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapEditor.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapGrid.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapScreen.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelector.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelect.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapTree.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapPopup.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapBusy.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=chkeditor/chkeditor.js" ></script>
		<script src="//cdn.ckeditor.com/4.4.7/standard/ckeditor.js"></script>
-->
        <script type="text/javascript" src="/api/debug.js"></script>
        <script type="text/javascript" src="/api/wapCommon.js"></script>
        <script type="text/javascript" src="/api/wapDataSource.js"></script>
        <script type="text/javascript" src="/api/wapDialog.2.0.0.js"></script>
        <script type="text/javascript" src="/api/wapGrid.2.0.0.js"></script>
        <script type="text/javascript" src="/api/wapScreen.js"></script>
        <script type="text/javascript" src="/api/wapSelector.js"></script>
        <script type="text/javascript" src="/api/wapSelect.js"></script>
        <script type="text/javascript" src="/api/wapTree.js"></script>
        <script type="text/javascript" src="/api/wapPopup.js"></script>
        <script type="text/javascript" src="/api/wapBusy.js"></script>
        <!--
		 --
		 -- get the stylesheets
		 --
		 -->
        <link rel="stylesheet" type="text/css" media="screen" href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=erm_erp.css" title="Default" />
        <!--				-->
        <script>
            function	onLoad() {
                showScreen( "splash") ;
            }
            var	rightVisible	=	true ;
            function	showBarRight() {
                if ( rightVisible) {
                    rightVisible	=	false ;
                    document.getElementById( "centerRight").style.width	=	20 ;
                    document.getElementById( "infos").style.visibility	=	"hidden" ;
                } else {
                    rightVisible	=	true ;
                    document.getElementById( "centerRight").style.width	=	195 ;
                    document.getElementById( "infos").style.visibility	=	"visible" ;
                }
            }
        </script>
    </head>
    <!--																														-->
    <!--																														-->
    <!--																														-->
    <body onload="onLoad()" id="page">
    <div id="toolBar">&nbsp;&nbsp;
		<button class="openbtn" onclick="openNav()">☰ Menu</button>
		<button class="openbtn" onclick="openNav()">&#8630; Reload</button>
<!---
		<img src="/rsrc/icons/16x16/row 15/13.png" title="Reload this screen" onClick="hookReload() ;"/>&nbsp;
        <img src="/rsrc/icons/16x16/row 2/1.png" title="Delete this Object"/>&nbsp;
        <img src="/rsrc/icons/16x16/row 10/4.png" title="Mail to ..." />&nbsp;
        <img src="/rsrc/icons/16x16/row 11/5.png" title="Create New Object" />&nbsp;
        <img src="/rsrc/icons/16x16/row 9/1.png" title="Session Info ..." onclick="hookSession() ;"/>&nbsp;
        <img src="/rsrc/icons/16x16/row 8/4.png" title="Go Back to last screen" onClick="hookBack() ;"/>&nbsp;
--->
	</div>
    <div style="height: 5px;">
    </div>
    <!--																-->
    <!--																-->
    <!--																-->
    <div id="centerContainer">
        <div id="centerLeft" class="centerLeft">
			<button class="closebtn" onclick="closeNav()">×</button>
			<div id="Navigator">
				<?php
				function	showLeftMenu( $_parent="", $_level=1) {
					global	$lineEnd ;
					global	$screen ;
					global	$appUser ;
					global	$mySysConfig ;
					global	$mySysSession ;
					$module	=	new UI_Module() ;
					$screen	=	new UI_Screen() ;
					$module->setIterCond( "ApplicationId = '".$mySysSession->ApplicationId."' AND ParentModuleName = '$_parent' ") ;
					$module->setIterOrder( "SeqNo ") ;
					reset( $module) ;
					error_log( "iterating modules ...") ;
					foreach ( $module as $key => $mod) {
						if ( $appUser->isGranted( "scr", $module->ModuleName)) {
							echo "<div class=\"g".(string)$_level."p\" onClick=\"fold( '".$module->ModuleName."') ; return false ;\">" . FTr::tr( $mod->Label) . "</div>\n" ;
							echo "<div id=\"".$module->ModuleName."\" style=\"display: none;\" wapIsLoaded=\"true\" data-wap-fold=\"true\">" ;
							showLeftMenu( $module->ModuleName, $_level+1) ;
							$screen->setIterCond( "ApplicationId = '".$mySysSession->ApplicationId."' AND ModuleName = '". $mod->ModuleName."' AND Label <> '' ") ;
							$screen->setIterOrder( "SeqNo ") ;
							reset( $screen) ;
							error_log( "iterating screens ...") ;
							foreach ( $screen as $key => $screen) {
								if ( $appUser->isGranted( "scr", $module->ModuleName.".".$screen->ScreenName) || $appUser->isGranted( "scr", $module->ModuleName.".*")) {
									echo "<div class=\"g".(string)($_level+1)."p\" onClick=\"closeNav() ; showScreen2( '".$screen->ScreenName."', false) ; return false ;\">" . FTr::tr( $screen->Label) . "</div>\n" ;
								}
							}
							echo "</div>\n" ;
						} else {
							error_log( "-----------------------------------> missing grant -----------------> " . $module->ModuleName) ;
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
        <!--
				<div id="centerRight">
					<div onclick="showBarRight() ;">
						<img src="/rsrc/icons/16x16/row 8/4.png" title="Go Back to last screen" />&nbsp;
					</div>
					<div id="infos">
					</div>
				</div>
		-->
        <div id="center">
            <div id="splash" class="contentHoldingDiv" data-wap-screen="splash.html"></div>
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
						echo "<div id=\"".$screen->ScreenName."\" class=\"contentHoldingDiv\" style=\"display: none;\" data-wap-is-loaded=\"false\" data-wap-module-dir=\"".$module->Dir."\" data-wap-screen-dir=\"".$screen->SubDir."\" data-wap-screen=\"".$screen->MainPHPFile."\"></div>" ;
					}
				}
			}
			showPanels( "") ;
			?>
        </div>
    </div>
    <div id="footer">
        <center>
            <p>&copy; 2007-<?php echo EISSCoreObject::today() ; ?> for Platform<br/>
                &copy; 2010-<?php echo EISSCoreObject::today() ; ?> for ERP Core<br/>
                &copy; 2016-<?php echo EISSCoreObject::today() ; ?> for Application</p>
            The content of this website is intended solely for wimtecc, Karl-Heinz Welter.<br />
        </center>
    </div>
    </body>
    </html>
	<?php
} else {
//	require_once( "login.html") ;
	echo "fail!" ;
	die() ;
}
?>
