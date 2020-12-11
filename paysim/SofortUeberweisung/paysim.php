This is the Payment Simulator.<br/>
<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
FDbg::begin( 1, "paysim.php", "main", "main") ;
EISSCoreObject::dumpGET() ;
EISSCoreObject::dumpPOST() ;
FDbg::end( 1, "paysim.php", "main", "main") ;
?>
<?php
switch ( $_POST['paymentAgent']) {
case	"GiroPay"	:						?>
<fieldset>
	<legend>GiroPay Simulation</legend>
	<table>
		<tr>
			<td>PaymentAgent</td><td><?php echo $_POST['paymentAgent'] ;	?></td>
		</tr>
		<tr>
			<td>Transaction Id.</td><td><?php echo $_POST['transactionId'] ;	?></td>
		</tr>
		<tr>
			<td>Amount:</td><td><?php echo $_POST['amount'] ;	?></td>
		</tr>
		<tr>
			<td>Bankcode:</td><td><?php echo $_POST['bankcode'] ;	?></td>
		</tr>
		<tr>
			<td>URL Redirect:</td><td><?php echo $_POST['urlRedirect'] ;	?></td>
		</tr>
		<tr>
			<td>URL Notify:</td><td><?php echo $_POST['urlNotify'] ;	?></td>
		</tr>
	</table>
	<table>
		<tr>
			<td>
				<form name="goPay" method="post" action="<?php echo $_POST['urlRedirect']."&gpCode=4000&gpHash=HASH" ; ?>">
					<input type="input" name="gpCode" value="4000" />
					<input type="input" name="gpHash" value="<?php echo $_POST['hash'] ; ?>" />
					<input type="submit" value="Payment ok" />
				</form>
			</td>
		</tr>
		<tr>
			<td>
				<form name="goPay" method="post" action="<?php echo $_POST['urlRedirect']."&gpCode=4500&gpHash=HASH" ; ?>">
					<input type="input" name="gpCode" value="4500" />
					<input type="input" name="gpHash" value="<?php echo $_POST['hash'] ; ?>" />
					<input type="submit" value="Payment status unclear" />
				</form>
			</td>
		</tr>
		<tr>
			<td>
				<form name="goPay" method="post" action="<?php echo $_POST['urlRedirect']."&gpCode=4900&gpHash=HASH" ; ?>">
					<input type="input" name="gpCode" value="4900" />
					<input type="input" name="gpHash" value="<?php echo $_POST['hash'] ; ?>" />
					<input type="submit" value="Payment failed" />
				</form>
			</td>
		</tr>
		<tr>
			<td>
				<form name="goPay" method="get" action="<?php echo $_POST['urlRedirect']."&gpCode=4900&gpHash=HASH" ; ?>">
					<input type="input" name="gpCode" value="3100" />
					<input type="input" name="gpHash" value="<?php echo $_POST['hash'] ; ?>" />
					<input type="submit" value="User cancellation" />
				</form>
			</td>
		</tr>
		</table>
</fieldset>
<?php 
	break ;
case	"Paypal"	:						?>
<fieldset>
	<legend>Paypal Simulation</legend>
</fieldset>
<?php 
	break ;
case	"PaypalExpress"	:					?>
<fieldset>
	<legend>PaypayExpress Simulation</legend>
</fieldset>
<?php
	break ;
}
?>
