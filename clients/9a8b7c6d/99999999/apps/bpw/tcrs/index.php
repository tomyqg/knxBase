<?php
/**
 * apps/bpw/DesignStudyR3/index.php
 * ================================
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
				.	$mySysSession->ApplicationId . "/scripts/"
				;
	$appUser	=	EISSCoreObject::__getAppUser() ;
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
	<head>
		<title>Designstudy Transport Container Rental System, Release 1</title>
		<script>
sessionId		=	"<?php echo $mySysSession->SessionId ; ?>" ;
		</script>

		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapBasic.css">
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapPopup.css">
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapBusy.css">
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapGrid.css">
		<link rel="stylesheet" type="text/css"
			href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=wapTree.css">
		<link rel="stylesheet" type="text/css" href="/rsrc/scripts/rome/rome.css" ></script>

		<style type="text/css" media="screen">

			.memu-icon {
				margin: 0;
				padding: 0;
				position: relative;
				width: 16px;
				height: 16px;
				margin: 0px 0px 0px 0px;
				float: left;
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
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapCommon.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapScreen.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapDataSource.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapEditor.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelector.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelect.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapGrid.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapTree.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapPopup.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapBusy.js" ></script>
		<script type="text/javascript" src="/rsrc/scripts/ckeditor/ckeditor.js" ></script>
		<script type="text/javascript" src="/rsrc/scripts/rome/rome.js" ></script>
		<script>
function	onLoad() {
//	dOpen( 9) ;
	showScreen( "splash") ;
	document.addEventListener( "keypress", hookEnterKey) ;
//	var intervalID	=	setInterval(
//							function() {
//								alert( "Interval reached") ;
//						}, 5000) ;
}
window.onbeforeunload = function(){
	return 'Are you sure you want to leave?';
};

		</script>
	</head>
	<body onload="onLoad()" id="page">
		<div id="topContainer">
			<img src="/clients/99999999/rsrc/images/bpw-logo-neu.png" onclick="myInfo() ; myDebug() ;" />
		</div>
		<div id="sepTop_Toolbar">
		</div>
		<div id="toolBar">&nbsp;&nbsp;
			<img src="/clients/99999999/rsrc/icons/16x16/row 1/8.png" title="Logoff" onClick="hookLogoff() ;"/>&nbsp;
			<img src="/clients/99999999/rsrc/icons/16x16/row 15/13.png" title="Reload this screen" onClick="hookReload() ;"/>&nbsp;
			<img src="/clients/99999999/rsrc/icons/16x16/row 8/4.png" title="Go Back to last screen" onClick="hookBack() ;"/>&nbsp;
		</div>
		<div id="sepToolbar_Center">
		</div>
		<div id="centerContainer">
			<div id="centerLeft">
				<div id="Navigator">
					<div class="g1p" onclick="fold( 'MainData') ; return false ;">Main Data</div>
					<div id="MainData" data-wap-fold="true" style="display: none;">
						<div class="g2p" onclick="showScreen( 'Customer', false) ; return false ;">Customers</div>
						<div class="g2p" onclick="showScreen( 'Container', false) ; return false ;">Containers</div>
						<div class="g2p" onclick="showScreen( 'AccountingPeriod', false) ; return false ;">Accounting Period</div>
					</div>
<?php
						if ( $appUser->isGranted( "scr", "ModAdmin")) {
?>
					<div class="g1p" onclick="fold( 'ModAdmin') ; return false ;">Administration</div>
					<div id="ModAdmin" data-wap-fold="true" wapisloaded="true" style="display: none;">
<?php
						if ( $appUser->isGranted( "scr", "ModAdmin.appUser")) {
?>
						<div class="g2p" onclick="showScreen( 'appUser', false) ; return false ;">User</div>
<?php
						}
?>
						<div class="g2p" onclick="showScreen( 'role', false) ; return false ;">Roles</div>
						<div class="g2p" onclick="showScreen( 'profile', false) ; return false ;">Profiles</div>
						<div class="g2p" onclick="showScreen( 'authObject', false) ; return false ;">Authorization Objects</div>
						<div class="g2p" onclick="showScreen( 'AppTrans', false) ; return false ;">Application Translations</div>
						<div class="g2p" onclick="showScreen( 'AppOption', false) ; return false ;">Application Options</div>
					</div>
<?php
						}
?>
				</div>
			</div>
			<div id="centerRight">
				<div id="infos">
					<div class="block block-list block-compare">
						<div class="block block-cart">
							<div class="block-title">
								<strong> <span>Debugger / Tracer</span>
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
								<strong><span>Misc. I</span></strong>
							</div>
							<div class="block-content" id="CartInfo"></div>
						</div>
					</div>
					<div class="block block-list block-compare">
						<div class="block block-cart">
							<div class="block-title">
								<strong><span>Misc. II</span></strong>
							</div>
							<div class="block-content" id="CartInfo"></div>
						</div>
					</div>
				</div>
			</div>
			<div id="center">
				<div id="splash" data-wap-screen="splash.html"></div>
				<div id="Customer" data-wap-screen="mainCustomer.xml" data-wap-screen-dir="Customer" data-wap-module-dir="Logistics" style="display: none;"></div>
				<div id="Container" data-wap-screen="mainContainer.xml" data-wap-screen-dir="Container" data-wap-module-dir="Logistics" style="display: none;"></div>
				<div id="AccountingPeriod" data-wap-screen="mainAccountingPeriod.xml" data-wap-screen-dir="AccountingPeriod" data-wap-module-dir="Logistics" style="display: none;"></div>
				<div id="appUser" data-wap-screen="appUser.xml" data-wap-screen-dir="AppUser" data-wap-module-dir="ModAdmin" style="display: none;"></div>
				<div id="role" data-wap-screen="role.xml" data-wap-screen-dir="Role" data-wap-module-dir="ModAdmin" style="display: none;"></div>
				<div id="profile" data-wap-screen="profile.xml" data-wap-screen-dir="Profile" data-wap-module-dir="ModAdmin" style="display: none;"></div>
				<div id="authObject" data-wap-screen="authObject.xml" data-wap-screen-dir="AuthObject" data-wap-module-dir="ModAdmin" style="display: none;"></div>
				<div id="AppTrans" data-wap-screen="mainAppTrans.xml" data-wap-screen-dir="AppTrans" data-wap-module-dir="ModAdmin" style="display: none;"></div>
				<div id="AppOption" data-wap-screen="mainAppOption.xml" data-wap-screen-dir="AppOption" data-wap-module-dir="ModAdmin" style="display: none;"></div>
				<div id="applicationSystem" data-wap-screen="applicationSystem.xml" data-wap-screen-dir="ApplicationSystem" data-wap-module-dir="ModOperator" style="display: none;"></div>
				<div id="client" data-wap-screen="client.xml" data-wap-screen-dir="Client" data-wap-module-dir="ModOperator" style="display: none;"></div>
				<div id="clientApplication" data-wap-screen="clientApplication.xml" data-wap-screen-dir="ClientApplication" data-wap-module-dir="ModOperator" style="display: none;"></div>
			</div>
		</div>
		<div id="sepCenter_Footer">
		</div>
		<div id="footer">
			<table>
				<tr>
					<td>
						<a href="http://www.bpw.de" alt="www.bpw.de" title="www.bpw.de" target="_blank">
							<img src="/clients/99999999/rsrc/images//logo-bpw.png" alt="BPW" />
					</a>
					</td>
					<td>
						<a href="http://www.ermax.dk" alt="www.ermax.dk" title="www.ermax.dk" target="_blank">
							<img src="/clients/99999999/rsrc/images//logo-ermax.png" alt="ermax" />
					</a>
					</td>
					<td>
					<a href="http://www.hbn.dk" alt="www.hbn.dk" title="www.hbn.dk" target="_blank">
						<img src="/clients/99999999/rsrc/images//logo-hbn-teknik.png" alt="HBN-Teknik" />
					</a>
					</td>
					<td>
					<a href="http://www.hestal.de" alt="www.hestal.de" title="www.hestal.de" target="_blank">
						<img src="/clients/99999999/rsrc/images//logo-hestal.png" alt="HESTAL" />
					</a>
					</td>
					<td>
					<a href="http://www.idemtelematics.com" alt="www.idemtelematics.com" title="www.idemtelematics.com" target="_blank">
						<img src="/clients/99999999/rsrc/images//logo-idem-telematics.png" alt="idem telematics" />
					</a>
					</td>
				</tr>
			</table>
			<center>
				<p>&copy; 2007-<?php echo EISSCoreObject::today() ; ?></p>
				The content of this website is intended solely for Hellmig EDV and
				BPW Wiehl internal review.<br />
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
