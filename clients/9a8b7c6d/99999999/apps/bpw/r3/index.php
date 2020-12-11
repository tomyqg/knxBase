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
	<title>Designstudy BrakeCalculator R3</title>
	<script>
sessionId		=	"<?php echo $mySysSession->SessionId ; ?>" ;
	</script>
	<link rel="stylesheet" type="text/css"
		href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=Styles.css" media="screen"
		title="Default screen stylesheet" />
	<link rel="stylesheet" type="text/css"
		href="/api/loadCSS.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&css=Calculator.css" media="screen"
		title="Default screen stylesheet" />
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=debug.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapCommon.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapScreenXML.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapDataSourceXML.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelector.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapSelectXML.js" ></script>
	<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapGridXML.js" ></script>
	<script>
var	activePage	=	"splash" ;
function	activate( _page) {
	if ( activePage != "") {
		myDiv	=	document.getElementById( activePage) ;
		myDiv.style.display	=	"none" ;
	}
	if ( document.getElementById( _page)) {
		activePage	=	_page ;
		myDiv	=	document.getElementById( activePage) ;
		myDiv.style.display	=	"block" ;
		wapIsLoaded	=	myDiv.getAttribute( "wapIsLoaded") ;
		if ( wapIsLoaded) {
			dTrace( 1, "index.php", "*", "activate( ...)", "div is already loaded ...", "") ;
		} else {
			dTrace( 1, "index.php", "*", "activate( ...)", "div is not yet loaded ...", "") ;
			myDiv.setAttribute( "wapIsLoaded", "true") ;
			myScreenName	=	myDiv.getAttribute( "wapScreen") ;
			if ( ! myScreenName) {
				myScreenName	=	"testScreen.xml" ;
			}
			/**
			 *
			 */
			myRequest	=	new XMLHttpRequest() ;
			myRequest.open( "GET", "/api/loadScreen.php?sessionId="+sessionId+"&screen="+myScreenName) ;
			myRequest.targetDiv	=		myDiv ;
			myRequest.addEventListener( 'load', function() {
				dTrace( 1, "index.php", "*", "activate( ...)", "loaded data ...", "") ;
				this.targetDiv.innerHTML	=	this.responseText ;
			}) ;
			myRequest.send() ;
			/**
			 *
			 */
			var fileref	=	document.createElement('script') ;
			fileref.setAttribute( "type", "text/javascript") ;
			fileref.screenName	=	myScreenName ;
			fileref.onload	=	function() {
												eval( this.screenName.replace( ".xml", "")+"()") ;
											} ;
			fileref.setAttribute( "src", "/api/loadScript.php?sessionId="+sessionId+"&script="+myScreenName.replace( "xml", "js")) ;
			if (typeof fileref != "undefined")
				document.getElementsByTagName("head")[0].appendChild(fileref) ;
		}
	} else {
		activePage	=	"" ;
	}
}
/**
 * _src			issuer of the event (calling DOM Object)
 * _tabCntrl	id of the tabController
 * _div			id of the div to activate
 */
function	showTab( _src, _tabCntrl, _div) {
	dBegin( 1, "index.php", "*", "showTab( <this>, '"+_tabCntrl+"', '"+_div+"'", "starting") ;
	myTabCntrl	=	document.getElementById( _tabCntrl) ;
	myDiv	=	document.getElementById( _div) ;
	if ( myTabCntrl && myDiv) {
		dTrace( 1, "index.php", "*", "showTab( ...)", "found both") ;
		myActiveTabId	=	myTabCntrl.getAttribute( "wapActiveTabId") ;
		myActiveDivId	=	myTabCntrl.getAttribute( "wapActiveDivId") ;
		if ( myActiveTabId) {
			dTrace( 1, "index.php", "*", "showTab( ...)", "wapActiveTabId := '"+myActiveTabId+"'") ;
			dTrace( 1, "index.php", "*", "showTab( ...)", "wapActiveTabId := '"+myActiveDivId+"'") ;
			myActiveTab	=	document.getElementById( myActiveTabId) ;
			myActiveDiv	=	document.getElementById( myActiveDivId) ;
			if ( myActiveTab && myActiveDiv) {
				myActiveTab.className	=	myActiveTab.className.replace( /(?:^|\s)active(?!\S)/g , '' );
				myActiveDiv.className	=	myActiveDiv.className.replace( /(?:^|\s)active(?!\S)/g , '' );
			}
		}
		myTabCntrl.setAttribute( "wapActiveTabId", _src.getAttribute( "id")) ;
		myTabCntrl.setAttribute( "wapActiveDivId", _div) ;
		myActiveTabId	=	myTabCntrl.getAttribute( "wapActiveTabId") ;
		myActiveDivId	=	myTabCntrl.getAttribute( "wapActiveDivId") ;
		dTrace( 1, "index.php", "*", "showTab( ...)", ">>>wapActiveTabId := '"+myActiveTabId+"'") ;
		dTrace( 1, "index.php", "*", "showTab( ...)", ">>>wapActiveTabId := '"+myActiveDivId+"'") ;
		myActiveTab	=	document.getElementById( myActiveTabId) ;
		myActiveDiv	=	document.getElementById( myActiveDivId) ;
		if ( myActiveTab && myActiveDiv) {
			dTrace( 1, "index.php", "*", "showTab( ...)", "activating Tab and Div") ;
			myActiveTab.className	+=	" active" ;
			myActiveDiv.className	+=	" active" ;
		}
	}
	dEnd( 1, "index.php", "*", "showTab( ...)", "starting") ;
}
function	onLoad() {
	dOpen( 99) ;
	/**
	 *
	 */
	myRequest	=	new XMLHttpRequest() ;
	myRequest.open( "GET", "splash.html") ;
	myRequest.targetDiv	=		document.getElementById( "splash") ;
	myRequest.addEventListener( 'load', function() {
		dTrace( 1, "index.php", "*", "activate( ...)", "loaded data ...", "") ;
		this.targetDiv.innerHTML	=	this.responseText ;
	}) ;
	myRequest.send() ;
}
	</script>
<body onload="onLoad()" id="page">
		<div id="topContainer">
			<img src="/clients/99999999/rsrc/images/bpw-logo-neu.png" />
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
					<div class="g1p" onClick="activate( 'trailerTypeSelection') ; return false ;">
					Trailer Type Selection
						<img src="/clients/99999999/rsrc/icons/16x16/row 4/13.png" />
					</div>
					<div class="g1p" onClick="activate( 'trailerData') ; return false ;">Trailer Data</div>
					<div class="g1p" onClick="activate( 'axleType') ; return false ;">Axle Type</div>
					<div class="g1p" onClick="activate( 'brake') ; return false ;">Brake</div>
					<div class="g1p" onClick="activate( 'tyre') ; return false ;">Tyre</div>
					<div class="g1p" onClick="activate( 'brakeSystem') ; return false ;">Brake System</div>
					<div class="g1p" onClick="activate( 'cylinder') ; return false ;">Cylinder</div>
					<div class="g1p" onClick="activate( 'evaluation') ; return false ;">Evaluation</div>
					<div id="evaluation" style="display: none;" wapIsLoaded="true" wapFold="true">
						<div class="g2p">Type 0</div>
						<a class="g2p" href="">Type I</a>
						<a class="g2p" href="">Type II</a>
						<a class="g2p" href="">Type III</a>
						<a class="g2p" href="">Abbremsung</a>
						<a class="g2p" href="">pm / pCylinder</a>
						<a class="g2p" href="">Distribution</a>
						<a class="g2p" href="">Liftable Axles</a>
					</div>
					<a class="g1p" href="">Print</a>
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
								<strong> <span>Trailer Data</span>
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
				<div id="splashMain">
					<div id="splash">
					</div>
				</div>
				<div id="trailerTypeSelection" style="display: none;">
				</div>
				<div id="trailerData" style="display: none;" wapScreen="trailerData.xml">
				</div>
				<div id="brake" style="display: none;" wapScreen="brake.xml">
				</div>
				<div id="brakeSystem" style="display: none;" wapScreen="brakeSystem.xml">
				</div>
				<div id="cylinder" style="display: none;">
				</div>
				<div id="axleType" style="display: none;">
				</div>
			</div>
		</div>
		<div id="footer">
			<center>
				<p>&copy; 2007-2015-02-25</p>
				The content of this website is intended solely for Hellmig EDV and
				BPW Wiehl internal review.<br />
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
	<title>Please login!</title>
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
