<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "Pay.php") ;
FDbg::begin( 0, "redirectToPay.php", "*", "main") ;
EISSCoreObject::dumpGET() ;
EISSCoreObject::dumpPOST() ;
$myPaymentAgent	=	$_POST['PaymentAgent'] ;
$myPayAgentClass	=	"Pay_" . $myPaymentAgent ;
$myPayAgent	=	new $myPayAgentClass() ;
if ( isset( $_COOKIE['SessionId'])) {
	FDbg::trace( 0, FDBg::mdTrcInfo1, "CustomerInfo.php", "CustomerInfo{Page}", "main", "session cookie is set") ;
	$mySession	=	new Session( $_COOKIE['SessionId']) ;
	if ( isset( $mySession->CuCartNo)) {
		if ( $mySession->CuCartNo == "") {
			if ( isset( $_GET['CuCartNo'])) {
				$mySession->CuCartNo	=	$_GET['CuCartNo'] ;
			}
		}
	} else if ( isset( $_GET['CuCartNo'])) {
		$mySession->CuCartNo	=	$_GET['CuCartNo'] ;
	}
} else {
	FDbg::trace( 0, FDbg::mdTrcInfo1, "CustomerInfo.php", "CustomerInfo{Page}", "main", "session cookie is NOT set") ;
}
?>
<html>
	<head>
	</head>
	<body>
  		<form name="goPay" method="post" action="<?php echo $myPayAgent->getSubmitURL() ; ?>">
  			<?php echo $myPayAgent->getForm() ; ?>
  			<input type="input" name="cartId" value="<?php echo $mySession->CuCartNo ;?>"/>
  			<input type="submit" value="Continue" />
  		</form>
 		<script type="text/javascript">document.goPay.submit();</script>
	</body>
<!--
-->
</html>
<?php
FDbg::end( 0, "redirectToPay.php", "*", "main") ;
?>
