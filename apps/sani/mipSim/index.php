<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<h1>Übersicht der MIP Kostenvoranschläge</h1>
<?php

include_once( "ExperimentalBase/expClasses.php");
include_once( "Experimental/expClasses.php");

FDb::registerDb( "localhost:7188", "root", "", "mipsim", "mip", "mysql") ;

$myMIPKV  =   new MIPKostenvoranschlag() ;
$myMIPKV->setIterCond( array( "1 = 1")) ;
$myMIPKV->setIterOrder( array( "ERPNr")) ;
?>
<table style="border:1px;">
	<thead>
		<tr>
			<th>Id</th>
			<th>ERP Nr.</th>
			<th>intKvID</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ( $myMIPKV as $key => $mipKV) {
	?>
	<tr>
		<td><?php echo $mipKV->Id ;?></td>
		<td><?php echo $mipKV->ERPNr ;?></td>
		<td><?php echo $mipKV->intKvID ;?></td>
	</tr>
	<?php
}
?>
	</tbody>
</table>
</body>
</html>
