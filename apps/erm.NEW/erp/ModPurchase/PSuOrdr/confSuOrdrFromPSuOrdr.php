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
echo FTr::tr( "Shall this <b>temporary supplier order</b><br/>really be added as a supplier order ?<br/>") ;
?>
<form onsubmit="return false ;">
<input type="submit" name="A2" value="<?php echo FTr::tr( "Yes, create it") ; ?>" onclick="confGo() ; return false ;" />
<input type="submit" name="A1" value="<?php echo FTr::tr( "No, don't create") ; ?>" onclick="confCancel() ; return false ;" />
</form>
</div>
</body>
</html>
