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
echo FTr::tr( "Shall this <b>colli</b><br/>really be deleted ?<br/>") ;
?>
<form onsubmit="return false ;">
<input type="submit" name="A2" value="Yes -> ..." onclick="confGo() ; return false ;" />
<input type="submit" name="A1" value="No -> Cancel" onclick="confCancel() ; return false ;" />
</form>
</div>
</body>
</html>