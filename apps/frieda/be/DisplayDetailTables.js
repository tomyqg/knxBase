function ClickTicketEntry(_tr){
	$("#ticket-detail").css("display", "inline");

	//function is crap right now. should get the whole dataset with the ticket id from the database and then print it into the spans
	$("#ticket-detail-date").text($(_tr).find('td').eq(0).text());
	$("#ticket-detail-company").text($(_tr).find('td').eq(2).text());
	$("#ticket-detail-description").text($(_tr).find('td').eq(3).text());
}

function ClickCompanyEntry(_tr){
	$("#company-detail").css("display", "inline");

	//function is crap right now. should get the whole dataset with the company id from the database and then print it into the spans
	$("#company-detail-name").text($(_tr).find('td').eq(0).text());
	$("#company-detail-adress").text($(_tr).find('td').eq(1).text());
	$("#company-detail-telephone").text($(_tr).find('td').eq(2).text());
}

function ClickMessageEntry(_tr){
	if($("#"+_tr.getAttribute('id')+"-details").css("display") === "table-row"){
		$("#"+_tr.getAttribute('id')+"-details").css("display", "none");
	}
	else{
		$("#"+_tr.getAttribute('id')+"-details").css("display", "table-row");
		//replace text with message body from database
		$("#"+_tr.getAttribute('id')+"-details").find('td').eq(0).text("Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.");
	}

	if(_tr.className === "unread-message"){_tr.className = "";}
}

function ClickJobEntry(_tr){
	$("#job-detail").css("display", "inline");

	//function is crap right now. should get the whole dataset with the job id from the database and then print it into the spans
	$("#job-detail-date").text($(_tr).find('td').eq(0).text());
	$("#job-detail-company").text($(_tr).find('td').eq(1).text());
	$("#job-detail-title").text($(_tr).find('td').eq(2).text());
	$("#job-detail-priority").text($(_tr).find('td').eq(3).text());
}

function ClickEmployeeEntry(_tr){
	$("#employee-detail").css("display", "inline");
	$('#employee-detail-label').html("ausgewählter Mitarbeiter:");

	var tr_id = $(_tr).attr("id");

	getDataset("Mitarbeiter", tr_id.split("-")[1], DisplayEmployeeEntry);
}

function DisplayEmployeeEntry(xml_Data)
{
	//xml_Data = XML-Objekt!

	$("#employee-detail-lastname").text($(xml_Data).find("Nachname").text());
	$("#employee-detail-firstname").text($(xml_Data).find("Vorname").text());
	$("#employee-detail-emailoffice").text($(xml_Data).find("EmailAdresseOffice").text());
	$("#employee-detail-emailprivate").text($(xml_Data).find("EmailAdressePrivate").text());
}

function ClickGroupEntry(_tr){
	$("#group-detail").css("display", "inline");
	$('#group-detail-label').html("ausgewählte Gruppe:");

	//function is crap right now. should get the whole dataset with the job id from the database and then print it into the spans
	$("#group-detail-name").text($(_tr).find('td').eq(0).text());
}

function ClickReactionTeamEntry(_tr){
	$("#reactionteam-detail").css("display", "inline");
	$('#reactionteam-detail-label').html("ausgewähltes Reaktionsteam:");

	//function is crap right now. should get the whole dataset with the job id from the database and then print it into the spans
	$("#reactionteam-detail-name").text($(_tr).find('td').eq(0).text());
}

function ClickEinsatzEntry(_tr){
	$("#einsatz-detail").css("display", "inline");
	$('#einsatz-detail-label').html("ausgewählter Einsatz:");

	//function is crap right now. should get the whole dataset with the job id from the database and then print it into the spans
	$("#einsatz-detail-name").text($(_tr).find('td').eq(0).text());
	$("#einsatz-detail-drive").text($(_tr).find('td').eq(1).text());
	$("#einsatz-detail-calculatable").text($(_tr).find('td').eq(2).text());
	$("#einsatz-detail-pauschCalculation").text($(_tr).find('td').eq(3).text());
}
