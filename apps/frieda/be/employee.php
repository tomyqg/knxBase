<h3>Mitarbeiter</h3>
<br/>
	<table class="standard-table">
		<!--Hole neuste 10 Einträge aus Datenbank, bei weiter Pfeil die nächsten 10, usw..-->
		<!--ZeilenID = MitarbeiterID aus Datenbank-->
		<tr> <th width="200px" >Vorname</th> <th width="200px">Nachname</th></tr>

		<?php
		require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
		//	include_once('../../lib/core/EISSCoreObject.php');
		//	include_once('../../lib/core/FDb.php');
		//	include_once('../../lib/core/FDbg.php');
		//	include_once('../../lib/core/FDbObject.php');
		//	include_once('../../lib/sys/Reply.php');

			$EmplyoeeFirst10Query = new FSqlSelect('Mitarbeiter');
			$EmplyoeeFirst10Query->addLimit(new FSqlLimit(0,10));
			$EmployeeTable = new FDbObject('Mitarbeiter');
			$EmployeesXMLString = $EmployeeTable->tableFromQuery($EmplyoeeFirst10Query);

			$EmployeesXML = simplexml_load_string($EmployeesXMLString);

			error_log($EmployeesXMLString);

			foreach($EmployeesXML->Mitarbeiter as $employeedata)
			{
				//echo 'Vorname: '.(string)$employeedata->Vorname.'<br/>';
				echo '<tr id="employee-'.(string)$employeedata->Id.'" onclick="ClickEmployeeEntry(this)"><td>'.(string)$employeedata->Vorname.'</td><td>'.(string)$employeedata->Nachname.'</td></tr>';
			}

		?>
		<!--
		<tr id="employee-21414125" onclick="ClickEmployeeEntry(this)"><td>Frieda</td><td>Müller</td></tr>
		<tr id="employee-21414126" onclick="ClickEmployeeEntry(this)"><td>Friedbert</td><td>Meier</td></tr>
		<tr id="employee-21414127" onclick="ClickEmployeeEntry(this)"><td>Bowser</td><td>Super</td></tr>
		<tr id="employee-21414128" onclick="ClickEmployeeEntry(this)"><td>MASTER</td><td>of the Universe</td></tr>
		<tr id="employee-21414129" onclick="ClickEmployeeEntry(this)"><td>Rosalina</td><td>Super</td></tr>
		<tr id="employee-21414130" onclick="ClickEmployeeEntry(this)"><td>Arthur</td><td>Dent</td></tr>
		<tr id="employee-21414131" onclick="ClickEmployeeEntry(this)"><td>Ford</td><td>Prefect</td></tr>
		<tr id="employee-21414132" onclick="ClickEmployeeEntry(this)"><td>Zaphod</td><td>Beblebrox</td></tr>
		<tr id="employee-21414133" onclick="ClickEmployeeEntry(this)"><td>Trisha</td><td>McMillan</td></tr>
		<tr id="employee-21414134" onclick="ClickEmployeeEntry(this)"><td>Marvin</td><td>the Robot</td></tr>
		-->
	</table>

	<center>
		<br/>
		<img src="icons/Arrow-Left-256.png" class="scroll-button"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mitarbeiternr. <span id="employee-nr-start">1</span> bis <span id="employee-nr-end">10</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="icons/Arrow-Right-256.png" class="scroll-button"/>
	</center>

	<div>
		<fieldset style="display:inline-block">
			<legend>neuen Mitarbeiter anlegen</legend>
			<input type="button" value="neuer Mitarbeiter" onclick="CreateNewEmployee()">
		</fieldset>
	</div>

	<div id="employee-detail" style="display:none">
		<br/>
		<span id="employee-detail-label" style="font-weight:bold"></span><br/>
		<table class="detail-table">
			<!--Aktualisiere bei Änderung obige Tabelle!!-->
			<tr>
				<td>
					<p>Vorname</p>
					<span id="employee-detail-firstname" onclick="ClickEditableTextEntry(this)" iseditable="false" maynull="<?php echo $EmployeeTable->mayNull("Vorname"); ?>" style="cursor:pointer;"></span>
				</td>
				<td>
					<p>Nachname</p>
					<span id="employee-detail-lastname" onclick="ClickEditableTextEntry(this)" iseditable="false" maynull="<?php echo $EmployeeTable->mayNull("Nachname"); ?>" style="cursor:pointer;"></span>
				</td>
			</tr>
			<tr>
				<td>
					<p>Email Office</p>
					<span id="employee-detail-emailoffice" onclick="ClickEditableTextEntry(this)" iseditable="false" maynull="<?php echo $EmployeeTable->mayNull("EmailAdresseOffice"); ?>" style="cursor:pointer;">DatabaseNotFound@YouAreAnIdiot.com</span>
				</td>
				<td>
					<p>Email Privat</p>
					<span id="employee-detail-emailprivate" onclick="ClickEditableTextEntry(this)" iseditable="false" maynull="<?php echo $EmployeeTable->mayNull("EmailAdressePrivate"); ?>" style="cursor:pointer;">DatabaseNotFound@YouAreAnIdiot.com</span>
				</td>
			</tr>
			<tr>
				<td>
					<p>Rolle(n)</p>
					<span id="employee-detail-role" onclick="ClickEditableCollectionEntry(this, 'roles')" iseditable="false" style="cursor:pointer;">Fallbearbeiter</span>

					<datalist id="roles">
						<!--value will be sent back to the server-->
						<!--fill datalist with customers from database, option-id should be role id from database-->
						<option label="" value="" option-id=""><!--empy option is really important!!!!-->
						<option label="Service" value="Service" option-id="23523523">
						<option label="Fallbearbeiter" value="Fallbearbeiter" option-id="23523524">
						<option label="System Admin" value="System Admin" option-id="23523525">
						<option label="Supervisor" value="Supervisor" option-id="23523526">
					</datalist>
				</td>
				<td>
					<p>Supervisor</p>
					<!--WOZU??!!-->
					<span id="employee-detail-supervisor" onclick="ClickEditableTextEntry(this)" iseditable="false" style="cursor:pointer;">---</span>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<p>Reaktionsteams</p><!--und Gruppen-->
					<!--Reaktionsteams nur zuweisbar wenn Mitarbeiter die Rolle SysAdmin hat-->
					<!--Ansonsten (Service/Bearbeiter/Supervisor): Auswahl für neues Reaktionsteam nicht vorhanden, stattdessen Hinweis: 'Reaktionsteam nur zuweisbar für SysAdmin'-->
					<span id="employee-detail-reactionteams" onclick="ClickEditableCollectionEntry(this, 'reaction_teams')" iseditable="false" style="cursor:pointer;">Datev<br/>PRO.CAD</span>

					<datalist id="reaction_teams">
						<!--value will be sent back to the server-->
						<!--fill datalist with customers from database, option-id should be role id from database-->
						<option label="" value="" option-id=""><!--empy option is really important!!!!-->
						<option label="EGBrake" value="EGBrake" option-id="23523523">
						<option label="Drucker" value="Drucker" option-id="23523524">
						<option label="Server" value="Server" option-id="23523525">
						<option label="Kampf gegen das Böse" value="Kampf gegen das Böse" option-id="23523526">
					</datalist>
				</td>
			</tr>
		</table>
	</div>
