<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="SysTexteC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="SysTexteC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="SysTexteKeyData" id="SysTexteKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "System Text Id") ; ?>:&nbsp;</th>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IId" id="_IId" value="" onkeypress="return enterKey( event) ;"/>
							</td>
							<td class="space"><input type="image" src="/Rsrc/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button type="button" data-dojo-type="dijit/form/Button" name="selSysTexteNr"
									onclick="screenCurrent.selSysTexte.show( '', -1, '') ;">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="SysTexteC3s1c2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90">
		<div id="SysTexteC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer">
			<div id="SysTexteCPListe" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Overview") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formSysTexteSurveyTop" id="formSysTexteSurveyTop" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Name"), "_SName", 10, 10, "", "") ;
								rowEdit( FTr::tr( "Reference no."), "_SRefNr", 24, 32, "", "") ;
								rowOption( FTr::tr( "Language"), "_SLang", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Language'"), "de_DE", "") ;
								?></table>
							<button data-dojo-type="dijit/form/Button"
								onClick="requestDataMinerNew( 'Base', 'DataMinerMisc', '/Common/hdlObject.php', 'getTableSysTexte', 'f50', 'divSysTexteList', 'Id', 'screenSysTexte', 'retToSysTexte', document.forms['SysTexteKeyData']._IId.value, cbSysTexteDMRes, 'formSysTexteSurveyTop') ; return false ; ">
								<?php echo FTr::tr( "Refresh") ; ?>
							</button>
							<?php tableBlockNF( "refSysTexteSurvey", "formSysTexteSurveyTop") ;		?>
						</form> 
					</div>
					<div id="depdata">
						<div id="divSysTexteList"></div>
					</div>
					<div id="maindata">
						<?php tableBlock( "refSysTexteSurvey", "formSysTexteSurveyBot") ;		?>
					</div>
				</div>
			</div>
			<div id="SysTexteCPMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formSysTexteMain" id="formSysTexteMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Text short code"), "_IName", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Reference no."), "_IRefNr", 24, 32, "", "") ;
								rowOption( FTr::tr( "Language"), "_ISprache", Opt::getRLangCodes(), "de_de", "") ;
								rowHTMLEdit( FTr::tr( "Long text 1"), "_RVolltextSysTexte", 24, 32, "", "") ;
								rowHTMLEdit( FTr::tr( "Long text 2"), "_RVolltext2SysTexte", 24, 32, "", "") ;
								?></table> 
							<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModSys', 'SysTexte', '/Common/hdlObject.php', 'add', '', '', '', 'formSysTexteMain', showSysTexte) ; return false ; ">
								 <?php echo FTr::tr( "Create") ; ?>
							</button>
							<button type="button" data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModSys', 'SysTexte', '/Common/hdlObject.php', 'upd', '', document.forms['SysTexteKeyData']._IId.value, '', 'formSysTexteMain', showSysTexte) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="SysTexteHelp" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Help") ; ?>">
				<div id="SysTexteHelpTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="SysTexteHelpDE" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "German") ; ?>">
<h3>SysTexte beinhaltet alle</h3>
<ul>
<li>EMail Texte, z.B. Anschreiben, Disclaimer etc., </li>
<li>Prefix uns Postfix Texte f&uuml;r Bestellungen, Angebote etc. sowie</li>
<li>Texte f&uuml;r R- und S-S&auml;tze bzw. die Texte entsprechend der neuen Gefahrgut-Terminologie</li>
</ul>
Diese Texte k&ouml;nnen in allen Sprachen und Sprachvarianten erfasst werden. Die Sprache wird in aller Regel entsprechend der Sprache der Empf&auml;ngers eines Dokumentes ausgew&auml;hlt.<br/>
<br/>
Texte f&uuml;r Produkt-, Artikel- sowie Katalog-Gruppen, finden sich unter den System Parametern 'Texte'.<br/>
F&uum;r weitere Informationen bite dort nachsehen.<br/>
<br/>
Ein Systemtext ist eindeutig identifiziert &uuml;ber:
<ul>
<li>den Textnamen, z.B. KdBestPrefix,</li>
<li>die Referenznummer sowie</li>
<li>die Sprache, z.B. de f&uuml;r deutsch oder en_us f&uuml;r amerikanisches Englisch</li>
</ul>

					</div>
					<div id="SysTexteHelpEN" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "English") ; ?>">
					</div>
					<div id="SysTexteHelpFR" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "French") ; ?>">
					</div>
					<div id="SysTexteHelpES" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Spanish") ; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
