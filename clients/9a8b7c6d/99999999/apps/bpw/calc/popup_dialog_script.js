//Funktionen für das popup menü bei der Bremsanlage (Auswahl eines bestimmten Ventils

//öffnet den Dialog, übergibt die ID des angeklickten Ventilfeldes an die globale Variable "actual_clicked_ventil"
function open_ventil_dialog(id)
{
	$("#popupdiv").load('Bremsanlage_Popup.html').dialog({modal:true, height:500, width:650, resizable:false, title:"Ventilauswahl", position: ["center",100]});
	
	actual_clicked_ventil = id;
}
//checked den producer und enabled/disabled die entsprechenden Select boxes
function popup_dialog_check_producer()
{
	switch($("#popup_hersteller").val())
	{
		case "BPW":
			{
				$("#popup_number_BPW_row").css("visibility", "visible");
				$("#popup_number_Knorr_row").css("visibility", "collapse");
				$("#popup_number_Wabco_row").css("visibility", "collapse");
				$("#popup_number_Haldex_row").css("visibility", "collapse");
			}break;
			
		case "Wabco":
			{
				$("#popup_number_BPW_row").css("visibility", "collapse");
				$("#popup_number_Knorr_row").css("visibility", "collapse");
				$("#popup_number_Wabco_row").css("visibility", "visible");
				$("#popup_number_Haldex_row").css("visibility", "collapse");
			}break;
			
		case "Haldex":
			{
				$("#popup_number_BPW_row").css("visibility", "collapse");
				$("#popup_number_Knorr_row").css("visibility", "collapse");
				$("#popup_number_Wabco_row").css("visibility", "collapse");
				$("#popup_number_Haldex_row").css("visibility", "visible");
			}break;
			
		case "Knorr":
			{
				$("#popup_number_BPW_row").css("visibility", "collapse");
				$("#popup_number_Knorr_row").css("visibility", "visible");
				$("#popup_number_Wabco_row").css("visibility", "collapse");
				$("#popup_number_Haldex_row").css("visibility", "collapse");
			}break;
	}
}
//schließt den dialog und sendet die ausgewählten daten an das passende Ventil'objekt'
//dies aber nur, wenn die daten beide definied sind
//ändert den namen des ventil 'objekts' ggf von 'n' (not definied) zu 'a' (alone)
//ändert styles entsprechend
function popup_dialog_send_data()
{
	$('#popupdiv').dialog('close');
	
	var hersteller = $("#popup_hersteller").val();
	
	var number = $("#popup_number_"+hersteller).val();
	
	if(hersteller && number)
	{	
		if($("#"+actual_clicked_ventil).attr('name') == 'n')
		{
			$("#"+actual_clicked_ventil).attr('name', 'a')
		}
		
		if($("#"+actual_clicked_ventil).attr('name') == 'a' || $("#"+actual_clicked_ventil).attr('name') == 'u')
		{
			$("#"+actual_clicked_ventil).html(number+"<br/>"+hersteller+"<br/>Anhäng.br.V.");
			
			$("#"+(parseInt(actual_clicked_ventil)+10)).css("visibility", "visible");
			
			$("#"+actual_clicked_ventil).css("background-color", "blue");
			$("#"+actual_clicked_ventil).css("color", "white");
		}
		
		if($("#"+actual_clicked_ventil).attr('name') == 'm' || $("#"+actual_clicked_ventil).attr('name') == 'l')
		{
			var height = get_height_of_cell_from_bottom(actual_clicked_ventil.substring(0,1),actual_clicked_ventil.substring(1,2));
			
			$("#"+(parseInt(actual_clicked_ventil)-height)).html(number+"<br/>"+hersteller+"<br/>Anhäng.br.V.");
			
			$("#"+(parseInt(actual_clicked_ventil)+10-height)).css("visibility", "visible");
			
			$("#"+(parseInt(actual_clicked_ventil)-height)).css("background-color", "blue");
			$("#"+(parseInt(actual_clicked_ventil)-height)).css("color", "white");
		}
	}
}