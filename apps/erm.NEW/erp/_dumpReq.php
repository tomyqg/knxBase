<?php

require_once( "gsGiropay.php") ;

echo "POST Variables.....:<br/>" ;
foreach ( $_POST as $ind => $val) {
	echo "Name: " . $ind . " :=> Value: " . $val . "<br/>" ;
}

echo "GET Variables.....:<br/>" ;
foreach ( $_GET as $ind => $val) {
	echo "Name: " . $ind . " :=> Value: " . $val . "<br/>" ;
}

$myGiropayTool	=	new gsGiropayTools() ;

$myMerchantId	=	"3600140" ;
$myProjectId	=	"74" ;
$myBankCode	=	"37050299" ;

$myHash	=	$myGiropayTool->generateHash( $myMerchantId.$myProjectId.$myBankCode, "psion0")

?>
<form name="f4" method="post" action="https://payment.girosolutions.de/payment/status/bank">
	<input type="text" name="merchantId" value="<?php echo $myMerchantId ; ?>" />
	<input type="text" name="projectId" value="<?php echo $myProjectId ; ?>" />
	<input type="text" name="bankcode" value="<?php echo $myBankCode ; ?>" />
	<input type="text" name="hash" value="<?php echo $myHash ; ?>" />
	<input name="check" type="submit" value="Check Bank ..." />
</form>
