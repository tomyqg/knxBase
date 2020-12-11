//Funktionen für den Screen "Fahrzeugtyp"

//weißt dem Bild Tag ein neues Bild entsprechend des Ausgewählten anhängers zu
function ChangeSattelTrailerImage()
{
	var selectedItem = dijit.byId("anhaenger_select_sattel").get("value");
	var image = document.getElementById("trailer_image");
	var new_image = new Image();
	
	switch(selectedItem)
	{
		case "1-Achs-Sattelanhänger": new_image.src = "imgs_trailer/1_Achssattel_Rahmen.jpg";break;
		case "2-Achs-Sattelanhänger": new_image.src = "imgs_trailer/2_Achssattel_Rahmen.jpg";break;
		case "3-Achs-Sattelanhänger": new_image.src = "imgs_trailer/3_Achssattel_Rahmen.jpg";break;
		case "4-Achs-Sattelanhänger": new_image.src = "imgs_trailer/4_Achssattel_Rahmen.jpg";break;
		case "5-Achs-Sattelanhänger": new_image.src = "imgs_trailer/5_Achssattel_Rahmen.jpg";break;
		case "6-Achs-Sattelanhänger": new_image.src = "imgs_trailer/6_Achssattel_Rahmen.jpg";break;
		case "7-Achs-Sattelanhänger": new_image.src = "imgs_trailer/Sattel_7_Achs.png";break;
		case "8-Achs-Sattelanhänger": new_image.src = "imgs_trailer/Sattel_8_Achs.png";break;
	}
	image.src = new_image.src;
}
//weißt dem Bild Tag ein neues Bild entsprechend des Ausgewählten anhängers zu
function ChangeDeichselTrailerImage()
{
	var selectedItem = dijit.byId("anhaenger_select_deichsel").get("value");
	var image = document.getElementById("trailer_image");
	var new_image = new Image();
	
	switch(selectedItem)
	{
		case "2-Achs-Deichselanhänger(1-1)": new_image.src = "imgs_trailer/2_Achanh(1_1)_Rahmen.jpg";break;
		case "3-Achs-Deichselanhänger(1-2)": new_image.src = "imgs_trailer/3_Achanh(1_2)_ISO_Rahmen.jpg";break;
		case "3-Achs-Deichselanhänger(2-1)": new_image.src = "imgs_trailer/3_Achanh(2_1)_Rahmen.jpg";break;
		case "4-Achs-Deichselanhänger(2-2)": new_image.src = "imgs_trailer/4_Achanh(2_2)_Rahmen.jpg";break;
		case "4-Achs-Deichselanhänger(1-3)": new_image.src = "imgs_trailer/4_Achanh(1_3)_Rahmen.jpg";break;
		case "5-Achs-Deichselanhänger(2-3)": new_image.src = "imgs_trailer/5_Achanh(2_3)_Rahmen.jpg";break;
	}
	image.src = new_image.src;
}
//weißt dem Bild Tag ein neues Bild entsprechend des Ausgewählten anhängers zu
function ChangeZentralTrailerImage()
{
	var selectedItem = dijit.byId("anhaenger_select_zentral").get("value");
	var image = document.getElementById("trailer_image");
	var new_image = new Image();
	
	switch(selectedItem)
	{
		case "1-Zentralachs-anhänger": new_image.src = "imgs_trailer/1_Achzentral_Rahmen.jpg";break;
		case "2-Zentralachs-anhänger": new_image.src = "imgs_trailer/2_Achzentral_Rahmen.jpg";break;
		case "3-Zentralachs-anhänger": new_image.src = "imgs_trailer/3_Achzentral_Rahmen.jpg";break;
	}
	image.src = new_image.src;
}
//disabled/enabled die zum aktuellen radio button passenden select boxen
function DeichselClicked()
{
	dijit.byId("anhaenger_select_deichsel").setAttribute('disabled', false);
	dijit.byId("anhaenger_select_sattel").setAttribute('disabled', true);
	dijit.byId("anhaenger_select_zentral").setAttribute('disabled', true);
	ChangeDeichselTrailerImage();
}
//disabled/enabled die zum aktuellen radio button passenden select boxen
function SattelClicked()
{
	dijit.byId("anhaenger_select_deichsel").setAttribute('disabled', true);
	dijit.byId("anhaenger_select_sattel").setAttribute('disabled', false);
	dijit.byId("anhaenger_select_zentral").setAttribute('disabled', true);
	ChangeSattelTrailerImage();
}
//disabled/enabled die zum aktuellen radio button passenden select boxen
function ZentralClicked()
{
	dijit.byId("anhaenger_select_deichsel").setAttribute('disabled', true);
	dijit.byId("anhaenger_select_sattel").setAttribute('disabled', true);
	dijit.byId("anhaenger_select_zentral").setAttribute('disabled', false);
	ChangeZentralTrailerImage();
}
//sucht die aktuelle achs-anzahl und übergibt diese an die globale variable 'axe_count'
function CheckTrailerType()
{
    if(document.getElementById("anhaenger_radio1").checked)
	{
		fahrzeugtyp = dijit.byId("anhaenger_select_deichsel").get("value");
		axe_count = dijit.byId("anhaenger_select_deichsel").get("value").substring(0,1);
	}
	
	if(document.getElementById("anhaenger_radio2").checked)
	{
		fahrzeugtyp = dijit.byId("anhaenger_select_sattel").get("value");
		axe_count = dijit.byId("anhaenger_select_sattel").get("value").substring(0,1);
	}
	
	if(document.getElementById("anhaenger_radio3").checked)
	{
		fahrzeugtyp = dijit.byId("anhaenger_select_zentral").get("value");
		axe_count = dijit.byId("anhaenger_select_zentral").get("value").substring(0,1);
	}
}