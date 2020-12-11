
function Auswertung_table_string()
{
var HTMLstring ="";

HTMLstring += '<table border="0"><tr>'
			+'<td width="200" align="right">'
			+'<font color="white">1. Achse</font></td>';
for(var i=2;i<=axe_count;i++)
{
			HTMLstring += '<td width="100" align="center">'
			+'<font color="white">'+i+'. Achse</font>'
			+'</td>';
}

HTMLstring +='</tr><tr>'
			+'<td width="200" align="right">'
			+'<font color="white">pZyl[bar] = </font><input type="text" style="width:75px" id="typ_0_pzyl_1" data-dojo-type="dijit.form.TextBox" value="6,4"/>'
			+'</td>';
for(var i=2;i<=axe_count;i++)
{
			HTMLstring += '<td width="100" align="center">'
			+'<input type="text" style="width:75px" id="typ_0_pzyl_'+i+'" data-dojo-type="dijit.form.TextBox" value="6,4"/>'
			+'</td>';
}
HTMLstring +='</tr><tr>'
			+'<td width="200" align="right">'
			+'<font color="white">ThA[N] = </font><input type="text" style="width:75px" id="typ_0_thA_1" data-dojo-type="dijit.form.TextBox" value="4512"/>'
			+'</td>';
for(var i=2;i<=axe_count;i++)
{
			HTMLstring += '<td width="100" align="center">'
			+'<input type="text" style="width:75px" id="typ_0_thA_'+i+'" data-dojo-type="dijit.form.TextBox" value="4512"/>'
			+'</td>';
}
HTMLstring +='</tr><tr><td width="200" align="right">'
			+'<font color="white">C[Nm] = </font><input type="text" style="width:75px" id="typ_0_C_1" data-dojo-type="dijit.form.TextBox" value="677"/>'
			+'</td>';
for(var i=2;i<=axe_count;i++)
{
			HTMLstring += '<td width="100" align="center">'
			+'<input type="text" style="width:75px" id="typ_0_C_'+i+'" data-dojo-type="dijit.form.TextBox" value="677"/>'
			+'</td>';
}
HTMLstring +='</tr><tr>'
			+'<td width="200" align="right">'
			+'<font color="white">T[N] = </font><input type="text" style="width:75px" id="typ_0_T_1" data-dojo-type="dijit.form.TextBox" value="44179"/>'
			+'</td>';
for(var i=2;i<=axe_count;i++)
{
			HTMLstring += '<td width="100" align="center">'
			+'<input type="text" style="width:75px" id="typ_0_T_'+i+'" data-dojo-type="dijit.form.TextBox" value="44179"/>'
			+'</td>';
}
HTMLstring +='</td></tr></table>';
return HTMLstring;
}