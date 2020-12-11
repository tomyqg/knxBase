<?php
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="TexteBC" data-dojo-type="dijit/layout/BorderContainer" style="width: 100%; height: 100%;">
	<div id="TexteCPKey" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'top', splitter:true" style="height: 50px;">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="TexteKeyData" id="TexteKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Text id") ; ?>:&nbsp;</th>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IId" id="_IId" value=""
									onkeypress="return enterKey( event) ;"/>
							</td>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button name="selTexteNr" data-dojo-type="dijit/form/Button"
									onclick="screenCurrent.selTexte.show( '', -1, '') ;">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="TexteCPMain" data-dojo-type="dijit/layout/ContentPane" data-dojo-props="region:'center'">
		<div id="TexteC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="TexteCPListe" data-dojo-type="dijit/layout/ContentPane" style="" title="<?php echo FTr::tr( "Overview") ; ?>">
				<div id="content">
					<div id="depdata">
						<div id="TexteOvRoot">
							<?php tableBlock( "itemViews['dmTexteOv']", "formTexteOvTop") ;		?>
							<table id="TableTexteOv" eissClass="Texte" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id" eissLinkTo="Texte" colspan="2">Id</th>
										<th eissAttribute="Name"><?php echo FTr::tr( "Name") ; ?></th>
										<th eissAttribute="Sprache"><?php echo FTr::tr( "Language") ; ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div id="TexteCPMainCP" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formTexteMain" id="formTexteMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Text name"), "_IName", 24, 64, "", "") ;
								rowEdit( FTr::tr( "Reference no."), "_IRefNr", 24, 32, "", "") ;
								rowOption( FTr::tr( "Language"), "_ISprache", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Language'"), "de_DE", "") ;
								rowHTMLEdit2( FTr::tr( "Long text 1"), "_RVolltextTexte", 24, 32, "", "") ;
								rowHTMLEdit2( FTr::tr( "Long text 2"), "_RVolltext2Texte", 24, 32, "", "") ;
								?></table> 
							<button type="button" data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'add', 'formTexteMain') ;">
								<?php echo FTr::tr( "Create") ;?>
							</button>
							<button type="button" data-dojo-type="dijit/form/Button"
								onclick="screenCurrent.sDispatch( true, 'upd', 'formTexteMain') ;">
								<?php echo FTr::tr( "Update") ;?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="TexteCPHelpCP" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Help") ; ?>">
				<div id="TexteHelpTC" data-dojo-type="dijit/layout/TabContainer">
					<div id="TexteHelpDE" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "German") ; ?>">
<h3>Anwendungs Texte</h3>
Anwendungs-Texte beinhaltet alle<ul>
<li>Produktgruppen-, Artikelgruppen- und Kataloggruppen Texte</li>
</ul>
für die Internetpräsenz.<br/>
Diese Texte k&ouml;nnen in allen Sprachen und Sprachvarianten erfasst werden. Die Sprache wird in aller Regel entsprechend der Sprache der Besuchers der Internet Präsenz ausgew&auml;hlt.<br/>
<br/>
Texte f&uuml;r EMail Inhalte, Prefix und Postfixe, finden sich unter den System Parametern 'System Texte'.<br/>
F&uum;r weitere Informationen bite dort nachsehen.
<br/>
Ein Text ist eindeutig identifiziert &uuml;ber:
<ul>
<li>den Textnamen, z.B. ArtGrName,</li>
<li>die Referenznummer, "Becherg&auml;ser" sowie</li>
<li>die Sprache, z.B. de f&uuml;r deutsch oder en_us f&uuml;r amerikanisches Englisch</li>
</ul>
					</div>
					<div id="TexteHelpEN" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "English") ; ?>">
					</div>
					<div id="TexteHelpFR" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "French") ; ?>">
					</div>
					<div id="TexteHelpES" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Spanish") ; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
