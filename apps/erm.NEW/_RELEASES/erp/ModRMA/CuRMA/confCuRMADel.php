<?php
require_once( "config.inc.php") ;
?>
<html dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div>
<?php
echo FTr::tr( "Shall this <b>customer order</b><br/>really be deleted ?<br/>") ;
?>
<form onsubmit="return false ;">
<input type="submit" name="A2" value="<?php echo FTr::tr( "Yes, delete it") ; ?>" onclick="confGo() ; return false ;" />
<input type="submit" name="A1" value="<?php echo FTr::tr( "No, don't delete") ; ?>" onclick="confCancel() ; return false ;" />
</form>
</div>
</body>
</html>
