<?php
/**
 * apps/bpw/calc/index.php
 * =======================
 * 
 * fetch the global configuration
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
?>
<html>
<head>
<link rel="shortcut icon" href="BRAKE32.ico">
<title>Brake Calc</title>
<!-- <link rel="stylesheet" href="/dojo/dojo-release-1.7.2/dijit/themes/claro/claro.css" type="text/css"/> -->

<!-- <link type="text/css" href="/jQuery/jquery-ui-1.8.23.custom/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" /> -->

<link rel="stylesheet" type="text/css" href="/dojo/dojo-release-1.9.1/dijit/themes/claro/claro.css" />
<link rel="stylesheet" href="css/jquery-ui-1.8.23/css/ui-lightness/jquery-ui-1.8.23.custom.css" type="text/css"/>

<style type="text/css">@import url("general.css");</style>
<style type="text/css">@import url("rightclickmenu.css");</style>
<script>
dojoConfig	=	{parseOnLoad: true} ;
sessionId		=	"<?php echo $mySession->SessionId ; ?>" ;
</script>
<script type="text/javascript" src="/dojo/dojo-release-1.9.1/dojo/dojo.js"></script>
<script type="text/javascript" src="Auswertung_table_string.js"></script>
<script type="text/javascript" src="/dojo/dojo-release-1.9.1/dojo/dojo.js">
</script>
<script type="text/javascript">
require([
 		"dojo/parser",
 		"dojo/domReady",
 		"dojo/io/iframe",
 		"dijit/form/Button",
 		"dijit/form/RadioButton",
 		"dijit/form/CheckBox",
 		"dijit/form/NumberTextBox",
 		"dijit/form/NumberSpinner",
 		"dijit/form/DateTextBox",
 		"dijit/layout/BorderContainer",
 		"dijit/layout/TabContainer",
 		"dijit/layout/AccordionContainer",
 		"dijit/layout/ContentPane",
 		"dijit/layout/AccordionPane",
 		"dijit/layout/StackContainer",
 		"dijit/layout/StackController",
 		"dijit/ProgressBar",
 		"dijit/Editor",
 		"dijit/DropDownMenu",
 		"dijit/MenuItem",
 		"dijit/Dialog",
 		"dijit/_editor/plugins/FontChoice",
// 		"dijit/_editor/plugins/TextColor",
 		"dijit/_editor/plugins/ViewSource",
 		"dijit/_editor/plugins/AlwaysShowToolbar",
 		"dijit/_editor/plugins/LinkDialog"
// 		"dojox/editor/plugins/TablePlugins",
 	]) ;
</script>

<script type="text/javascript" src="Bremsanlage.js"></script>
<script type="text/javascript" src="Auswertung_typ0_table_string.js"></script>
<script type="text/javascript" src="Auswertung_typ3_table_string.js"></script>
<script type="text/javascript" src="ShowPage.js"></script>
<script type="text/javascript" src="popup_dialog_script.js"></script>
<script type="text/javascript" src="Fahrzeugtyp.js"></script>
<script type="text/javascript" src="/jQuery/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="/jQuery/jquery-ui-1.8.23.custom/js/jquery-ui-1.8.23.custom.min.js"></script>

<script type="text/javascript">

//window.onDownloadEnd = ShowPage(1);

//definition globaler Variablen, die für Fahrzeugübersicht benötigt werden:
var axe_count;
var fahrzeugtyp;
var p_ges_beladen;
var p_ges_leer;
var axe_full = new Array(8);
var axe_empty = new Array(8);
var hr_leer;
var hr_beladen;
var Er_leer;
var Er_beladen;
var aggregat_typ;
var bremsen;
var reifen;
var bremsanlage;
var abbremsung_z_0;
var overview_state = 1;

var column_count;

var right_click_x;
var right_click_y;

var actual_clicked_ventil = "";

//Kontextmenü wird nie angezeigt:
document.oncontextmenu=function(){return false;};

function printf_overwrite(div, text)
{
	var ausgabe = document.getElementById(div);
	ausgabe.innerHTML = text;
}
function printf(div, text)
{
	var ausgabe = document.getElementById(div);
	ausgabe.innerHTML += text;
}
//stack controler für die ContentPane:
function GetScreen(screenid)
{
	var stack_controler = dijit.byId("screenCntr");
	var myDijitScreen	=	dijit.byId(screenid);
	if(stack_controler)
	{
		stack_controler.selectChild(myDijitScreen);
	}
	else{ alert("stack_controler undefinied!");}
}
//Aktualisieren der Fahrzeugübersicht
function overview_actualize()
{
	var overview = document.getElementById("textarea_collection");
	overview.value = "";
	if(overview_state >= 1){overview.value += "Fahrzeugtyp:\n"+fahrzeugtyp +"\n\n";}
	if(overview_state >= 2)
	{
		overview.value += "Fahrzeugdaten:\nPges beladen:\nPges leer:\n\n    beladen   leer\n";
		for(var i=1; i<= axe_count;i++)
			{
			if(window.axe_full[i-1])overview.value += "A"+i+":   "+axe_full[i-1]+"kg "+axe_empty[i-1]+"kg\n";
			else overview.value += "A"+i+":\n";
			}
		overview.value += "hr:  "+hr_beladen+" - "+hr_leer+" mm\n" ;
		overview.value += "Er:  "+Er_beladen+" - "+Er_leer+" mm\n\n" ;
	}
	if(overview_state >= 3){overview.value += "Aggregattyp:\n"+aggregat_typ+"\n\n";}
	if(overview_state >= 4){overview.value += "Bremse:\n"+bremsen+"\n\n";}
	if(overview_state >= 5){overview.value += "Reifen:\n"+reifen+"\n\n";}
	if(overview_state >= 6){overview.value += "Bremsanlage:\n"+bremsanlage+"\n\n";}
	if(overview_state >= 7){overview.value += "Bestückung:\n\n";}
	if(overview_state >= 8){overview.value += "Abbremsung:\n"+Math.round(parseFloat(abbremsung_z_0)*1000)/10+"%";}
}
//Check der Eingaben unter "Fahrzeugdaten"
function CheckTrailerData()
{
	hr_leer = dijit.byId("schwrpkt_leer").get("value");
	hr_beladen = dijit.byId("schwrpkt_beladen").get("value");
	Er_leer = dijit.byId("rdstnd_bis").get("value");
	Er_beladen = dijit.byId("rdstnd_von").get("value");
	
	for(var i=1;i<=axe_count;i++)
	{
		axe_full[i-1] = dijit.byId(i+"_achse_beladen").get("value");
		axe_empty[i-1] = dijit.byId(i+"_achse_leer").get("value");
	}
	for(var i=axe_count;i<8;i++)
	{
		axe_full[i] = "";
		axe_empty[i] = "";
	}
	
}
//Check der Eingaben unter "Aggregat"
function CheckAggregatType()
{
	if(document.getElementById("aggregat_radio1").checked){aggregat_typ="Luftfederung" ;}
	if(document.getElementById("aggregat_radio2").checked){aggregat_typ="VA-Aggregat" ;}
	if(document.getElementById("aggregat_radio3").checked){aggregat_typ="Einzelachsen" ;}
	if(document.getElementById("aggregat_radio4").checked){aggregat_typ="VB-Aggregat" ;}
	if(document.getElementById("aggregat_radio5").checked){aggregat_typ="W-Aggregat" ;}
	if(document.getElementById("aggregat_radio6").checked){aggregat_typ="Hydraulikfederung" ;}
}
//Check der Eingaben unter "Bremsanlage"
function CheckBrakeSystem()
{
	bremsanlage = dijit.byId("Brake_System_select").get("value");
	
	document.getElementById("table").innerHTML = '';
	
	Bremsanlage_initialize();
}
//Check der Eingaben unter "Auswertung T0"
function Check_z_0()
{
	abbremsung_z_0 = dijit.byId("z_0").get("value");
}
//Check der Eingaben unter "Reifen"
function CheckTyre()
{
	reifen = dijit.byId("tyre_select").get("value");
}
//Check der Eingaben unter "Bremsen"
function CheckBrakes()
{
    if(document.getElementById("kind_of_brake_1").checked)
	{	
		bremsen = dijit.byId("SN_brake_select").get("value");
	}
    if(document.getElementById("kind_of_brake_2").checked)
	{	
		bremsen = dijit.byId("SB_brake_select").get("value");
	}
    if(document.getElementById("kind_of_brake_3").checked)
	{	
		bremsen = dijit.byId("N_brake_select").get("value");
	}
    if(document.getElementById("kind_of_brake_4").checked)
	{	
		bremsen = dijit.byId("FL_brake_select").get("value");
	}
    if(document.getElementById("kind_of_brake_5").checked)
	{	
		bremsen = dijit.byId("S_brake_select").get("value");
	}
}
//disable/able Formelemente zur manuellen Einrichtung eines Formelements
function BrakeSystemCheckCustomSystem()
{
		dijit.byId("BrakeSystem_hersteller_select").setAttribute('disabled', !dijit.byId("manuelle Eingabe").checked);
		dijit.byId("BrakeSystem_Anlagen_nr").setAttribute('disabled', !dijit.byId("manuelle Eingabe").checked);
		dijit.byId("BrakeSystem_Beschreibung").setAttribute('disabled', !dijit.byId("manuelle Eingabe").checked);
		dijit.byId("BrakeSystem_CustomSave").setAttribute('disabled', !dijit.byId("manuelle Eingabe").checked);
		if(dijit.byId("manuelle Eingabe").checked){$('#table_for_ventil_table').css('visibility', 'visible');}
		else{$('#table_for_ventil_table').css('visibility', 'collapse');}
}
//disable/able der selectboxen in abhängigkeit des radio buttons für "Bremsen"
function BrakeCheckRadio_DisableSelect()
{
    if(document.getElementById("kind_of_brake_1").checked)
	{	
    	dijit.byId("SN_brake_select").setAttribute('disabled', false);
    	dijit.byId("SB_brake_select").setAttribute('disabled', true);
    	dijit.byId("N_brake_select").setAttribute('disabled', true);
    	dijit.byId("FL_brake_select").setAttribute('disabled', true);
    	dijit.byId("S_brake_select").setAttribute('disabled', true);
	}
    if(document.getElementById("kind_of_brake_2").checked)
	{	
    	dijit.byId("SN_brake_select").setAttribute('disabled', true);
    	dijit.byId("SB_brake_select").setAttribute('disabled', false);
    	dijit.byId("N_brake_select").setAttribute('disabled', true);
    	dijit.byId("FL_brake_select").setAttribute('disabled', true);
    	dijit.byId("S_brake_select").setAttribute('disabled', true);
	}
    if(document.getElementById("kind_of_brake_3").checked)
	{	
    	dijit.byId("SN_brake_select").setAttribute('disabled', true);
    	dijit.byId("SB_brake_select").setAttribute('disabled', true);
    	dijit.byId("N_brake_select").setAttribute('disabled', false);
    	dijit.byId("FL_brake_select").setAttribute('disabled', true);
    	dijit.byId("S_brake_select").setAttribute('disabled', true);
	}
    if(document.getElementById("kind_of_brake_4").checked)
	{	
    	dijit.byId("SN_brake_select").setAttribute('disabled', true);
    	dijit.byId("SB_brake_select").setAttribute('disabled', true);
    	dijit.byId("N_brake_select").setAttribute('disabled', true);
    	dijit.byId("FL_brake_select").setAttribute('disabled', false);
    	dijit.byId("S_brake_select").setAttribute('disabled', true);
	}
    if(document.getElementById("kind_of_brake_5").checked)
	{	
    	dijit.byId("SN_brake_select").setAttribute('disabled', true);
    	dijit.byId("SB_brake_select").setAttribute('disabled', true);
    	dijit.byId("N_brake_select").setAttribute('disabled', true);
    	dijit.byId("FL_brake_select").setAttribute('disabled', true);
    	dijit.byId("S_brake_select").setAttribute('disabled', false);
	}
}
</script>

</head>
<body class="claro" style="background-color: #D3D3D3">
 	<div id="loadingOverlay" class="loadingOverlay pageOverlay">
		<div class="loadingMessage">Loading...</div>
	</div>
	<table border="0" cellpadding="20" cellspacing="1" width="1350"
		height="600">
		<tr>

			<!--  Menu Bar -->
			<div data-dojo-type="dijit/MenuBar" id="MenuBar"
				style="background-color: #AAAAAA">
				<div data-dojo-type="dijit/PopupMenuBarItem"
					style="background-color: #AAAAAA">
					<span>Berechnung</span>
					<div data-dojo-type="dijit/DropDownMenu" id="calculation_menu">
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Neu</div>
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Laden</div>
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Speichern</div>
						<div data-dojo-type="dijit/MenuSeparator"
							style="background-color: #DDDDDD"></div>
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Importieren</div>
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Exportieren</div>
						<div data-dojo-type="dijit/MenuSeparator"
							style="background-color: #DDDDDD"></div>
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">EBS-Parameter exportieren</div>
						<div data-dojo-type="dijit/MenuSeparator"
							style="background-color: #DDDDDD"></div>
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD" onClick="document.location.href = '/logout.php?logoff=' ;">Beenden</div>
					</div>
				</div>
				<div data-dojo-type="dijit/PopupMenuBarItem"
					style="background-color: #AAAAAA">
					<span>Optionen</span>
					<div data-dojo-type="dijit/DropDownMenu" id="option_menu">
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Voreinstellungen</div>
						<div data-dojo-type="dijit/PopupMenuItem"
							style="background-color: #DDDDDD">
							<span>Benutzer</span>
							<div data-dojo-type="dijit/Menu" id="user_menu">
								<div data-dojo-type="dijit/MenuItem"
									style="background-color: #DDDDDD">Benutzer wechseln</div>
								<div data-dojo-type="dijit/MenuItem"
									style="background-color: #DDDDDD">Passwort ändern</div>
							</div>
						</div>

						<div data-dojo-type="dijit/PopupMenuItem"
							style="background-color: #DDDDDD">
							<span>Datenbank</span>
							<div data-dojo-type="dijit/Menu" id="sql_menu">
								<div data-dojo-type="dijit/MenuItem"
									style="background-color: #DDDDDD">Datenbank aktualisieren</div>
								<div data-dojo-type="dijit/MenuItem"
									style="background-color: #DDDDDD">Datenbank neu einlesen</div>
							</div>
						</div>
					</div>
				</div>
				<div data-dojo-type="dijit/PopupMenuBarItem"
					style="background-color: #AAAAAA">
					<span>Hilfe</span>
					<div data-dojo-type="dijit/DropDownMenu" id="help_menu">
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Hilfe anzeigen</div>
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Info anzeigen</div>
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Größe zurücksetzen</div>
						<div data-dojo-type="dijit/MenuItem"
							style="background-color: #DDDDDD">Über BPW Brake Calculator</div>
					</div>
				</div>

				<div data-dojo-type="dijit/MenuBarItem"
					style="background-color: #C0C0C0" data-dojo-props="disabled:false"
					onclick="window.open('version_info.html');">
					<span>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;Version B.1.0
						auf BrakeCalc.2</span>
				</div>

			</div>
			<div id="toolbar" data-dojo-type="dijit/Toolbar">
				<div data-dojo-type="dijit/form/Button" id="toolbar.newpage"
					data-dojo-props="iconClass:'dijitEditorIcon dijitEditorIconNewPage', showLabel:false">New</div>
				<!--  <div data-dojo-type="dijit/form/Button" id="toolbar.copy"
        data-dojo-props="iconClass:'dijitEditorIcon dijitEditorIconCopy', showLabel:false">Copy</div> -->
				<div data-dojo-type="dijit/form/Button" id="toolbar.save"
					data-dojo-props="iconClass:'dijitEditorIcon dijitEditorIconSave', showLabel:false">Save</div>
				<!-- The following adds a line between toolbar sections
        -->
				<span data-dojo-type="dijit/ToolbarSeparator"></span>
				<div data-dojo-type="dijit/form/ToggleButton" id="toolbar.print"
					data-dojo-props="iconClass:'dijitEditorIcon dijitEditorIconPrint', showLabel:false">Print</div>
				<span data-dojo-type="dijit/ToolbarSeparator"></span> <select
					id="toolbar_select" size="1" data-dojo-type="dijit/form/Select"
					name="toolbar_select">
					<option value="1TS">EG - Typ III</option>
					<div data-dojo-type="dijit/PopupMenuItem"
						style="background-color: #DDDDDD">
						<span>EG</span>
						<div data-dojo-type="dijit/Menu" id="sql_menu">
							<option value="1TS">Typ III</option>
							<option value="1TS">Typ II</option>
						</div>
					</div>
				</select> <span data-dojo-type="dijit/ToolbarSeparator"></span>
				<div data-dojo-type="dijit/form/ToggleButton" id="toolbar.dummy"
					data-dojo-props="iconClass:'dijitEditorIcon dijitEditorIconToggleDir', showLabel:false">dummy</div>
			</div>
		</tr>
		<tr>
			<!-- Sidebar -->
			<td width="180" style="background-color: #D3D3D3; font-size: 14px;"
				align="center" valign="top">
				<div data-dojo-type="dijit/DropDownMenu" id="navMenu" width="170"
					style="background-color: #D3D3D3; border: 0;">
					<div data-dojo-type="dijit/MenuItem" id="menuitem1"
						onClick="ShowPage(1)"
						style="color: #D3D3D3; background-color: 19196F">Fahrzeugtyp</div>
					<br />
					<div data-dojo-type="dijit/MenuItem" id="menuitem2"
						data-dojo-props="disabled:true" onClick="ShowPage(2)"
						style="color: #D3D3D3; background-color: #19196F">Fahrzeugdaten</div>
					<br />
					<div data-dojo-type="dijit/MenuItem" id="menuitem3"
						data-dojo-props="disabled:true" onClick="ShowPage(3)"
						style="color: #D3D3D3; background-color: #19196F">Aggregat</div>
					<br />
					<div data-dojo-type="dijit/MenuItem" id="menuitem4"
						data-dojo-props="disabled:true" onClick="ShowPage(4)"
						style="color: #D3D3D3; background-color: #19196F">Bremsen</div>
					<br />
					<div data-dojo-type="dijit/MenuItem" id="menuitem5"
						data-dojo-props="disabled:true" onClick="ShowPage(5)"
						style="color: #D3D3D3; background-color: #19196F">Reifen</div>
					<br />
					<div data-dojo-type="dijit/MenuItem" id="menuitem6"
						data-dojo-props="disabled:true" onClick="ShowPage(6)"
						style="color: #D3D3D3; background-color: #19196F">Bremsanlage</div>
					<br />
					<div data-dojo-type="dijit/MenuItem" id="menuitem7"
						data-dojo-props="disabled:true" onClick="ShowPage(7)"
						style="color: #D3D3D3; background-color: #19196F">Bestückung</div>
					<br />
					<div data-dojo-type="dijit/MenuItem" id="menuitem8"
						data-dojo-props="disabled:true" onClick="ShowPage(8)"
						style="color: #D3D3D3; background-color: #19196F">
						Auswertung <br />Ventileinst.
					</div>
					<br />
					<div
						style="visibility: collapse; color: #D3D3D3; width: 50px; background-color: #5959AF"
						data-dojo-type="dijit/MenuItem" id="menuitem81"
						onClick="ShowPage(81)">Typ 0</div>
					<br />
					<div
						style="visibility: collapse; color: #D3D3D3; width: 50px; background-color: #5959AF"
						data-dojo-type="dijit/MenuItem" id="menuitem82"
						onClick="ShowPage(82)">Typ III</div>
					<br />
					<div
						style="visibility: collapse; color: #D3D3D3; width: 50px; background-color: #5959AF"
						data-dojo-type="dijit/MenuItem" id="menuitem83"
						onClick="ShowPage(83)">Abbremsung</div>
					<br />
					<div
						style="visibility: collapse; color: #D3D3D3; width: 50px; background-color: #5959AF"
						data-dojo-type="dijit/MenuItem" id="menuitem84"
						onClick="ShowPage(84)">pm/pZylinder</div>
					<br />
					<div
						style="visibility: collapse; color: #D3D3D3; width: 50px; background-color: #5959AF"
						data-dojo-type="dijit/MenuItem" id="menuitem85"
						onClick="ShowPage(85)">Bremskraftverteilung</div>
					<br />
					<div data-dojo-type="dijit/MenuItem" id="menuitem9"
						data-dojo-props="disabled:true" onClick="ShowPage(9)"
						style="color: #D3D3D3; background-color: #19196F">Ausdruck</div>
					<!--   <div data-dojo-type="dijit/MenuSeparator"></div>  -->
				</div>
			</td>
			<td width="870" style="background-color: #19196F; color: white"
				align="left" valign="top">
				<!--  Content Pane -->
				<div id="actl_pg_content_pane" dojoType="dijit/layout/ContentPane"
					style="padding: 0; border: 0; height: 120%; overflow: visible;">
					<div id="screenCntr" dojoType="dijit/layout/StackContainer"
						style="padding: 0; border: 0; overflow: visible; width: 850px;">
						<div dojoType="dijit/layout/ContentPane" id="TrailerType"
							href="Fahrzeugtyp.html" onDownloadEnd="ShowPage(1);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="TrailerData"
							href="Fahrzeugdaten.html" onDownloadEnd="ShowPage(2);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="Aggregate"
							href="Aggregat.html" onDownloadEnd="ShowPage(3);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="Brakes"
							href="Bremsen.html" onDownloadEnd="ShowPage(4);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="Tyre"
							href="Reifen.html" onDownloadEnd="ShowPage(5);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="BrakeSystem"
							href="Bremsanlage.php"
							onDownloadEnd="ShowPage(6);Bremsanlage_initialize();"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="Armament"
							href="Bestückung.html" onDownloadEnd="ShowPage(7);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="Calculation_Type_0"
							href="Auswertung_Type_0.html" onDownloadEnd="ShowPage(81);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="Calculation_Type_3"
							href="Auswertung_Type_3.html" onDownloadEnd="ShowPage(82);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="Calculation_Brake"
							href="Auswertung_Brake.html" onDownloadEnd="ShowPage(83);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="Calculation_pm"
							href="Auswertung_pm.html" onDownloadEnd="ShowPage(84);"
							style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane"
							id="Calculation_Brake_dif" href="Auswertung_Brake_dif.html"
							onDownloadEnd="ShowPage(85);" style="overflow: visible;"></div>
						<div dojoType="dijit/layout/ContentPane" id="Print"
							href="Ausdruck.html" onDownloadEnd="ShowPage(9);"
							style="overflow: visible;"></div>
					</div>
					<span id="screenCntrl" dojoType="dijit/layout/StackController"
						containerId="screenCntr" style="visibility: hidden;"> </span>
				</div>


			</td>
			</td>
			<td width="300" style="background-color: #19196F" align="center"
				valign="top">
				<div id="overview">
					<!-- Info Text Area (Hinweise zu aktuellem Screen) -->
					<textarea id="textarea1" name="textarea1"
						data-dojo-type="dijit/form/SimpleTextarea" style="width: 100%;"
						rows="10"></textarea>
					<!-- Fahrzeugübersicht Text Area -->
					<textarea id="textarea_collection"
						data-dojo-type="dijit/form/SimpleTextarea" style="width: 100%;"
						rows="37" value="Fahrzeugübersicht"></textarea>
				</div>
			</td>
		</tr>

	</table>
	<br />
	<br />
	<br />
	</center>

</body>
</html>
