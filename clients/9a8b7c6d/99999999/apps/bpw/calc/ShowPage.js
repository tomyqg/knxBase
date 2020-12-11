//Zeigt den entsprechenden Screen wenn man den entsorechenden Button anklickt, Führt aktualisierungen durch, enabled weiter Buttons
//in der Sidebar

function ShowPage(index)
{
	switch(index)
	{
	case 1:{
				GetScreen("TrailerType");
				var info = document.getElementById("textarea1");
				info.value = "Fahrzeugtyp wählen \n\n Achsenanzahl und -verteilung wählen";
				dijit.byId("menuitem2").setAttribute('disabled', false);
	        
				CheckTrailerType();
				overview_actualize();
	        
			}break;
			
	case 2:{
				GetScreen("TrailerData");
				var info = document.getElementById("textarea1");
				info.value = "Gesamtgewicht, Achslasten eingeben\n\nSchwerpunkthöhe beladen und leer\n\nRadstand(-bereich)\nangeben";
				dijit.byId("menuitem3").setAttribute('disabled', false);
            
				var fahrzeugdaten_achsen = document.getElementById("fahrzeugdaten_Achsen");     
            
				dojo.forEach(dijit.findWidgets(dojo.byId("fahrzeugdaten_Achsen")),function(w){w.destroyRecursive();});
				fahrzeugdaten_achsen.innerHTML = "";  
            
				for(var i=1; i<=axe_count;i++)
				{
					fahrzeugdaten_achsen.innerHTML 	+= '<fieldset><legend>'+i+'.Achse</legend>'
													+ '<input type="text" style="width:6em" id="'+i+'_achse_beladen" data-dojo-type="dijit.form.TextBox" value="8000" onblur="CheckTrailerData();overview_actualize();"/>'
													+ '&#160;&#160;&#160;&#160;&#160;&#160;&#160;'
													+ '<input type="text" style="width:6em" id="'+i+'_achse_leer" data-dojo-type="dijit.form.TextBox" value="1600" onblur="CheckTrailerData();overview_actualize();"/>'
													+ '</fieldset>';
				}		
			
				dojo.parser.parse(fahrzeugdaten_achsen);
				
				CheckTrailerData();
				if(overview_state == 1){overview_state++;}
				overview_actualize();
            }break;
			
	case 3:{
				GetScreen("Aggregate");
				var info = document.getElementById("textarea1");
				info.value = "Aggregattyp wählen \n\nEvtl. Luftfederung wählen\n\nMögliche Liftachsen wählen";
				dijit.byId("menuitem4").setAttribute('disabled', false);
            
				var aggregat_achsen = document.getElementById("aggregat_Achsen");
				dojo.forEach(dijit.findWidgets(dojo.byId("aggregat_Achsen")),function(w){w.destroyRecursive();});
				aggregat_achsen.innerHTML = "";
            
				for(var i=1; i<=axe_count;i++)
            	{
					if(i==8){aggregat_achsen.innerHTML += '<br/>'}
					aggregat_achsen.innerHTML += '<input id="aggregat_'+i+'_Achse" dojoType="dijit.form.CheckBox" value="'+i+'_Achse" checked="false"/><label>'+i+'. Achse&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;</label></input>';
            	}
				dojo.parser.parse(aggregat_achsen);
            
				CheckAggregatType();
				if(overview_state == 2){overview_state++;}
				overview_actualize();
			}break;
			
	case 4:{
				GetScreen("Brakes");
				var info = document.getElementById("textarea1");
				info.value = "Bremstyp und Gutachten auswählen";
				dijit.byId("menuitem5").setAttribute('disabled', false);
            
				CheckBrakes();
				if(overview_state == 3){overview_state++;}
				overview_actualize();
			}break;
			
	case 5:{
				GetScreen("Tyre");
				var info = document.getElementById("textarea1");
				info.value = "Reifen auswählen\n\nEvtl. Bereich angeben";
				dijit.byId("menuitem6").setAttribute('disabled', false);
			
				CheckTyre();
				if(overview_state == 4){overview_state++;}
				overview_actualize();
			}break;
			
	case 6:{
				GetScreen("BrakeSystem");
				var info = document.getElementById("textarea1");
				info.value = "Bremsanlage auswählen\n\noder\n\nmanuell zusammenstellen";
				dijit.byId("menuitem7").setAttribute('disabled', false);
	        
				CheckBrakeSystem();
				if(overview_state == 5){overview_state++;}
				overview_actualize();
	        
				Bremsanlage_initialize();
			}break;
			
	case 7:{
				GetScreen("Armament");
				var info = document.getElementById("textarea1");
				info.value = "Zyl.-Herst.und Bauart wählen\nmit \"automatischer Bestückung\" Programmvorschlag auswählen";
				dijit.byId("menuitem8").setAttribute('disabled', false);
	        
				if(overview_state == 6){overview_state++;}
				overview_actualize();
	        
				var bestueckung_achsen = document.getElementById("bestueckung_achsen");     
            
				dojo.forEach(dijit.findWidgets(dojo.byId("bestueckung_achsen")),function(w){w.destroyRecursive();});
				bestueckung_achsen.innerHTML = "";  
            
				for(var i=1; i<=axe_count;i++)
            	{
					bestueckung_achsen.innerHTML 	+= '<fieldset><legend color="white">'+i+'. Achse</legend><table border="0">'
													+ '<tr><td width="100"><select id="Bestückung_Hersteller_'+i+'" size="10" data-dojo-type="dijit.form.Select" name="Bestückung_Hersteller_'+i+'" style="width:100%">'
													+ '<option value="BPW">BPW</option></select></td><td width="100">'
													+ '<select id="Zylinder_Typ_'+i+'" size="10" data-dojo-type="dijit.form.Select" name="Zylinder_Typ_'+i+'" style="width:100%">'
													+ '<option value="M">M</option></select></td><td width="150">'
													+ '<input type="text" style="width:100%" id="Sachnummer_'+i+'" data-dojo-type="dijit.form.TextBox"/>'
													+ '</td><td width="100"><select id="Zylinder_Anz_'+i+'" size="10" data-dojo-type="dijit.form.Select" name="Zylinder_Anz_'+i+'" style="width:100%">'
													+ '<option value="1">1</option><option value="2">2</option></select>'
													+ '</td><td width="100">'
													+ '<select id="Zylinder_gr_'+i+'" size="10" data-dojo-type="dijit.form.Select" name="Zylinder_gr_'+i+'" style="width:100%">'
													+ '<option value="0">0</option></select></td><td width="75">'
													+ '<select id="Hebel_'+i+'" size="10" data-dojo-type="dijit.form.Select" name="Hebel_'+i+'" style="width:100%">'
													+ '<option value="0">0</option></select></td><td width="100">'
													+ '<input type="text" style="width:100%" id="Brmskrft_vert_'+i+'" data-dojo-type="dijit.form.TextBox"/>'
													+ '</td></tr><tr><td width="100"></td><td width="100"></td><td width="150"></td><td width="100"></td>'
													+ '<th width="175" colspan="2"><font color="white">'
													+ '<input id="achse_'+i+'_äußereRckst" data-dojo-type="dijit.form.CheckBox" disabled="true" value="achse_'+i+'_äußereRckst" checked="false"><label>&auml;ussere R&uuml;ckstellfeder</label></input>'
													+ '</font></th><td width="100"></td></tr></table></fieldset>';
            	}
            
				dojo.parser.parse(bestueckung_achsen);
			}break;
			
	case 8:{
				GetScreen("Calculation_Type_0");
				var info = document.getElementById("textarea1");
				info.value = "Gesetzliche Vorgaben\n\nBPW-Empfehlungen\nprüfen\nVentileinstellungen\nändern";
				dijit.byId("menuitem9").setAttribute('disabled', false);
				document.getElementById("menuitem81").style.visibility="visible";
				document.getElementById("menuitem82").style.visibility="visible";
				document.getElementById("menuitem83").style.visibility="visible";
				document.getElementById("menuitem84").style.visibility="visible";
				document.getElementById("menuitem85").style.visibility="visible";
			
				Check_z_0();
				if(overview_state == 7){overview_state++;}
				overview_actualize();
			
				var auswertung_table = document.getElementById("auswertung_typ0_table");     
				
				dojo.forEach(dijit.findWidgets(dojo.byId("auswertung_typ0_table")),function(w){w.destroyRecursive();});
				auswertung_table.innerHTML = "";  
            
				auswertung_table.innerHTML = Auswertung_typ0_table_string();
            
				dojo.parser.parse(auswertung_table);
			}break;
			
	case 81:{
				GetScreen("Calculation_Type_0");
				var info = document.getElementById("textarea1");
				info.value = "Gesetzliche Vorgaben\n\nBPW-Empfehlungen\nprüfen\nVentileinstellungen\nändern";
            
				Check_z_0();
				if(overview_state == 7){overview_state++;}
				overview_actualize();
			
				var auswertung_table = document.getElementById("auswertung_typ0_table");     
            
				dojo.forEach(dijit.findWidgets(dojo.byId("auswertung_typ0_table")),function(w){w.destroyRecursive();});
				auswertung_table.innerHTML = "";  
				
				auswertung_table.innerHTML = Auswertung_typ0_table_string();
            
				dojo.parser.parse(auswertung_table);
	 		}break;
			
	case 82:{
				GetScreen("Calculation_Type_3");
	        
				var auswertung_table = document.getElementById("auswertung_typ3_table");     
            
				dojo.forEach(dijit.findWidgets(dojo.byId("auswertung_typ3_table")),function(w){w.destroyRecursive();});
				auswertung_table.innerHTML = "";  
            
				auswertung_table.innerHTML = Auswertung_typ3_table_string();
            
				dojo.parser.parse(auswertung_table);
 		    }break;
			
	case 83:{
				GetScreen("Calculation_Brake");
		    }break;
			
	case 84:{
				GetScreen("Calculation_pm");
		    }break;
			
	case 85:{
				GetScreen("Calculation_Brake_dif");
		    }break;
			
	case 9:{
				GetScreen("Print");
				var info = document.getElementById("textarea1");
				info.value = "Ausdruck konfigurieren\n\nUnterschriftenver-\nwaltung";
			}break;
	}
}