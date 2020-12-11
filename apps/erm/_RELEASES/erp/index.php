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

		.sprite-reload { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -64px -80px; }
		.sprite-left { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -96px -16px; }
		.sprite-right { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -32px -16px; }
		.sprite-dleft { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -48px -160px; }
		.sprite-dright { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -32px -160px; }
		.sprite-sleft { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -80px -160px; }
		.sprite-sright { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -64px -160px; }
		.sprite-search { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -160px -112px; }
		.sprite-triangle-1-n { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -0px -16px; }
		.sprite-triangle-1-s { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -64px -16px; }
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
		.sprite-info { width: 16px; height: 16px; background:url(/jQuery/jquery-ui-1.8.23.custom/css/cupertino/images/ui-icons_3d80b3_256x240.png) no-repeat -16px -144px; }

		</style>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=debug.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapCommon.js" ></script>
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
	<!--																														-->
	<!--																														-->
	<!--																														-->
	<body onload="onLoad()" id="page">
	<!--
		<div data-dojo-type="dijit/MenuBar" id="menuBar">
			<div data-dojo-type="dijit/PopupMenuBarItem">
				<span>File</span>
				<div data-dojo-type="dijit/Menu" id="fileMenu">
					<div data-dojo-type="dijit/MenuItem" id="menuItemBack" onClick="hookBack() ;"><?php echo FTr::tr( "Last screen") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemNew" onClick="hookNew() ;"><?php echo FTr::tr( "New ....") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemCopy" onClick="hookCopy() ;"><?php echo FTr::tr( "Copy ...") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemSaveTab" onClick="hookSaveTab() ;"><?php echo FTr::tr( "Save this tab") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemSaveAllTabs" onClick="hookSaveAllTabs() ;"><?php echo FTr::tr( "Save all tabs") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemDelete" onClick="hookDelete() ;"><?php echo FTr::tr( "Delete ...") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemCreateCSV" onClick="hookCSVCreate() ;"><?php echo FTr::tr( "Create CSV") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemCreatePDF" onClick="hookPDFCreate() ;"><?php echo FTr::tr( "Create PDF") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemShowPDF" onClick="hookPDFShow() ;"><?php echo FTr::tr( "Display PDF") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemPrintPDF" onClick="hookPDFPrint() ;"><?php echo FTr::tr( "Print PDF") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemMailer" onClick="unsolMailer() ;"><?php echo FTr::tr( "Write E-Mail ...") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemReload" onClick="hookReload() ;"><?php echo FTr::tr( "Reload screen") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemDumpScreenAll" onClick="screenDumpAll() ;"><?php echo FTr::tr( "Dump screen-info") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemSetMenuItems" onClick="hookSetMenuItems() ;"><?php echo FTr::tr( "Re-enable menu items") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" id="menuItemLogoff" onClick="hookLogoff() ;"><?php echo FTr::tr( "Logoff") ; ?></div>
				</div>
			</div>
			<div data-dojo-type="dijit/PopupMenuBarItem">
				<span>Developer</span>
				<div data-dojo-type="dijit/Menu" id="devMenu">
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile incRev ', null, null) ;"><?php echo FTr::tr( "Increment Revision") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
	<?php	if ( strpos( $mySysUser->Packages, "canRel") !== false) {			?>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile release ', null, null) ;"><?php echo FTr::tr( "Release all") ; ?></div>
	<?php	}																?>
	<?php 	if ( strpos( $mySysUser->Packages, "canRel") !== false) {			?>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile releaseLocal ', null, null) ;"><?php echo FTr::tr( "Release local") ; ?></div>
	<?php	}																?>
					<div data-dojo-type="dijit/MenuItem" onClick="dClose() ;"><?php echo FTr::tr( "Debug level := 0") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="dOpen( 9) ;"><?php echo FTr::tr( "Debug mask := 9") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="dOpen( 99) ;"><?php echo FTr::tr( "Debug mask := 99") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="dOpen( 499) ;"><?php echo FTr::tr( "Debug mask := 499") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="dOpen( 999) ;"><?php echo FTr::tr( "Debug mask := 999") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="dOpen( 9999) ;"><?php echo FTr::tr( "Debug mask := 9999") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="dClear() ;"><?php echo FTr::tr( "Clear History") ; ?></div>
				</div>
			</div>
			<div data-dojo-type="dijit/PopupMenuBarItem">
				<span>SysOp</span>
				<div data-dojo-type="dijit/Menu" id="sysOpMenu">
	<?php 	if ( strpos( $mySysUser->Packages, "canUpd") !== false) {			?>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile selfUpdate ', null, null) ;"><?php echo FTr::tr( "Self-Update public") ; ?></div>
	<?php	}																?>
	<?php 	if ( strpos( $mySysUser->Packages, "canUpd") !== false) {			?>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile selfUpdateLocal', null, null) ;"><?php echo FTr::tr( "Self-Update local") ; ?></div>
	<?php 	}																?>
				</div>
			</div>
			<div data-dojo-type="dijit/PopupMenuBarItem">
				<span>Admin</span>
				<div data-dojo-type="dijit/Menu" id="admMenu">
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile toProduction ', null, null) ;"><?php echo FTr::tr( "Copy to Production Site") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; ./scrSyncImages ', null, null) ;"><?php echo FTr::tr( "Sync Images - both ways") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; ./scrSyncDownloads ', null, null) ;"><?php echo FTr::tr( "Sync Downloads - both ways") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; ./scrSyncDocuments ', null, null) ;"><?php echo FTr::tr( "Sync Documents - both ways") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile updateSysData ', null, null) ;"><?php echo FTr::tr( "Upload System Data to public") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile updateCustomers ', null, null) ;"><?php echo FTr::tr( "Upload Customers to public") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile updatePrices ', null, null) ;"><?php echo FTr::tr( "Upload Prices to public") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile updateArticles ', null, null) ;"><?php echo FTr::tr( "Upload Articles to public") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile updateStructure ', null, null) ;"><?php echo FTr::tr( "Upload Structure to public") ; ?></div>
					<div data-dojo-type="dijit/MenuSeparator"></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile loadPrices ', null, null) ;"><?php echo FTr::tr( "Load Prices") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile loadArticles ', null, null) ;"><?php echo FTr::tr( "Load Articles") ; ?></div>
					<div data-dojo-type="dijit/MenuItem" onClick="requestScript( 'Scripts', 'export scriptPath=<?php echo $scriptPath ; ?> ; make -f Makefile loadStructure ', null, null) ;"><?php echo FTr::tr( "Load Structure") ; ?></div>
				</div>
			</div>
	<?php
		function	showTopMenu( $_parent) {
			global	$lineEnd ;
			$module	=	new UI_Module() ;
			$screen	=	new UI_Screen() ;
			$module->setIterCond( "ParentModuleName = '$_parent' ") ;
			$module->setIterOrder( "SeqNo ") ;
			reset( $module) ;
			foreach ( $module as $key => $mod) {
				echo "<div data-dojo-type=\"dijit/PopupMenuBarItem\">".$lineEnd ;
				echo "<span>".FTr::tr( $mod->Label)."</span>".$lineEnd ;
				echo "<div data-dojo-type=\"dijit/Menu\" id=\"menu".$mod->ModuleName."\">".$lineEnd ;
				$screen->setIterCond( "ModuleName = '". $mod->ModuleName."' ") ;
				$screen->setIterOrder( "SeqNo ") ;
				reset( $screen) ;
				foreach ( $screen as $key => $screen) {
					echo "<div data-dojo-type=\"dijit/MenuItem\" onClick=\"screenShow( '".$screen->ScreenName."') ;\">".FTr::tr( $screen->Label)."</div>".$lineEnd ;
				}
				echo "</div>".$lineEnd ;
				echo "</div>".$lineEnd ;
			}
		}
		showTopMenu( "") ;
	?>
			<div data-dojo-type="dijit/PopupMenuBarItem">
		        <span>Help</span>
				<div data-dojo-type="dijit/Menu" id="helpMenu">
					<div data-dojo-type="dijit/MenuItem" onClick="hookAbout() ;">About</div>
					<div data-dojo-type="dijit/MenuItem" onClick="hookSession() ;">SysSession Data</div>
					<div data-dojo-type="dijit/MenuItem" onClick="hookWebsite() ;">Goto WebSite</div>
					<div data-dojo-type="dijit/MenuItem" onClick="hookProgDoc() ;">Programming Doc.</div>
					<div data-dojo-type="dijit/MenuItem" onClick="hookSysUserDoc() ;">SysUser Doc.</div>
				</div>
			</div>
		</div>
	-->
	<div id="topContainer">
		<img src="" alt="a fancy logo is still missing ..." onclick="myInfo() ; myDebug() ;" />
	</div>
	<div style="height: 10px;">
	</div>
	<div id="toolBar">&nbsp;&nbsp;
		<img src="/clients/99999999/rsrc/icons/16x16/row 15/13.png" title="Reload this screen" onClick="hookReload() ;"/>&nbsp;
		<img src="/clients/99999999/rsrc/icons/16x16/row 2/1.png" title="Delete this Object"/>&nbsp;
		<img src="/clients/99999999/rsrc/icons/16x16/row 10/4.png" title="Mail to ..." />&nbsp;
		<img src="/clients/99999999/rsrc/icons/16x16/row 11/5.png" title="Create New Object" />&nbsp;
		<img src="/clients/99999999/rsrc/icons/16x16/row 9/1.png" title="Session Info ..." onclick="hookSession() ;"/>&nbsp;
		<img src="/clients/99999999/rsrc/icons/16x16/row 8/4.png" title="Go Back to last screen" onClick="hookBack() ;"/>&nbsp;
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
					echo "<div class=\"g".(string)$_level."p\" onClick=\"fold( '".$module->ModuleName."') ; return false ;\">" . FTr::tr( $mod->Label) . "</div>" ;
					echo "<div id=\"".$module->ModuleName."\" style=\"display: none;\" wapIsLoaded=\"true\" data-wap-fold=\"true\">" ;
					showLeftMenu( $module->ModuleName, $_level+1) ;
					$screen->setIterCond( "ApplicationId = '".$mySysSession->ApplicationId."' AND ModuleName = '". $mod->ModuleName."' ") ;
					$screen->setIterOrder( "SeqNo ") ;
					reset( $screen) ;
					foreach ( $screen as $key => $screen) {
						if ( $appUser->isGranted( "scr", $module->ModuleName.".".$screen->ScreenName) || $appUser->isGranted( "scr", $module->ModuleName.".*")) {
							echo "<div class=\"g".(string)($_level+1)."p\" onClick=\"showScreen( '".$screen->ScreenName."', false) ; return false ;\">" . FTr::tr( $screen->Label) . "</div>" ;
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
	require_once( "login.html") ;
	die() ;
}
?>
