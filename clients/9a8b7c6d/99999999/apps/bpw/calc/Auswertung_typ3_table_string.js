function Auswertung_typ3_table_string()
{
	var HTMLstring ="";

	HTMLstring 	+= '<table border="0"><tr>'
				+'<td width="200" align="right">'
				+'<font color="white">1. Achse</font></td>';
				
	
	//kreiert die Tabelle zum Screen typ3 in abhängigkeit von der achsen-anzahl des Fahrzeugs
	for(var i=2;i<=axe_count;i++)
	{
		HTMLstring 	+= '<td width="100" align="center">'
					+'<font color="white">'+i+'. Achse</font>'
					+'</td>';
	}

	HTMLstring 	+='</tr><tr>'
				+'<td width="200" align="right">'
				+'<font color="white">pZyl[bar]=</font><input type="text" style="width:75px" id="typ_3_pzyl_1" data-dojo-type="dijit.form.TextBox" value="3,758"/>'
				+'</td>';
				
	for(var i=2;i<=axe_count;i++)
	{
		HTMLstring 	+= '<td width="100" align="center">'
					+'<input type="text" style="width:75px" id="typ_3_pzyl_'+i+'" data-dojo-type="dijit.form.TextBox" value="3,758"/>'
					+'</td>';
	}
	
	HTMLstring 	+='</tr><tr>'
				+'<td width="200" align="right">'
				+'<font color="white">% Pe[%]=</font><input type="text" style="width:75px" id="typ_3_Pe_1" data-dojo-type="dijit.form.TextBox" value="23,31"/>'
				+'</td>';
				
	for(var i=2;i<=axe_count;i++)
	{
		HTMLstring 	+= '<td width="100" align="center">'
					+'<input type="text" style="width:75px" id="typ_3_Pe_'+i+'" data-dojo-type="dijit.form.TextBox" value="23,31"/>'
					+'</td>';
	}
	
	HTMLstring 	+='</tr><tr><td width="200" align="right">'
				+'<font color="white">Hub[mm] = </font><input type="text" style="width:50px" id="typ_3_Hub_1" data-dojo-type="dijit.form.TextBox" value="56"/>'
				+'</td>';
				
	for(var i=2;i<=axe_count;i++)
	{
		HTMLstring 	+= '<td width="100" align="center">'
					+'<input type="text" style="width:50px" id="typ_3_Hub_'+i+'" data-dojo-type="dijit.form.TextBox" value="56"/>'
					+'</td>';
	}
	
	HTMLstring +='</td></tr></table>';

	return HTMLstring;
}