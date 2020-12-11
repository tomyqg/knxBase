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
//
// here we need to feed in wrapper code for the specific version to be used
//
//
// end of wrapper code for version management
//
	.	$mySysSession->ApplicationId . "/scripts/"
 				;
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<title>Frieda - KH's Mockup</title>
	<script>
sessionId		=	"<?php echo $mySysSession->SessionId ; ?>" ;
	</script>
	<link rel="stylesheet" type="text/css"
		href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=Styles.css" media="screen"
		title="Default screen stylesheet" />
	<link rel="stylesheet" type="text/css"
		href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=Calculator.css" media="screen"
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
	</style>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=debug.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=common.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapScreen.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapDataSource.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapEditor.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelector.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelect.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapGrid.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapPopup.js" ></script>
	<script>
function	onLoad() {
	dOpen( 9) ;
	/**
	 *
	 */
//	myRequest	=	new XMLHttpRequest() ;
//	myRequest.open( "GET", "splash.html") ;
//	myRequest.targetDiv	=		document.getElementById( "splash") ;
//	myRequest.addEventListener( 'load', function() {
//		dTrace( 1, "index.php", "*", "activate( ...)", "loaded data ...", "") ;
//		this.targetDiv.innerHTML	=	this.responseText ;
//	}) ;
//	myRequest.send() ;
	showScreen( "splash") ;
}
	</script>
<body onload="onLoad()" id="page">
		<div id="topContainer">
			<img alt="no image yet" onclick="myDebug() ;" />
		</div>
		<div style="height: 10px;"></div>
		<div id="toolBar">&nbsp;&nbsp;
			<img src="/clients/99999999/rsrc/icons/16x16/row 2/1.png" title="Delete this calculation"/>&nbsp;
			<img src="/clients/99999999/rsrc/icons/16x16/row 10/4.png" title="Mail to Approver" />&nbsp;
			<img src="/clients/99999999/rsrc/icons/16x16/row 11/5.png" title="Create New Calculation" />&nbsp;
		</div>
		<div style="height: 10px;"></div>
		<div id="centerContainer">
			<div id="centerLeft">
				<div id="Navigator">
					<div class="g1p" onClick="showScreen( 'Mitarbeiter', true) ; return false ;">Mitarbeiter</div>
					<div class="g1p" onClick="showScreen( 'Einsatzart', true) ; return false ;">Einsatzart</div>
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
							<div class="block-content"></div>
						</div>
					</div>
					<div class="block block-list block-compare">
						<div class="block block-cart">
							<div class="block-title">
								<strong> <span>Tyres</span>
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
				<div id="splash" wapScreen="splash.html"></div>
				<div id="Mitarbeiter" style="display: none;" wapScreen="Mitarbeiter.xml"></div>
				<div id="Einsatzart" style="display: none;" wapScreen="Einsatzart.xml"></div>
			</div>
		</div>
		<div id="footer">
			<center>
				<p>&copy; 2007-2015-02-25</p>
				The content of this website is intended solely for Hellmig EDV internal review.<br />
			</center>
		</div>
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
