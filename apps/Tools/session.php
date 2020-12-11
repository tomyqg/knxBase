<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
FDbg::begin( 0, "hdlObjectXML.php", "*", "main()") ;
FDbg::trace( 0, FDbg::mdTrcInfo1, "dispatchXML.php", "*", "main()", "session Validity := ".$mySysSession->Validity) ;
$mySysUser	=	EISSCoreObject::__getSysUser() ;
$myAppUser	=	EISSCoreObject::__getAppUser() ;
//if ( locale_accept_from_http) {
//	$locale	=	locale_accept_from_http( $_SERVER['HTTP_ACCEPT_LANGUAGE']) ;
//} else {
//	$locale	=	"NOT SUPPORTED" ;
//}
$locale	=	"de_DE" ;
FDbg::end() ;
?>
<script>
</script>
<center><h1>wimtecc - wapCore Info</h1></center><br/>
<fieldset>
	<legend>Core System Data</legend>
	<table>
		<tr><td>Accept-Language:</td><td><?php echo $locale ; ?></td></tr>
		<tr><td>DocumentRoot:</td><td><?php echo $_SERVER["DOCUMENT_ROOT"] ; ?></td></tr>
		<tr><td>Server IP:</td><td><?php echo $_SERVER['REMOTE_ADDR'] ; ?></td></tr>
		<tr><td>System Database Id:</td><td><?php echo FDb::getId( "sys") ; ?></td></tr>
		<tr><td>Application Database Id:</td><td><?php echo FDb::getId( "def") ; ?></td></tr>
		<tr><td>Today:</td><td><?php echo EISSCoreObject::today() ; ?></td></tr>
	</table>
</fieldset>
<fieldset>
	<legend>Application System Data</legend>
	<table>
		<tr><td>Client Id:</td><td><?php echo $mySysSession->ClientId ; ?></td></tr>
		<tr><td>Application System Id:</td><td><?php echo $mySysSession->ApplicationSystemId ; ?></td></tr>
		<tr><td>Application Id:</td><td><?php echo $mySysSession->ApplicationId ; ?></td></tr>
	</table>
</fieldset>
<fieldset>
	<legend>System User Data</legend>
	<table>
		<tr><td>System User:</td><td><?php echo $mySysUser->UserId ; ?></td></tr>
		<tr><td>User Language:</td><td><?php echo $mySysUser->Lang ; ?></td></tr>
	</table>
</fieldset>
<fieldset>
	<legend>Application User Data</legend>
	<table>
		<tr><td>Application User:</td><td><?php echo $myAppUser->UserId ; ?></td></tr>
		<tr><td>User Language:</td><td><?php echo $myAppUser->Lang ; ?></td></tr>
	</table>
</fieldset>
<fieldset>
	<legend>Application Data</legend>
	<table>
		<tr><td>Application System Database Id:</td><td><?php echo FDb::getId( "appSys") ; ?></td></tr>
		<tr><td>Application Database Id:</td><td><?php echo FDb::getId( "def") ; ?></td></tr>
		<?php 	if ( isset( $mySysConfig->ui)) {		?>
		<tr><td>User-Interface Database Id:</td><td><?php echo FDb::getId( $mySysConfig->ui->dbAlias) ; ?></td></tr>
		<?php 	}								?>
		<?php 	if ( $myAppUser != null) {		?>
		<tr><td>Authorized User:</td><td><?php echo $myAppUser->UserId ; ?></td></tr>
		<tr><td>User Language:</td><td><?php echo $myAppUser->Lang ; ?></td></tr>
		<tr><td>SCR:</td><td></td></tr>
		<tr><td colspan="2"><?php echo $myAppUser->toString( "", $myAppUser->scr, "<br/>") ;?></td>
		<tr><td>DBT:</td></tr>
		<tr><td colspan="2"><?php echo $myAppUser->toString( "", $myAppUser->dbt, "<br/>") ;?></td>
		<tr><td>DBV:</td></tr>
		<tr><td colspan="2"><?php echo $myAppUser->toString( "", $myAppUser->dbv, "<br/>") ;?></td>
		<tr><td>FNC:</td</tr>
		<tr><td colspan="2"><?php echo $myAppUser->toString( "", $myAppUser->fnc, "<br/>") ;?></td>
		<?php 	} else {						?>
			<tr><td>Authorized User:</td><td>No Application User Data available!</td></tr>
		<?php 	}								?>
	</table>
</fieldset>
