<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
if ( isset( $_GET["edtName"]))
	$edtName	=	$_GET["edtName"] ;
?>
<html>
<head></head>
<body>
<div id="content">
	<div id="maindata">
		<form name="editorObject" id="editorObject" onsubmit="return false ;"> 
			<table>
				<tr>
					<td></td>
					<td>
						<input type="checkbox" checked="true" name="editorKeep">Keep dialog open</input>
					</td>
				</tr>	
				<?php
				rowDisplay( FTr::tr( "Id"), "_HId", 8, 8, "", "") ;
				rowEdit( FTr::tr( "Line no."), "_ILineNo", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Item no."), "_IItemNo", 6, 8, "", "") ;
				rowDisplay( FTr::tr( "Total %"), "_IAmountTotal", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Amount"), "_FAmount", 6, 8,
								"", "",
								"onkeypress=\"if ( event.keyCode == 13) {itemEditors['edtJournalLineItem'].enterAmount() ; return false ;} return true ;\"") ;
				rowOption( FTr::tr( "Template"), "_IJournalTmplNo",
								Opt::getArray( "JournalTmpl", "JournalTmplNo", "Description", "1 = 1"),
								"0", "",
								"itemEditors['edtJournalLineItem'].loadTmpl() ;") ;
				rowEdit( FTr::tr( "Description"), "_IDescription", 32, 32, "", "") ;
				rowDisplay( FTr::tr( "Journal template no."), "_HKey", 6, 6, "", "") ;
				?></table>
			<div id="TableJournalLineRoot">
				<table id="TableJournalLines" eissClass="JournalLine" width="100%">
					<thead>
						<tr eissType="header">
<!-- 
								<th eissAttribute="ItemNo"><?php echo FTr::tr( "Item no.") ; ?></th>
 								<th eissAttribute="Description"><?php echo FTr::tr( "Description") ; ?></th>
 -->
							<th eissAttribute="AccountDebit" eissFunctions="edit"><?php echo FTr::tr( "Debit") ; ?></th>
							<th eissAttribute="CAD" eissFunctions="edit" eissVT="float"></th>
							<th eissAttribute="AccountCredit" eissFunctions="edit"><?php echo FTr::tr( "Credit") ; ?></th>
							<th eissAttribute="CAC" eissFunctions="edit" eissVT="float"></th>
							<th eissAttribute="AmountDebit" eissFunctions="edit" eissVT="float"><?php echo FTr::tr( "Amount debit") ; ?></th>
							<th eissAttribute="AmountCredit" eissFunctions="edit" eissVT="float"><?php echo FTr::tr( "Amount credit") ; ?></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionCreateObject" onclick="itemEditors['edtJournalLineItem'].add() ;" />
				<?php echo FTr::tr( "Create") ; ?>
			</button>
		</form> 
	</div>
</div>
</body>
</html>