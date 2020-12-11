
<font style="color:red;font-weight:bold;">Dies ist eine Demo des Benutzerinterfaces <span style="text-decoration:underline;">ohne jegliche Berechnungs-funktion</span></font>
<br/>
<style type="text/css">@import url("general.css");</style>

<fieldset>
	<legend>Bremsschema-Auswahl</legend>
	
	<table border=0>
		<tr>
			<td width="500">
				<p><font color="white">
					Bremsanlage
				</font><p>
				
				<select id="Brake_System_select" size="3" data-dojo-type="dijit.form.Select" name="Brake_System_select" width="300" onchange="CheckBrakeSystem();overview_actualize();">
					<option value="BPW / / EBS-Anlage">BPW / / EBS-Anlage</option>
					<option value="SCC / / BestThing">SCC / / BestThing</option>
				</select>
				
				<font color="white">
					<br/>
					<input id="manuelle Eingabe" data-dojo-type="dijit.form.CheckBox" value="manuelle Eingabe" onclick="BrakeSystemCheckCustomSystem();"checked="false"><label>manuelle Eingabe&#160;&#160;&#160;&#160;&#160;</label></input>
					<button type="button" data-dojo-type="dijit.form.Button" id="BrakeSystem_CustomSave" disabled="true">Benutzer-Anlage speichern</button>
				</font>
			</td>
			<td>
				<fieldset>
					<legend style="color:white">Filter</legend>
					
					<font color="white">
						<input type="radio" data-dojo-type="dijit.form.RadioButton" name="brake_system_radio" id="brake_system_radio1" value="alle anzeigen" checked="true">alle anzeigen</input>
						<br/>
						<input type="radio" data-dojo-type="dijit.form.RadioButton" name="brake_system_radio" id="brake_system_radio2" value="Benutzeranlagen">Benutzeranlagen</input>
						<br/>
						<input type="radio" data-dojo-type="dijit.form.RadioButton" name="brake_system_radio" id="brake_system_radio3" value="Standardanlagen">Standardanlagen </input>
						<br/>
					</font>
				</fieldset>
			</td>
		</tr>
	</table>
	
</fieldset>
			
<br/><br/>
			
<fieldset>
	<table border="0">
		<tr>
			<td>
				<font color="white">
					Hersteller
				</font>
			</td>
			<td>
				<font color="white">
				Anlagen-Nr.
				</font>
			</td>
			<td>
				<font color="white">
				Beschreibung
				</font>
			</td>
		</tr>
		<tr>
			<td width="100">
				<select id="BrakeSystem_hersteller_select" size="3" data-dojo-type="dijit.form.Select" data-dojo-props="disabled:true" name="Brake_System_hersteller_select" width="100">
					<option value="BPW">BPW</option>
				</select>
			</td>
			<td width="200">
				<input type="text" style="width:100%" id="BrakeSystem_Anlagen_nr" data-dojo-type="dijit.form.TextBox" data-dojo-props="disabled:true" value="EBS-Anlage"/>
			</td>
			<td width="350">
				<input type="text" style="width:100%" id="BrakeSystem_Beschreibung" data-dojo-type="dijit.form.TextBox" data-dojo-props="disabled:true" value="EBS-Anlage"/>
			</td>
		</tr>
	</table>
</fieldset>

<div id='menu' style='position:absolute;top:-250;left:0;z-index:300;visibility:hidden;'>
	<table width='"+menuWidth+"' height='"+menuHeight+"' style='background-color:#D3D3D3'>
		<!-- <tr><td onclick='connecting_with_upper_cell();' class="menu_background"><a href='#' style='text-decoration:none;'>Verbinde mit oberer Zelle</a></td></tr> -->
		<tr><td onclick='connecting_with_lower_cell();' class="menu_background"><a href='#' style='text-decoration:none;'>mit n&auml;chster Achse verbinden</a></td></tr>
		<tr><td onclick='disconnect_cell();' class="menu_background"><a href='#'style='text-decoration:none;'>diese Achse trennen</a></td></tr>
		<tr><td onclick='delete_cell()' class="menu_background"><a href='#'style='text-decoration:none;'>diese Achse l&ouml;schen</a></td></tr>
	</table>
</div>
			
<br/>

<!--<font style="cursor:pointer;width:200px;background-color:green" onclick="addcolumn();">&#160;&#160;&#160;+&#160;&#160;&#160;</font>-->
<!--<font style="cursor:pointer;width:200px;background-color:red" onclick="delcolumn();">&#160;&#160;&#160;-&#160;&#160;&#160;</font>-->
<br/><br/>
			
<table id="table_for_ventil_table" style="visibility:collapse;">
	<tr>
		<td><div id="table"></div></td>
	</tr>
</table>

<div id="popupdiv"></div>
			
	
			