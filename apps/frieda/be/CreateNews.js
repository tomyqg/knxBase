function CreateNewEmployee(){
	$("#employee-detail").css("display", "inline");
	
	//TODO: first: create dummy dataset in database
	//TODO: remember id and apply this employee id to table
	$('#employee-detail-firstname').html("---");
	$('#employee-detail-lastname').html("---");
	$('#employee-detail-emailoffice').html("---");
	$('#employee-detail-emailprivate').html("---");
	$('#employee-detail-role').html("---");
	$('#employee-detail-supervisor').html("---");
	$('#employee-detail-reactionteams').html("---");
	
	$('#employee-detail-label').html("neuer Mitarbeiter:");
}

function CreateNewGroup(){
	$("#group-detail").css("display", "inline");

	$('#group-detail-label').html("neue Gruppe:");
	
	$('#group-detail-name').html("---");
	$('#group-detail-members').html("---");
}

function CreateNewReactionTream(){
	$("#reactionteam-detail").css("display", "inline");

	$('#reactionteam-detail-label').html("neues Reaktionsteam:");
	
	$('#reactionteam-detail-name').html("---");
	$('#reactionteam-detail-members').html("---");
	$('#reactionteam-detail-groups').html("---");
}

function CreateNewEinsatz(){
	$("#einsatz-detail").css("display", "inline");

	$('#einsatz-detail-label').html("neue Einsatzart:");
	
	$('#einsatz-detail-name').html("---");
	$('#einsatz-detail-drive').html("---");
	$('#einsatz-detail-calculatable').html("---");
	$('#einsatz-detail-pauschCalculation').html("---");
}