<!DOCTYPE html>
<meta content="text/html; charset=utf-8">

<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
//	include_once('../../lib/core/EISSCoreObject.php');
//	include_once('../../lib/core/FDb.php');
//	include_once('../../lib/core/FDbg.php');
//	include_once('../../lib/core/FDbObject.php');
//	include_once('../../lib/sys/Reply.php');

	FDbg::setLevel( 999) ;
	FDbg::enable() ;

	//FDb::registerDb('kh-s08-mssql08', 'erpdemo', 'demoerp', 'TicketSystemTest', 'def', 'mssql');
	$TestObject = new FDbObject('Mitarbeiter');
	//error_log( "isValid: " . $TestObject->isValid()) ;
	$res	=	$TestObject->getNextAsXML() ;
	/*
	error_log( "isValid: " . $TestObject->isValid()) ;

	$TestObject->Vorname	=	"Hans-Dieter Müller" ;
	$TestObject->storeInDb() ;

	$res	=	$TestObject->getNextAsXML() ;
	error_log( $res->replyData) ;
	$res	=	$TestObject->getNextAsXML() ;
	error_log( $res->replyData) ;

	$res	=	$TestObject->getNextAsXML() ;
	*/
	//error_log( "Vorname: " . $TestObject->Vorname . $TestObject->Nachname) ;

	//TODO: get username from database for login id



	//$username = "Frieda M&uuml;ller";
	$username = $TestObject->Vorname." ".$TestObject->Nachname;
	//TODO! you should get the roles from the database
	$UserRoles = array('sysadmin');

	$UserRolesString = array();

	if(in_array('worker', $UserRoles)){array_push($UserRolesString, 'Bearbeiter');}
	if(in_array('supervisor', $UserRoles)){array_push($UserRolesString, 'Supervisor');}
	if(in_array('service', $UserRoles)){array_push($UserRolesString, 'Service');}
	if(in_array('sysadmin', $UserRoles)){array_push($UserRolesString, 'System-Admin');}

if ( $mySysSession->Validity == SysSession::VALIDAPP) {
	$mySysUser	=	$mySysSession->SysUser ;
	$scriptPath	=	$_SERVER["DOCUMENT_ROOT"] . "/"
				.	"clients"
				.	$mySysSession->ClientId . "/apps/"
				.	$mySysSession->ApplicationSystemId . "/"
 				.	$mySysSession->ApplicationId . "/scripts/"
 				;
				?>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<title>Frieda (<?php echo $username;?>)</title>
		<script src="/jQuery/jquery-2.1.3.min.js"></script>
		<script src="Main.js"></script>
		<script src="CreateNews.js"></script>
		<script src="GUIElements.js"></script>
		<script src="DisplayDetailTables.js"></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=debug.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=common.js" ></script>
		<script type="text/javascript" src="/api/loadScript.php?sessionId=<?php echo $mySysSession->SessionId ; ?>&script=wapDataSource.js" ></script>

		<link rel="stylesheet" type="text/css" href="Main.css">
		<script>
		sessionId		=	"<?php echo $mySysSession->SessionId ; ?>" ;
		</script>
	</head>
	<body>
		<center>
			<h2>Ticketsystem Frieda</h2>
			<table style="border-collapse:collapse;background-color:#DDDDDD">
				<tr>
					<!--colspan has to be equal number of functions available to current user minus 2 -->
					<td width="400">
						<?php echo $username;?>
					</td>
					<td width="500">
						<?php echo implode(", ", $UserRolesString)?>
					</td>
					<td align="right" width="200">
						<span onclick="hookLogoff();" class="clickable-span">Logout&nbsp;&nbsp;<img src="icons/Off-256.png" class="little-icon-button" title="abmelden"/></span>
					</td>
				</tr>
			</table>
			<table style="border-collapse:collapse;" id="MAIN">
				<tr>
					<?php
						class TabProperties
						{
							public $name = "";
							public $title = "";
							public $imgpath = "";

							public function __construct($name, $title, $imgpath)
							{
								$this->name = $name;
								$this->title = $title;
								$this->imgpath = $imgpath;
							}
						}

						$Tab_NewJob = new TabProperties('new-job', 'Neuen Job erstellen', 'Add-Job-256.png');
						$Tab_NewTicket = new TabProperties('new-ticket', 'Neues Ticket erstellen', 'Add-Ticket-256.png');
						$Tab_Tickets = new TabProperties('tickets', 'Tickets ansehen', 'Documents-256.png');
						$Tab_OwnTickets = new TabProperties('owntickets', 'Eigene Tickets ansehen', 'OwnTickets-256.png');
						$Tab_Requests = new TabProperties('requests', 'Requests', 'Request-256.png');
						$Tab_Jobs = new TabProperties('jobs', 'Jobs', 'Clipboard-256.png');
						$Tab_Customers = new TabProperties('customer-data', 'Kundendaten', 'Customer-256.png');
						$Tab_Messages = new TabProperties('messages', 'Nachrichten', 'Message-256.png');
						$Tab_Statistics = new TabProperties('statistics', 'Statistik', 'Graph-256.png');
						$Tab_Employees = new TabProperties('employees', 'Mitarbeiterdaten bearbeiten', 'Employee-256.png');
						$Tab_Groups = new TabProperties('groups', 'Gruppen', 'Group-256.png');
						$Tab_ReactionTeams = new TabProperties('reaction-teams', 'Reaktionsteams', 'Reaction-Team-256.png');
						$Tab_Escalation = new TabProperties('escalation', 'Eskalation', 'Escalation-256.png');
						$Tab_Einsatz = new TabProperties('einsatz', 'Einsatzarten', 'Van-256.png');

						$PermissionsService = array('new-job', 'jobs', 'tickets', 'customer-data', 'statistics', 'messages');
						$PermissionsWorker = array('new-job', 'new-ticket', 'tickets', 'owntickets', 'requests', 'jobs', 'customer-data', 'messages');
						$PermissionsSysadmin = array('employees', 'groups', 'reaction-teams', 'customer-data', 'escalation', 'einsatz', 'messages');
						$PermissionsSupervisor = array('tickets', 'jobs', 'statistics', 'messages');

						$UserPermissions = array();

						if(in_array('worker', $UserRoles)){$UserPermissions = array_merge($UserPermissions, $PermissionsWorker);}
						if(in_array('supervisor', $UserRoles)){$UserPermissions = array_merge($UserPermissions, $PermissionsSupervisor);}
						if(in_array('service', $UserRoles)){$UserPermissions = array_merge($UserPermissions, $PermissionsService);}
						if(in_array('sysadmin', $UserRoles)){$UserPermissions = array_merge($UserPermissions, $PermissionsSysadmin);}

						$UserPermissions = array_unique($UserPermissions);

						//echo implode(";", $UserPermissions);
						$TabCount = 0;

						foreach($UserPermissions as $permission)
						{
							$tab;

							switch($permission)
							{
								case 'new-job': {$tab = $Tab_NewJob;}break;
								case 'new-ticket': {$tab = $Tab_NewTicket;}break;
								case 'tickets': {$tab = $Tab_Tickets;}break;
								case 'owntickets': {$tab = $Tab_OwnTickets;}break;
								case 'requests': {$tab = $Tab_Requests;}break;
								case 'jobs': {$tab = $Tab_Jobs;}break;
								case 'customer-data': {$tab = $Tab_Customers;}break;
								case 'messages': {$tab = $Tab_Messages;}break;
								case 'statistics': {$tab = $Tab_Statistics;}break;
								case 'employees': {$tab = $Tab_Employees;}break;
								case 'groups': {$tab = $Tab_Groups;}break;
								case 'reaction-teams': {$tab = $Tab_ReactionTeams;}break;
								case 'escalation': {$tab = $Tab_Escalation;}break;
								case 'einsatz':{$tab = $Tab_Einsatz;}break;
								default: throw new Exception('Permission "'.$permission.'" does not exist.');
							}

							echo '<td class="head-icon" id="tab'.($TabCount + 1).'" name="'.$tab->name.'" onclick="ChangeTabTo(this)" title="'.$tab->title.'"> <img src="icons/'.$tab->imgpath.'" class="head-icon-img"/> </td>';

							$TabCount++;
						}

						echo '<td id="head-filler" width="'.(1100 - $TabCount *120).'"></td>';

						echo '</tr>	<tr> <td id= "frieda-body" class="MAIN" colspan="'.($TabCount + 1).'" height="500px">	</td>';
					?>

				</tr>
			</table>
		</center>
	</body>
</html>
<?php
} else {
	echo "Session not valid!" ;
}
?>
