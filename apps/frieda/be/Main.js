
$(document).ready(function()
{
	ChangeTabTo(document.getElementById('tab1'));

	//should actually made by PHP:
	//~DONE BY PHP
	/*
	var NumberOfTabs = $("#MAIN").find('tr').eq(0).find('td').length - 1;
	$("#head-filler").attr("width", String(1100 - NumberOfTabs *120));
	$("#frieda-body").attr("colspan", String(NumberOfTabs + 1));
	*/
});

function ChangeTabTo(_td) {

	//i could do that with jquery, just scroll trough all td in second row of main table
	for(var i = 1; i<20; i++)
	{
		var tab = document.getElementById("tab" + String(i));

		if(tab !== null)
		{
			tab.className = "head-icon";
		}
		else
		{
			break;
		}
	}

	_td.className = "head-icon-chosen";


	//TODO: adding right html form to cell with attribute id=frieda-body
	switch(_td.getAttribute('name'))
	{
		case 'new-job':{$("#frieda-body").load('job_erstellen.html?sessionId='+sessionId);} break;
		case 'tickets':{$("#frieda-body").load('tickets.html?sessionId='+sessionId);} break;
		case 'customer-data':{$("#frieda-body").load('kundendaten.html?sessionId='+sessionId);} break;
		case 'statistics':{$("#frieda-body").load('statistik.html?sessionId='+sessionId);} break;
		case 'messages':{$("#frieda-body").load('nachrichten.html?sessionId='+sessionId);} break;
		case 'requests':{$("#frieda-body").load('requests.html?sessionId='+sessionId);} break;
		case 'owntickets':{$("#frieda-body").load('owntickets.html?sessionId='+sessionId);} break;
		case 'jobs':{$("#frieda-body").load('jobs.html?sessionId='+sessionId);} break;
		case 'employees':{$("#frieda-body").load('employee.php?sessionId='+sessionId);} break;
		case 'groups':{$("#frieda-body").load('groups.html?sessionId='+sessionId);} break;
		case 'reaction-teams':{$("#frieda-body").load('reaction-teams.html?sessionId='+sessionId);} break;
		case 'einsatz':{$("#frieda-body").load('einsatzarten.html?sessionId='+sessionId);} break;
		case 'escalation':{$("#frieda-body").load('escalation.html?sessionId='+sessionId);} break;
		case 'new-ticket':{$("#frieda-body").load('createticket.html?sessionId='+sessionId);} break;
		default:{$("#frieda-body").load('job_erstellen.html?sessionId='+sessionId);}
	}
}


function DeleteArticle(id){
	$('#' + id).remove();
}

function AddArticle(id_articleno, id_name, id_serialno, id_amount, id_NewArticleLine, id_NewArticleTable){
	var NewArticleLine = $('#' + id_NewArticleLine);

	var LineCount = $('#' + id_NewArticleTable).find('tr').length;

	$('#' + id_NewArticleTable).append("<tr id='article-"+LineCount+"'><td>"+$('#'+id_articleno).val()+"</td><td>"+$('#'+id_name).val()+"</td><td>"+$('#'+id_serialno).val()+"</td><td>"+$('#'+id_amount).val()+"</td><td><input type='button' value='del' onclick='DeleteArticle(\"article-"+LineCount+"\")'/></td></tr>");

	$('#'+id_articleno).val("");
	$('#'+id_name).val("");
	$('#'+id_serialno).val("");
	$('#'+id_amount).val("");

	$('#' + id_NewArticleLine).remove();

	$('#' + id_NewArticleTable).append(NewArticleLine);
}

function FormatDateTime(Date)
{
	var result = "";
	if(Date.getDate() < 10){result += "0";}
	result += Date.getDate() + ".";
	if((Date.getMonth() + 1) < 10){result += "0";}
	result += (Date.getMonth() + 1) + ".";
	result += Date.getFullYear() + " ";
	if(Date.getHours() < 10){result += "0";}
	result +=  + Date.getHours() + ":";
	if(Date.getMinutes() < 10){result += "0";}
	result += Date.getMinutes();

	return result;
}

function TimeTracking(_button, start_id, end_id, get_time_button_id){
	if($(_button).attr("value") === "Start")
	{
		$('#' + start_id).html(FormatDateTime(new Date()));
		$(_button).attr("value", "Stop");
	}
	else if($(_button).attr("value") === "Stop")
	{
		$('#' + end_id).html(FormatDateTime(new Date()));
		$(_button).attr("value", "Stop");
		$(_button).attr("disabled", "disabled");
		$('#' + get_time_button_id).removeAttr("disabled");
	}
}

function TimeTrackingReset(start_id, end_id, start_button_id, get_time_button_id)
{
	$('#' + start_id).html("");
	$('#' + end_id).html("");
	$('#' + start_button_id).attr("value", "Start");
	$('#' + start_button_id).removeAttr("disabled");
	$('#' + get_time_button_id).attr("disabled", "disabled");
}

function getTrackingTime(start_id, end_id, target_start_id, target_end_id)
{
	$('#' + target_start_id).val($('#' + start_id).html());
	$('#' + target_end_id).val($('#' + end_id).html());
}

//returns xml object of specified dataset
function getDataset(table_name, dataset_id, done_function)
{
	var owner = new Object();
	var DataTable = new wapDataSource(owner, {object: table_name});

	owner.onDataSourceLoaded = function(_scr, _xml){
		done_function(_xml);
	};

	DataTable.load("", dataset_id, "");

	//var php_function = "GetOneDataset.php";

	//$.post(php_function, {name: table_name, id: dataset_id}, null, "text").done(function(data){done_function(data);});
}
