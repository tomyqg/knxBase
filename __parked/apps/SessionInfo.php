<!--
Example for an XML file describing a screen

Note:
All Ids will be automatically assigned is not given. Manual assignment is only needed in case
the node must be looked up, as it's for example sometimes needed to locate a tab in a tab container
 -->
<html
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:wap="http://www.openwap.org/wap">
	<div>
		<h1 class="page-title">Session Information</h1>
<?
	$mySysSession	=	EISSCoreObject::
	$mySysUser	=	EISSCoreObject::__getSysUser() ;
	$myAppUser	=	EISSCoreObject::__getAppUser() ;
	$mySysConfig	=	EISSCoreObject::__getSysConfig() ;
	$myAppConfig	=	EISSCoreObject::__getAppConfig() ;
?>
	<div class="data" style="overflow:scroll;">
		<table class="sessionInfo">
			<thead></thead>
			<tbody>
				<tr class="sessionInfoRow">
					<td class="sessionInfoLabel">Date:</td>
					<td><?php echo EISSCoreObject::today() ; ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">System User:</td>
					<td><?php echo $mySysUser->UserId ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">System Db: Host:</td>
					<td><?php echo $mySysConfig->dbSys->host ; ?>, Db: <?php echo $mySysConfig->dbSys->name ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">Application Db:</td>
					<td>Host: <?php echo $myAppConfig->def->dbHost ; ?>, Db: <?php echo $myAppConfig->def->dbName ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">Application User:</td>
					<td><?php echo ( $myAppUser->UserId != "" ? $myAppUser->UserId : "---undefined---") ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">Application System:</td>
					<td><?php echo $mySysSession->ApplicationSystemId ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">Application:</td>
					<td><?php echo $mySysSession->ApplicationId ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">Client id.:</td>
					<td><?php echo $mySysSession->ClientId ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">Session id.:</td>
					<td><?php echo $mySysSession->SessionId ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">Translation locale:</td>
					<td><?php echo FTr::getLocale() ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">User language:</td>
					<td><?php echo $mySysUser->Lang ; ?></td>
				</tr>
				<tr>
					<td class="sessionInfoLabel">Style Sheets in priority order<br/>(top highest priority):</td>
					<td>
<?php
function	showCSS( $_path) {
	if ( file_exists( $_path)) {
		echo "$_path<br/>" ;
		$dir	=	new DirectoryIterator( $_path);
		foreach ( $dir as $fileinfo) {
		    if ( $fileinfo->getExtension() == "css") {
		        echo "CSS: " . $fileinfo->getFilename() . "<br/>" ;
		    }
		}
	}
}
showCSS( $_SERVER["DOCUMENT_ROOT"] . "/../clients/".$mySysSession->ClientId."/apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/rsrc/styles/") ;
showCSS( $_SERVER["DOCUMENT_ROOT"] . "/../clients/".$mySysSession->ClientId."/apps/".$mySysSession->ApplicationSystemId."/rsrc/styles/") ;
showCSS( $_SERVER["DOCUMENT_ROOT"] . "/../clients/".$mySysSession->ClientId."/apps/rsrc/styles/") ;
showCSS( $_SERVER["DOCUMENT_ROOT"] . "/../clients/".$mySysSession->ClientId."/rsrc/styles/") ;
showCSS( $_SERVER["DOCUMENT_ROOT"] . "/../apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/rsrc/styles/") ;
showCSS( $_SERVER["DOCUMENT_ROOT"] . "/../apps/".$mySysSession->ApplicationSystemId."/rsrc/styles/") ;
showCSS( $_SERVER["DOCUMENT_ROOT"] . "/../apps/rsrc/styles/") ;
?>
					</td>
				</tr>
			</tbody>
		</thead>
    </div>
</html>
