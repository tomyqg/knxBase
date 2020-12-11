<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
?>
<html dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div>
echo FTr::tr( "Shall this <br/><b>Journal Template</b><br/> really be deleted ?<br/>") ;
<?php
?>
<form onsubmit="return false ;">
<input type="submit" name="A2" value="<?php echo FTr::tr( "Yes, delete it") ; ?>" onclick="confGo() ; return false ;" />
<input type="submit" name="A1" value="<?php echo FTr::tr( "No, don't delete") ; ?>" onclick="confCancel() ; return false ;" />
</form>
</div>
</body>
</html>
