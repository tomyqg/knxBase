<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$myConfig	=	EISSCoreObject::__getConfig() ;
$myUser	=	new User() ;
if ( isset( $_POST['UserId'])) {
	error_log( "POST[UserId] set") ;
	if ( $myUser->identify( $_POST['Password'], $_POST['UserId'])) {
		FTr::init( $myUser->Lang) ;
		error_log( "User identified") ;
		$_SESSION['UserId']	=	$myUser->UserId ;

		/**
		 * need to do this here since other chances to get the variable
		 * $authUser set have been passed already in config.inc.php
		 */
		$authUser	=	$_SESSION['UserId'] ;		// need to do this here since other chances to get this
	} else if ( $myUser->_status == 4712) {
		FTr::init( $myUser->Lang) ;
		error_log( "User outside of validity => will present login screen (login.html)") ;
		echo FTr::tr( "User account withdrawn") ;
		die() ;
	} else {
		error_log( "User *NOT* identified => will present login screen (login.html)") ;
		require_once( "login.html") ;
		die() ;
	}
} else {
	error_log( "POST[UserId] NOT set") ;
}
EISSCoreObject::__setUser( $myUser) ;
?>
<html dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $myConfig->base->siteName ; ?> - ERP</title>
<style type="text/css">
@import "/dojo/dojo-release-1.4.1/dijit/themes/tundra/tundra.css" ;
@import "/dojo/dojo-release-1.4.1/dojo/resources/dojo.css" ;
</style>
<script type="text/javascript" src="/dojo/dojo-release-1.4.1/dojo/dojo.js" djConfig="parseOnLoad: true"></script>
<script type="text/javascript">
dojo.require("dojo.parser") ;
dojo.require("dijit.Toolbar") ;
dojo.require("dijit.MenuBar");
dojo.require("dijit.PopupMenuBarItem");
dojo.require("dijit.Menu");
dojo.require("dijit.MenuItem");
dojo.require("dijit.PopupMenuItem");
dojo.require("dijit/form/BorderContainer") ;
dojo.require("dijit/layout/TabContainer") ;
dojo.require("dijit/form/StackContainer") ;
dojo.require("dijit/form/SplitContainer") ;
dojo.require("dijit/form/AccordionContainer") ;
dojo.require("dijit/layout/ContentPane") ;
dojo.require("dijit/form/AccordionPane") ;
dojo.require("dijit/form/Form") ;
dojo.require("dijit/form/DateTextBox") ;
dojo.require("dijit/form/NumberTextBox") ;
dojo.require("dijit/form/NumberSpinner") ;
dojo.require("dijit.Editor") ;
dojo.require("dijit.ProgressBar") ;
dojo.require("dijit._Widget") ;
dojo.require("dijit._editor.plugins.FontChoice") ;
dojo.require("dijit._editor.plugins.TextColor") ;
dojo.require("dijit._editor.plugins.ViewSource") ;
dojo.require("dijit._editor.plugins.LinkDialog") ;
dojo.require("dojox.editor.plugins.TablePlugins") ;
dojo.require("dojox.av.FLAudio") ;
dojo.require("dijit.Dialog") ;
dojo.require("dojo.io.iframe");
</script>
<script type="text/javascript" src="/Common/common.js.php" ></script>
<script type="text/javascript" src="/Common/commonRequest.js" ></script>
<script type="text/javascript" src="/Common/debug.js" ></script>
<script type="text/javascript" src="/Common/editor.js.php" ></script>
<script type="text/javascript" src="/Common/mailer.js.php" ></script>
<script type="text/javascript" src="/Base/KdKomm/sequencer.js.php" ></script>
<!--									-->
<!-- Module			"Master Data"		-->
<!--									-->
<!-- load the "Stock" module			-->
<script type="text/javascript" src="/Base/Stock/mainStockScan.js.php" ></script>
<!-- get the stylesheets				-->
<link rel="stylesheet" type="text/css" media="screen" href="/styles/v1<?php echo $_SESSION['UserId'] ; ?>.css" title="Version 1 <?php echo $_SESSION['UserId'] ; ?>" />
<link rel="alternate stylesheet" type="text/css" media="screen" href="/styles/v1.css" title="Version 1" />
<link rel="alternate stylesheet" type="text/css" media="screen" href="/styles/v1alt.css" title="Version 1 Alt" />
<!--				-->
</head>
<body class="tundra">
<?php
require_once( "config.inc.php") ;
require_once( "common.php") ;
?>
<div data-dojo-type="dijit/form/BorderContainer" style=" height:100%; width=100%; padding=0px; ">
	<div id="c3" data-dojo-type="dijit/layout/ContentPane" region="center" style="">
		<div id="screenCntr"
							data-dojo-type="dijit/form/StackContainer" style="">
			<div id="screenStockScan"
							data-dojo-type="dijit/layout/ContentPane" title="StockScan" href="/Base/Stock/mainStockScan.php"></div>
		</div>
		<span id="screenCntrl"
							data-dojo-type="dijit/form/StackController" containerId="screenCntr" style="visibility: hidden ; ">
		</span>
	</div>
	<div id="c5" data-dojo-type="dijit/layout/ContentPane" region="bottom" style="">
		<textarea id="debuginfo" rows="10" cols="160">
bottom
		</textarea>
	</div>
	<div id="divBeep"></div>
</div>
</body>
</html>
