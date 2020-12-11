<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
?>
<html dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div>
<?php
echo FTr::tr( "Shall the <b>supplier orders for direct shipment</b><br/>really be createed ?<br/>") ;
?>
	<button data-dojo-type="dijit/form/Button" onclick="confGo() ;">
		<?php echo FTr::tr( "Yes, please create") ; ?>
	</button>
	<button data-dojo-type="dijit/form/Button" onclick="confCancel() ;">
		<?php echo FTr::tr( "No, don't create") ; ?>
	</button>
</div>
</body>
</html>
