<html>
<head>
<title>==&gt; MAS Login &lt;==</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" media="screen" href="/api/loadCSS.php?css=wapBasic.css" title="Version 1" />
<link rel="stylesheet" type="text/css" href="/api/loadCSS.php?css=wapBasic.css">
<link rel="stylesheet" type="text/css" href="/api/loadCSS.php?css=wapPopup.css">
<link rel="stylesheet" type="text/css" href="/api/loadCSS.php?css=wapBusy.css">
<link rel="stylesheet" type="text/css" href="/api/loadCSS.php?css=wapGrid.css">
<link rel="stylesheet" type="text/css" href="/api/loadCSS.php?css=wapTree.css">
<script type="text/javascript" src="/api/debug.js" ></script>
<script>
sessionId	=	"login" ;
screenTable		=	new Object() ;
</script>
<script type="text/javascript">
/**
 *
 */
function	init() {
	dBegin( 1, "index.html", "main", "init") ;
	myClientSelect	=	new wapSelectXML( this, "mySelect", {
											selectNode:	getFormField( "ClientApplicationKeyData", "ClientId", document)
										,	object:		"Client"
										,	key:		"ClientId"
										,	value:		"Name1"
										,	onDataSourceLoaded:	function( _parent, _data) {
												dBegin( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
												this.show() ;
												clientIdSelected( null) ;
												dEnd( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
											}
								}) ;
	myApplicationSystemSelect	=	new wapSelectXML( this, "mySelect", {
											selectNode:	getFormField( "ClientApplicationKeyData", "ApplicationSystemId", document)
										,	object:		"ApplicationSystem"
										,	key:		"ApplicationSystemId"
										,	value:		"Description1"
										,	onDataSourceLoaded:	function( _parent, _data) {
												dBegin( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
												this.show() ;
												myApplicationSelect.dataSource.val	=	"APerClient" ;
												myApplicationSelect.dataSource.dispatch( false, "getList", "ClientApplicationKeyData") ;
												dEnd( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
											}
								}) ;
	myApplicationSelect	=	new wapSelectXML( this, "mySelect", {
											selectNode:	getFormField( "ClientApplicationKeyData", "ApplicationId", document)
										,	object:		"Application"
										,	key:		"ApplicationId"
										,	value:		"Description1"
										,	onDataSourceLoaded:	function( _parent, _data) {
												dBegin( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
												this.show() ;
												dEnd( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
											}
									}) ;
	myProjectSelect	=	new wapSelectXML( this, "mySelect", {
											selectNode:	getFormField( "ClientApplicationKeyData", "ApplicationId", document)
										,	object:		"Application"
										,	key:		"ApplicationId"
										,	value:		"Description1"
										,	onDataSourceLoaded:	function( _parent, _data) {
												dBegin( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
												this.show() ;
												dEnd( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
											}
									}) ;
//	myClientSelect.refresh() ;
//	myApplicationSystemSelect.refresh() ;
//	myApplicationSelect.refresh() ;
//	dOpen( 999) ;
	dEnd( 1, "index.html", "main", "init") ;
}
//
//
//
function	onKeyClientId() {
	dBegin( 2, "index.html", "main", "onClientChanged") ;
//	scrLogin() ;
	dEnd( 2, "index.html", "main", "onClientChanged") ;
}
//
//
//
function	onKeyPassword( _event) {
	dBegin( 1, "index.html", "main", "onKeyPassword()") ;
	if ( _event.keyCode == 13) {
		scrLogin() ;
	}
	dEnd( 1, "index.html", "main", "onKeyPassword()") ;
}
//
//
//
function	scrLogin() {
	dBegin( 1, "index.html", "scrLogin", "main()") ;
	wapScreen.call( this, "Client") ;
	this.package	=	"ModLogin" ;
	this.module	=	"Login" ;
	this.coreObject	=	"ClientApplication" ;
	this.keyForm	=	"ClientApplicationKeyData" ;
	this.keyField	=	getFormField( 'ClientApplicationKeyData', 'ClientApplicationKey') ;
	this.dataSource	=	new wapDataSource( this, {
									object: "ClientApplication",
									form:	"ClientApplicationKeyData",
									fncGet:	"getAppData"
								}) ;		// dataSource for display
	this.onDataSourceLoaded	=	function( _scr, _xmlObj) {
		clientApplication	=	_xmlObj.getElementsByTagName( "ClientApplication")[0] ;
		pathApplication	=	clientApplication.getElementsByTagName( "PathApplication")[0].childNodes[0].nodeValue ;
		document.ClientApplicationKeyData.action	=	pathApplication ;		// this.dataSource.objects.ClientApplication[0].PathApplication ;
		document.ClientApplicationKeyData.submit() ;
	} ;
	myKey	=	new Array() ;
	myKey[0]	=	getFormField( 'ClientApplicationKeyData', 'ClientId').value ;
	myKey[1]	=	getFormField( 'ClientApplicationKeyData', 'UserId').value ;
	myKey[2]	=	getFormField( 'ClientApplicationKeyData', 'ApplicationSystemId').value ;
	myKey[3]	=	getFormField( 'ClientApplicationKeyData', 'ApplicationId').value ;
	this.dataSource.key	=	myKey ;
	this.dataSource.dispatch( false, "getAppData", "ClientApplicationKeyData") ;
	dEnd( 1, "index.html", "scrLogin", "main()") ;
}
function	clientIdSelected( _select) {
	dBegin( 1, "index.html", "scrLogin", "clientIdSelected()") ;
	myApplicationSystemSelect.dataSource.val	=	"ASPerClient" ;
	myApplicationSystemSelect.dataSource.dispatch( false, "getList", "ClientApplicationKeyData") ;
	dEnd( 1, "index.html", "scrLogin", "clientIdSelected()") ;
}
function	applicationSystemIdSelected( _select) {
	dBegin( 1, "index.html", "scrLogin", "clientIdSelected()") ;
	myApplicationSelect.dataSource.val	=	"APerClient" ;
	myApplicationSelect.dataSource.dispatch( false, "getList", "ClientApplicationKeyData") ;
	dEnd( 1, "index.html", "scrLogin", "clientIdSelected()") ;
}
</script>
<!--
		<script type="text/javascript" src="/api/loadScript.php?script=wapCommon.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?script=wapDataSource.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?script=wapScreen.js" ></script>	
		<script type="text/javascript" src="/api/loadScript.php?script=wapSelect.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?script=wapBusy.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?script=wapPopup.js" ></script>
-->
		<script type="text/javascript" src="/api/wapCommon.js" ></script>
		<script type="text/javascript" src="/api/wapDataSource.js" ></script>
		<script type="text/javascript" src="/api/wapScreen.js" ></script>
		<script type="text/javascript" src="/api/wapSelect.js" ></script>
		<script type="text/javascript" src="/api/wapBusy.js" ></script>
		<script type="text/javascript" src="/api/wapPopup.js" ></script>
</head>
<body style="background-image:url(/api/loadImage.php?image=login_bg.jpg)" onLoad="init() ;">
	<table width="100%" height="100%">
		<tbody>
			<tr>
				<td width="33%"></td>
				<td width="33%" style="vertical-align: middle; align: center;">
					<div id="Client">
						<form id="ClientApplicationKeyData" name="ClientApplicationKeyData" class="wapForm" method="post">
								<table border="0" cellspacing="0" cellpadding="0" style="color: black; background: rgba(255, 255, 255, 0.4);" >
									<tr>
										<td style="color: black; background: rgba(255, 255, 255, 0.4);" width="50%" align="right">Language:&nbsp;</td>
										<td width="50%" style="color: black;">
											<select id="selLang" name="selLang">
												<option value="-">Benutzer</option>
												<option value="de">Deutsch</option>
												<option value="en">English</option>
												<option value="es">Espanol</option>
												<option value="fr">Francais</option>
												<option value="nl">Dutch</option>
											</select>
										</td>
									</tr>
									<tr>
										<td style="color: black; background: rgba(255, 255, 255, 0.4);" align="right">Environment:&nbsp;</td>
										<td align="left">
											<select id="selEnv" name="selEnv">
												<option value="office-sinspert">Sinspert</option>
												<option value="office-bomig">Bomig</option>
											</select>
										</td>
									</tr>
									<tr>
										<td style="color: black; background: rgba(255, 255, 255, 0.4);" align="right">Client Id.:&nbsp;</td>
							        	<td>
							        		<input id="ClientId" name="ClientId" class="wapField" type="input" maxlength="32" value="<?php echo $_POST[ "ClientId"] ; ?>" />
							        	</td>
							        </tr>
		<!--
		 							<tr>
										<td style="color: black; background: rgba(255, 255, 255, 0.4);" align="right">Client Id.:&nbsp;</td>
							        	<td>
							        		<input wapType="text" wapMode="edit" wapAttr="a" name="ClientId" id="ClientId" type="text" class="inputtext_04" placeholder="ClientId" size="18" maxlength="12" onKeypress="onKeyClientId() ;" />
							        	</td>
							        </tr>
		 -->
									<tr>
										<td style="color: black; background: rgba(255, 255, 255, 0.4);" align="right">Application System Id.:&nbsp;</td>
							        	<td>
							        		<input id="ApplicationSystemId" name="ApplicationSystemId" class="wapField" type="input" maxlength="32" value="<?php echo $_POST[ "ApplicationSystemId"] ; ?>" />
							        	</td>
							        </tr>
									<tr>
										<td style="color: black; background: rgba(255, 255, 255, 0.4);" align="right">Application Id.:&nbsp;</td>
							        	<td>
							        		<input id="ApplicationId" name="ApplicationId" class="wapField" type="input" maxlength="32" value="<?php echo $_POST[ "ApplicationId"] ; ?>" />
							        	</td>
							        </tr>
									<tr>
										<td style="color: black; background: rgba(255, 255, 255, 0.4);" align="right">User Id.:&nbsp;</td>
							        	<td><input onblur=";" name="UserId" id="UserId" class="wapField" type="text" class="inputtext_04" size="18" placeholder="UserId" maxlength="12" value="<?php echo $_POST[ "UserId"] ; ?>" ></td>
							        </tr>
									<tr>
										<td style="color: black; background: rgba(255, 255, 255, 0.4);" align="right">Password:&nbsp;</td>
							        	<td><input onblur=";" name="Password" id="Password" class="wapField" type="password" class="inputtext_04" size="18" maxlength="12"
							        		onKeypress="onKeyPassword( event);" /></td>
							        </tr>
									<tr>
										<td style="color: black; background: rgba(255, 255, 255, 0.4);" align="right">Project Id.:&nbsp;</td>
							        	<td>
							        		<select id="ProjectId" name="ProjectId" class="wapField" data-wap-type="option" data-wap-attr="ProjectId" >
												<option value="p1">Project X</option>
							        		</select>
							        	</td>
							        </tr>
								</table>
						</form>
					</div>
				</td>
				<td width="33%"></td>
			</tr>
		</tbody>
	</table>
</body>
</html>
