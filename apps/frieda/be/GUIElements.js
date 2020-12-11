function ClickEditableTextEntry(_span){
	if($(_span).attr("iseditable") == 'false')
	{
		$(_span).attr("iseditable", "true");
		var content = $(_span).html();
		$(_span).html("<input type='text' value='"+String(content)+"' onblur='ExitEditableTextEntry(this)'/>");
		$(_span).find('input').eq(0).focus();
	}
}

function ExitEditableTextEntry(_input){
	var content = $(_input).val();
	
	if(String(content) === ""){content = "---";}
	
	$(_input).parent().attr("iseditable", "false");
	$(_input).parent().html(String(content));
	
	//TODO: Send new data back to database
	//get kind of data with span id
}
/*
function ClickSingleListEntry(_span, id_collection_source){
	if($(_span).attr("iseditable") == 'false')
	{
		$(_span).attr("iseditable", "true");
		var content = $(_span).html();
		$(_span).html("<input list='"+String(id_collection_source)+"' onblur='ExitSingleListEntry(this)'/>");
		$(_span).find('input').eq(0).focus();
	}
}

function ExitSingleListEntry(_input){
	var content = $(_input).val();
	$(_input).parent().attr("iseditable", "false");
	$(_input).parent().html(String(content));
	
	//TODO: Send new data back to database
	//get kind of data with span id
}
*/
function ClickEditableTextAreaEntry(_span){
	if($(_span).attr("iseditable") == 'false')
	{
		$(_span).attr("iseditable", "true");
		var content = $(_span).html();
		
		content = content.replace(/<br\/>/gmi, "\n");
		content = content.replace(/<br>/gmi, "\n");
		
		$(_span).html("<textarea type='text' onblur='ExitEditableTextAreaEntry(this)'>"+String(content)+"</textarea>");
		$(_span).find('textarea').eq(0).focus();
	}
}

function ExitEditableTextAreaEntry(_textarea){
	var content = $(_textarea).val();
	
	content = content.replace(/\n/gmi, "<br/>");
	
	$(_textarea).parent().attr("iseditable", "false");
	$(_textarea).parent().html(String(content));
	
	//TODO: Send new data back to database
	//get kind of data with span id
}
//Add: for each element in collection: add delete button after label
function ClickEditableCollectionEntry(_span, id_collection_source){
	if($(_span).attr("iseditable") == 'false')
	{
		$(_span).attr("iseditable", "true");
		var content = $(_span).html();
		
		//var LineArray = content.split("<br/>");
		var LineArray = content.split("<br>");
		
		var index;
		var NewContent = "";
		for(index = 0;index < LineArray.length; ++index)
		{
			LineArray[index] = String(LineArray[index]).replace(/&nbsp;/g,"");/*remove whitespaces at the end*/
			if(String(LineArray[index]) !== "---")
			{
				NewContent += "<div>" + String(LineArray[index]) + "&nbsp;&nbsp;<img src='icons/Delete-256.png' class='little-icon-button' title='entfernen' onmousedown='DeleteCollectionEntry(this)'/></div>";
			}
		}
		
		var SelectTag = NewContent + "<select onblur='ExitEditableCollectionEntry(this)'>";
		
		$('#' + String(id_collection_source)).find('option').each(function(index){
			SelectTag += "<option value='"+$(this).attr('value')+"'>"+$(this).attr('value')+"</option>";
		});
		
		SelectTag += "</select>";
		
		$(_span).html(String(SelectTag));
		$(_span).find('select').eq(0).focus();
	}
}

function DeleteCollectionEntry(_img)
{
	$(_img).parent().remove();
}

function ExitEditableCollectionEntry(_select){
	var content = $(_select).val();
	
	$(_select).parent().attr("iseditable", "false");

	var OldContent = "";
	
	$(_select).parent().find('div').each(function(index){
		$(this).find('img').eq(0).remove();
		
		if(String(OldContent) !== ""){OldContent += "<br/>"}
		
		OldContent +=  String($(this).html());
	});
	
	
	OldContent = String(OldContent).replace(/&nbsp;/g,"");
	
	if(String(content) === "")
	{
		if(String(OldContent) === "")
		{
			$(_select).parent().html("---");
		}
		else
		{
			$(_select).parent().html(String(OldContent));
		}
	}
	else
	{
		if(String(OldContent) === "")
		{
			$(_select).parent().html(String(content));
		}
		else
		{
			$(_select).parent().html(String(OldContent)+"<br/>"+String(content));
		}
	}
	
	//TODO: Send new data back to database
	//get kind of data with span id
}


function ClickSingleSelectEntry(_span, id_collection_source){
	if($(_span).attr("iseditable") == 'false')
	{
		$(_span).attr("iseditable", "true");
		var content = $(_span).html();
		
		//content = content.replace(/<br\/>/gmi, "\n");
		//content = content.replace(/<br>/gmi, "\n");
		
		var SelectTag = "<select onblur='ExitSingleSelectEntry(this)'>";
		
		$('#' + String(id_collection_source)).find('option').each(function(index){
			if($(this).attr('value') === String(content))
			{
				SelectTag += "<option value='"+$(this).attr('value')+"' selected='selected'>"+$(this).attr('value')+"</option>";
			}
			else
			{
				SelectTag += "<option value='"+$(this).attr('value')+"'>"+$(this).attr('value')+"</option>";
			}
		});
		
		SelectTag += "</select>";
		
		//$(_span).html("<input type='text' value='"+String(content)+"' onblur='ExitEditableCollectionEntry(this)'/>");
		$(_span).html(String(SelectTag));
		$(_span).find('select').eq(0).focus();
	}
}

function ExitSingleSelectEntry(_select){
	var content = $(_select).val();
	
	//content = content.replace(/\n/gmi, "<br/>");
	
	$(_select).parent().attr("iseditable", "false");

	$(_select).parent().html(String(content));
	
	//TODO: Send new data back to database
	//get kind of data with span id
}

function ClickTimeRangeEntry(_td){
	if($(_td).attr("iseditable") == 'false')
	{
		$(_td).attr("iseditable", "true");
		var content = $(_td).html();
		var ContentArray = String(content).split(" ");
		
		var SelectElement = "<select id='temp-input-select' onblur='ExitTimeRangeEntry(this)'>";
		var index = 0;
		var Times = new Array("Stunden", "Tage", "Wochen", "Monate", "Jahre");
		
		for(index = 0;index < 5;index++)
		{
			SelectElement += "<option value='" + Times[index] + "' ";
			
			if(String(Times[index]) === String(ContentArray[1])){SelectElement += "selected='selected' ";}
			
			SelectElement += ">"+Times[index] + "</option>";
		}
		
		SelectElement += "</select>";
		
		$(_td).html("<input type='number' id='temp-input-number' min='1' max='24' onblur='ExitTimeRangeEntry(this)' style='width:3em;' value='"+String(ContentArray[0])+"' />"+SelectElement);
		$('#temp-input-select').focus();
	}
}

function ExitTimeRangeEntry(_input){
	if($('#temp-input-number:focus').size() == 0 && $('#temp-input-select:focus').size() == 0)//if none of these two elements has focus
	{
		$(_input).parent().attr("iseditable", "false");
	
		$(_input).parent().html(String($('#temp-input-number').val()) + " " + $('#temp-input-select').val());
	}
}