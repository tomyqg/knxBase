<?php
require( dirname( __FILE__) . "/../../Config/config.inc.php") ;
$myUser	=	EISSCoreObject::__getUser() ;
?>
<center><h1>wimtecc - EISS R3</h1></center><br/>
<table>
<tr><td>Authorized User:</td><td><?php echo $myUser->UserId ; ?></td></tr>
<tr><td>User Language:</td><td><?php echo $myUser->Lang ; ?></td></tr>
<tr><td>DocumentRoot:</td><td><?php echo $_SERVER["DOCUMENT_ROOT"] ; ?></td></tr>
<tr><td>Environment:</td><td><?php echo $_SESSION['Env'] ; ?></td></tr>
<tr><td>Server IP:</td><td><?php echo $_SERVER['REMOTE_ADDR'] ; ?></td></tr>
<tr><td>Database Id:</td><td><?php echo FDb::getId() ; ?></td></tr>
<tr><td>PDF Combiner:</td><td><?php echo $myConfig->pdf->concatTool ; ?></td></tr>
<tr><td>Today:</td><td><?php echo EISSCoreObject::today() ; ?></td></tr>
</table>
