<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
?>
<html dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div>
<?php echo FTr::tr( "Shall this <b>supplier order</b> really be deleted ?<br/>") ; ?>
			<form onsubmit="return false ;">
				<button data-dojo-type="dijit/form/Button" onclick="confGo() ; return false ;">
					<?php echo FTr::tr( "Yes, delete it") ; ?>
				</button>
				<button data-dojo-type="dijit/form/Button" onclick="confCancel() ; return false ;">
					<?php echo FTr::tr( "No, don't delete") ; ?>
				</button>
			</form>
</div>
</body>
</html>
