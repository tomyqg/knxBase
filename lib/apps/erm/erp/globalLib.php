<?php

/**
 * globalLib.php - Global Library for Presentation Layer
 * 
 * This library contains global functions to simplify the coding of the presentation layer
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package PL-Libraries
 */

require_once( "Fields.php" );

/**
 * Erzeugt eine Tabellezeile, einschl. aller Zeilen- und Spalten-Tags fuer
 * eine Zwei-Spaltige Option.
 * 
 * @param $_label		Text des Labels fuer die Option
 * @param $_name		Inhalt des name="..." Tags fuer HTML
 * @param $_option		Feld mit dem Optionen ( ... => ... Form )
 * @param $_value		Initialer Wert der Option
 * @param $_note		Kommentar, Notiz (erscheint in der 3. Spalte)
 * @return unknown_type
 */
function	rowOption( $_label, $_name, $_option, $_value, $_note="", $_js="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellOption( $_name, $_option, $_value, $_js) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}
function	rowFlag( $_label, $_name, $_flag, $_value, $_note="", $_js="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellFlag( $_name, $_flag, $_value, $_js) ;
	echo "</tr>\n" ;
}
function	rowCB( $_label, $_name, $_flag, $_value, $_note="", $_js="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellCB( $_name, $_flag, $_value, $_js) ;
	echo "</tr>\n" ;
}

/**
 * Erzeugt eine Tabellezeile, einschl. aller Zeilen- und Spalten-Tags fuer
 * eine Ein-Spaltige Option.
 * 
 * @param $_label		Text des Labels fuer die Option
 * @param $_name		Inhalt des name="..." Tags fuer HTML
 * @param $_option		Feld mit dem Optionen ( LEER => ... Form )
 * @param $_value		Initialer Wert der Option
 * @param $_note		Kommentar, Notiz (erscheint in der 3. Spalte)
 * @return unknown_type	
 */
function	_rowOptionS( $_label, $_name, $_option, $_value, $_note="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellOptionS( $_name, $_option, $_value) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 *
 */
function	rowDispOption( $_label, $_name, $_option, $_value, $_note="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellDispOption( $_name, $_option, $_value) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 *
 */
function	rowHTMLEdit( $_label, $_name, $_cols, $_rows, $_value, $_note="", $_jsin="", $_jsout="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellHTMLEdit( $_name, $_cols, $_rows, $_value) ;
	cellHover( $_jsin, $_jsout) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 *
 */
function	rowHTMLEdit2( $_label, $_name, $_cols, $_rows, $_value, $_note="", $_jsin="", $_jsout="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellHTMLEdit2( $_name, $_cols, $_rows, $_value) ;
	cellHover( $_jsin, $_jsout) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 *
 */
function	rowTextEdit( $_label, $_name, $_cols, $_rows, $_value, $_note="", $_js="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellTextEdit( $_name, $_cols, $_rows, $_value, $_js) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 *
 */
function	rowRTFEdit( $_label, $_name, $_cols, $_rows, $_value, $_note="", $_js="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellEdit( $_name, $_cols, $_rows, $_value, $_js) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 * Erzeugt eine Tabellezeile, einschl. aller Zeilggen- und Spalten-Tags fuer
 * ein einfaches Eingabefeld.
 * 
 * @param $_label		Text des Labels fuer die Option
 * @param $_name		Inhalt des name="..." Tags fuer HTML
 * @param $_size		Dargestellte Laenge des Eingabefeldes
 * @param $_max			Maximale Laenge des Eingabefeldes
 * @param $_value		Initialer Wert der Option
 * @param $_note		Kommentar, Notiz (erscheint in der 3. Spalte)
 * @param $_js			opt.: javascript code zur Ausfuehrung (assoziiert mit dem Eingabefeld)
 * @param $_jsCall		opt.: javascript code zur Ausfuehrung mit einem opt., zusaetzlichen Lookup Knopf
 * @return unknown_type
 */
function	rowEdit( $_label, $_name, $_size, $_max, $_value, $_note="", $_js="", $_jsCall="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label, $_note) ;
	cellEdit( $_name, $_size, $_max, $_value, $_js) ;
	if ( strlen( $_jsCall) > 0) {
		cellBtnLookup( $_jsCall) ;
	}
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

function	rowSpin( $_label, $_name, $_size, $_max, $_value, $_note="", $_js="", $_jsCall="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellSpin( $_name, $_size, $_max, $_value, $_js) ;
	if ( strlen( $_jsCall) > 0) {
		cellBtnLookup( $_jsCall) ;
	}
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

function	rowEditFloat( $_label, $_name, $_size, $_max, $_value, $_note="", $_js="", $_jsCall="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellEditFloat( $_name, $_size, $_max, $_value, $_js) ;
	if ( strlen( $_jsCall) > 0) {
		cellBtnLookup( $_jsCall) ;
	}
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 * Erzeugt eine Tabellezeile, einschl. aller Zeilggen- und Spalten-Tags fuer
 * ein einfaches Eingabefeld.
 * 
 * @param $_label		Text des Labels fuer die Option
 * @param $_name		Inhalt des name="..." Tags fuer HTML
 * @param $_size		Dargestellte Laenge des Eingabefeldes
 * @param $_max			Maximale Laenge des Eingabefeldes
 * @param $_value		Initialer Wert der Option
 * @param $_note		Kommentar, Notiz (erscheint in der 3. Spalte)
 * @param $_js			opt.: javascript code zur Ausfuehrung (assoziiert mit dem Eingabefeld)
 * @param $_jsCall		opt.: javascript code zur Ausfuehrung mit einem opt., zusaetzlichen Lookup Knopf
 * @return unknown_type
 */
function	rowDate( $_label, $_name, $_size, $_max, $_value, $_note="", $_js="", $_jsCall="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellDate( $_name, $_size, $_max, $_value, $_js) ;
	if ( strlen( $_jsCall) > 0) {
		cellBtnLookup( $_jsCall) ;
	}
	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 * Erzeugt eine Tabellezeile, einschl. aller Zeilen- und Spalten-Tags fuer
 * ein doppeltes (zwei INPUT Element hintereinander) Eingabefeld.
 * 
 * @param $_label		Text des Labels fuer die Option
 * @param $_name		Inhalt des name="..." Tags fuer HTML
 * @param $_size		Dargestellte Laenge des Eingabefeldes
 * @param $_max			Maximale Laenge des Eingabefeldes
 * @param $_value		Initialer Wert der Option
 * @param $_name2		Inhalt des name="..." Tags fuer HTML
 * @param $_size2		Dargestellte Laenge des Eingabefeldes
 * @param $_max2		Maximale Laenge des Eingabefeldes
 * @param $_value2		Initialer Wert der Option
 * @param $_note		Kommentar, Notiz (erscheint in der 3. Spalte)
 * @param $_js			opt.: javascript code zur Ausfuehrung (assoziiert mit dem Eingabefeld)
 * @param $_jsCall		opt.: javascript code zur Ausfuehrung mit einem opt., zusaetzlichen Lookup Knopf
 * @return unknown_type
 */
function	rowEditDbl( $_label, $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2, $_note="", $_js="", $_jsCall="", $_js2="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellEditDbl( $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2, $_js, $_js2) ;
	if ( strlen( $_jsCall) > 0) {
		cellBtnLookup( $_jsCall) ;
	}
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 * Erzeugt eine Tabellenzeile, einschl. aller Zeilen- und Spalten-Tags fuer
 * ein doppeltes (zwei INPUT Element ueber BR getrennt) Eingabefeld.
 * 
 * @param $_label		Text des Labels fuer die Option
 * @param $_name		Inhalt des name="..." Tags fuer HTML
 * @param $_size		Dargestellte Laenge des Eingabefeldes
 * @param $_max			Maximale Laenge des Eingabefeldes
 * @param $_value		Initialer Wert der Option
 * @param $_name2		Inhalt des name="..." Tags fuer HTML
 * @param $_size2		Dargestellte Laenge des Eingabefeldes
 * @param $_max2		Maximale Laenge des Eingabefeldes
 * @param $_value2		Initialer Wert der Option
 * @param $_note		Kommentar, Notiz (erscheint in der 3. Spalte)
 * @param $_js			opt.: javascript code zur Ausfuehrung (assoziiert mit dem Eingabefeld)
 * @param $_jsCall		opt.: javascript code zur Ausfuehrung mit einem opt., zusaetzlichen Lookup Knopf
 * @return unknown_type
 */
function	rowEditDblBR( $_label, $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2, $_note="", $_js="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellEditDblBR( $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2, $_js) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 *
 */
function	rowDisplay( $_label, $_name, $_size, $_max, $_value, $_note="", $_jsCall="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellDisplay( $_name, $_size, $_max, $_value) ;
	if ( strlen( $_jsCall) > 0) {
		cellBtnLookup( $_jsCall) ;
	}
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 *
 */
function	rowDisplayDbl( $_label, $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2, $_note="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellDisplayDbl( $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 *
 */
function	rowDisplayDblBR( $_label, $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2, $_note="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellDisplayDblBR( $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}

/**
 *
 */
function	rowFile( $_label, $_name, $_size, $_max, $_value, $_note="") {
	echo "<tr>\n" ;
	cellHelp( $_note) ;
	cellLabel( $_label) ;
	cellFile( $_name, $_size, $_max, $_value) ;
//	cellNote( $_note) ;
	echo "</tr>\n" ;
}
/**
 * title=\"$_help\" 
 */
function	cellHelp( $_help) {
	echo "<th class=\"flHelp\">" ;
	echo "<image src=\"/images/img/b_help.png\" title=\"$_help\">" ;
	echo "</th>" ;
}

function	cellLabel( $_label, $_note="") {
	echo "<th width=\"150\" class=\"flMainData\">" . $_label . ":</th>\n" ;
}
/**
 *
			"<textarea id=\"" . $_name . "\" ".
			"name=\"" . $_name . "\" ".
			">" . $_value . "</textarea>".
 */
function	cellHTMLEdit( $_name, $_cols, $_rows, $_value) {
	$myJs	=	"onkeypress=\"mark( ".$_name.") ;\"" ;
	echo "<td class=\"fEdit\" ".
			"width=\"500\">".
			"<textarea data-dojo-type=\"dijit.Editor\" ".
				"id=\"".$_name."\" ".
				"name=\"".$_name."\" ".
//				"style=\"rows:10;\" ".
				"height=\"250px;\" ".
//				"onChange=\"console.log('editor1 onChange handler: ' + arguments[0])\" ".
				"plugins=\"['cut','copy','paste','|','bold','italic','underline','strikethrough','subscript','superscript','|', 'insertOrderedList', 'insertUnorderedList', '|', 'indent', 'outdent', 'justifyLeft', 'justifyCenter', 'justifyRight'" .
//				"," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'insertTable'}," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'modifyTable'}," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'InsertTableRowBefore'}," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'InsertTableRowAfter'}," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'insertTableColumnBefore'}," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'insertTableColumnAfter'}," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'deleteTableRow'}," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'deleteTableColumn'}," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'colorTableCell'}," .
//				"{name: 'dojox.editor.plugins.TablePlugins', command: 'tableContextMenu'}" .
				"]\" ".
				"extraPlugins=\"['foreColor','hiliteColor', 'fontName', 'fontSize', 'fullscreen', 'viewsource', 'createLink', 'unlink', 'insertImage'" .
				", " .
				"]\" ".
				">".
				"</textarea>".
			"</td>\n" ;
}

function	cellHTMLEdit2( $_name, $_cols, $_rows, $_value) {
	$myJs	=	"onkeypress=\"mark( ".$_name.") ;\"" ;
	echo "<td class=\"fEdit\" ".
			"width=\"500\">".
			"<div data-dojo-type=\"dijit/Editor\"  ".
				"id=\"".$_name."\" ".
				"name=\"".$_name."\" ".
//				"style=\"rows:10;\" ".
				"data-dojo-props=\"" .
				"height:'150px;',".
				"plugins:[".
				"'cut','copy','paste','|','bold','italic','underline','strikethrough','subscript','superscript','|', 'insertOrderedList', 'insertUnorderedList', '|', 'indent', 'outdent', 'justifyLeft', 'justifyCenter', 'justifyRight'" .
				"],".
				"extraPlugins:[".
				"'viewsource'" .
				"]\" ".
				">".
				"</div>".
	"</td>\n" ;
}
//				"extraPlugins:\"['foreColor','hiliteColor', 'fontName', 'fontSize', 'fullscreen', 'viewsource', 'createLink', 'unlink', 'insertImage'" .

/**
 *
			"<textarea id=\"" . $_name . "\" ".
			"name=\"" . $_name . "\" ".
			">" . $_value . "</textarea>".
 */
function	cellTextEdit( $_name, $_cols, $_rows, $_value, $_js="") {
	$myJs	=	"onkeypress=\"mark( ".$_name.") ;\"" ;
	echo "<td class=\"fEdit\" ".
			"width=\"240\">".
			"<textarea ".
				"id=\"".$_name."\" ".
				"name=\"".$_name."\" ".
				"cols=\"" . $_cols . "\" ".
				"rows=\"" . $_rows . "\" ".
				">".
				"</textarea>".
			"</td>\n" ;
}

/**
 *
 */
function	cellRTFEdit( $_name, $_cols, $_rows, $_value, $_js="") {
	$myJs	=	"onkeypress=\"mark( ".$_name.") ;\"" ;
	echo "<td class=\"fEdit\" ".
			"width=\"240\">".
			"<textarea id=\"" . $_name . "\" ".
			"name=\"" . $_name . "\" ".
			"cols=\"" . $_cols . "\" ".
			"rows=\"" . $_rows . "\" ".
			$_js . " " . $myJs .
			">" . $_value . "</textarea></td>\n" ;
}

/**
 *
 */
function	cellSpin( $_name, $_size, $_max, $_value, $_js="") {
	$myJs	=	"onkeypress=\"mark( ".$_name.") ;\" " ;
	echo "<td class=\"fDate\"width=\"240\">".
			"<input id=\"" . $_name . "\" ".
			"data-dojo-type=\"dijit/form/NumberSpinner\" " .
			"constraints=\"{ min:0,max:99,places:0}\" " .
			"name=\"" . $_name . "\" ".
			"smaldelta=\"1\" ".
//			"size=\"" . $_size . "\" ".
//			"maxlength=\"" . $_max . "\" ".
			"value=\"" . $_value . "\" ". 
			$_js . " " . $myJs . " />".
			"</td>\n" ;
}

function	cellEditFloat( $_name, $_size, $_max, $_value, $_js="") {
	$mySize	=	$_size ;
	$myMax	=	$_max ;
	$myJs	=	"onkeypress=\"mark( ".$_name.") ;\"" ;
	echo "<td class=\"fEdit\"width=\"240\">".
			"<input id=\"" . $_name . "\" ".
			"data-dojo-type=\"dijit/form/NumberTextBox\" " .
			"constraints=\"{ min:0,places:2}\" " .
			"name=\"" . $_name . "\" ".
			"size=\"" . $mySize . "\" ".
			"maxlength=\"" . $myMax . "\" ".
			"value=\"" . $_value . "\" ". 
			$_js . " " . $myJs . " />".
			"</td>\n" ;
}

/**
 *
 */
function	cellDate( $_name, $_size, $_max, $_value, $_js="") {
	$myJs	=	"onkeypress=\"mark( ".$_name.") ;\"" ;
	echo "<td class=\"fDate\"width=\"240\">".
			"<input id=\"" . $_name . "\" ".
			"name=\"" . $_name . "\" ".
			"data-dojo-type=\"dijit/form/DateTextBox\" " .
			$_js . " " . $myJs . " />".
			"</td>\n" ;
}

/**
 *
 */
function	cellEditDbl( $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2, $_js="", $_js2="") {
	$mySize	=	$_size ;
	$myMax	=	$_max ;
	$myJs	=	"onkeypress=\"mark( ".$_name.") ;\"" ;
	echo "<td class=\"fEdit\"width=\"240\">" ;
	echo "<input id=\"" . $_name . "\" ".
			"name=\"" . $_name . "\" ".
			"size=\"" . $mySize . "\" ".
			"maxlength=\"" . $myMax . "\" ".
			"value=\"" . $_value . "\" ". 
			$_js . " " . $myJs . " />" ;

	$mySize	=	$_size2 ;
	$myMax	=	$_max2 ;
	$myJs	=	"onkeypress=\"mark( ".$_name2.") ;\"" ;
	echo "<input id=\"" . $_name2 . "\" ".
			"name=\"" . $_name2 . "\" ".
			"size=\"" . $mySize . "\" ".
			"maxlength=\"" . $myMax . "\" ".
			"value=\"" . $_value2 . "\" ". 
			$_js2 . " " . $myJs . " />" ;
	echo "</td>\n" ;
}

/**
 *
 */
function	cellEditDblBR( $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2, $_js="") {
	echo "<td class=\"fEdit\"width=\"240\"><input id=\"" . $_name . "\" name=\"" . $_name . "\" size=\"" . $_size . "\" maxlength=\"" . $_max . "\" value=\"" . $_value . "\" " . $_js . " /><br/>" ;
	echo "<input id=\"" . $_name2 . "\" name=\"" . $_name2 . "\" size=\"" . $_size2 . "\" maxlength=\"" . $_max2 . "\" value=\"" . $_value2 . "\" " . $_js . " /></td>\n" ;
}

/**
 *
 */
function	cellEdit( $_name, $_size, $_max, $_value, $_js="") {
	$mySize	=	$_size ;
	$myMax	=	$_max ;
	$myJs	=	"onkeypress=\"mark( ".$_name.") ;\"" ;
	echo "<td class=\"fEdit\" width=\"240\">".
			"<input id=\"" . $_name . "\" ".
			"name=\"" . $_name . "\" ".
			"size=\"" . $mySize . "\" ".
			"maxlength=\"" . $myMax . "\" ".
			"value=\"" . $_value . "\" ". 
			$_js . " " . $myJs . " />".
			"</td>\n" ;
}

function	cellDisplay( $_name, $_size, $_max, $_value) {
	echo "<td class=\"fDisplay\" width=\"240\"><input readonly id=\"" . $_name . "\" name=\"" . $_name . "\" size=\"" . $_size . "\" maxlength=\"" . $_max . "\" value=\"" . $_value . "\" /></td>\n" ;
}

/**
 *
 */
function	cellDisplayDbl( $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2) {
	echo "<td class=\"fDisplay\" width=\"240\"><input readonly id=\"" . $_name . "\" name=\"" . $_name . "\" size=\"" . $_size . "\" maxlength=\"" . $_max . "\" value=\"" . $_value . "\" />&nbsp;/&nbsp;" ;
	echo "<input readonly id=\"" . $_name2 . "\" name=\"" . $_name2 . "\" size=\"" . $_size2 . "\" maxlength=\"" . $_max2 . "\" value=\"" . $_value2 . "\" /></td>\n" ;
}

/**
 *
 */
function	cellDisplayDblBR( $_name, $_size, $_max, $_value, $_name2, $_size2, $_max2, $_value2) {
	echo "<td class=\"fDisplay\" width=\"240\"><input readonly name=\"" . $_name . "\" size=\"" . $_size . "\" maxlength=\"" . $_max . "\" value=\"" . $_value . "\" /><br/>" ;
	echo "<input readonly name=\"" . $_name2 . "\" size=\"" . $_size2 . "\" maxlength=\"" . $_max2 . "\" value=\"" . $_value2 . "\" /></td>\n" ;
}

/**
 *
 */
function	cellFile( $_name, $_size, $_max, $_value) {
	echo "<td class=\"fDisplay\" width=\"240\"><input id=\"" . $_name . "\" name=\"" . $_name . "\" size=\"" . $_size . "\" maxlength=\"" . $_max . "\" value=\"" . $_value . "\" type=\"file\" /></td>\n" ;
}

/**
 *
 */
function	cellOption( $_name, $_option, $_value, $_js) {
	echo "<td class=\"fEdit\" width=\"240\">" . Opt::optionRet( $_option, $_value, $_name, $_js) . "</td>\n" ;
}
function	cellFlag( $_name, $_flag, $_value, $_js) {
	echo "<td class=\"fEdit\" width=\"240\">" . Opt::flagRet( $_flag, $_value, $_name, $_js) . "</td>\n" ;
}
function	cellCB( $_name, $_flag, $_value, $_js) {
	echo "<td class=\"fEdit\" width=\"240\">" . Opt::cbRet( $_flag, $_value, $_name, $_js) . "</td>\n" ;
}
/**
 *
 */
function	cellOptionS( $_name, $_option, $_value) {
	echo "<td class=\"fEdit\" width=\"240\">" . Opt::optionRetS( $_option, $_value, $_name) . "</td>\n" ;
}

/**
 *
 */
function	cellDispOption( $_name, $_option, $_value) {
	echo "<td class=\"fDisplay\" width=\"240\">" . Opt::optionretRO( $_option, $_value, $_name) . "</td>\n" ;
}

/**
 *
 */
function	cellNote( $_note) {
	echo "<td class=\"fNote\">" . $_note . "</td>\n" ;
}

/**
 *
 */
function	skipTable( $modName) {

	global	$_dbStartRow, $_dbCount, $_dbFilter ;

?><center>
  <form action="<?php echo $modName ; ?>.php" name="Form" method="POST">
    <table>
	<tr>
		<td>
			Filter:&nbsp;<input type="text" name="_dbFilter" value="<?php echo $_dbFilter ; ?>" tabindex="1" border="0" />
			</td>
		<td>
			<input type="text" name="_dbStartRow" size="4" maxlength="4" value="<?php echo $_dbStartRow ; ?>" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/Rsrc/licon/Blue/24/Repeat.png" name="setStartRow" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/Rsrc/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/Rsrc/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/Rsrc/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/Rsrc/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" />
			</td>
		<td>
			<input type="image" type="submit" src="/Rsrc/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" />
			</td>
	</tr>
    </table>
  </form>
</center><?php
}

/**
 *
 */
function	cellBtnEdit( $_object, $_id) {
	echo "<td class=\"fEdit\">\n" ;
	buttonEdit( $_object, $_id) ;
	echo "</td>\n" ;
}

/**
 *
 */
function	cellBtnUpdate( $_object) {
	echo "<td class=\"fUpdate\">\n" ;
	buttonUpdate( $_object) ;
	echo "</td>\n" ;
}

/**
 *
 */
function	cellHover( $_jsOver, $_jsOut) {
	if ( strlen( $_jsOver) != 0) {
?>
	<td class="fEdit">
		<input type="image" src="/Rsrc/licon/yellow/32/chat_01.png" name="NONE" value="=0" tabindex="1" border="0" onclick="<?php echo $_jsOver ; ?> ; return false ;" />
	</td>
<?php
	} else {
?>
	<td class="fEdit">
	</td>
<?php
	}
}

/**
 *
 */
function	buttonEdit( $_object, $_id) {

?>
		<input type="image" type="submit" src="/Rsrc/licon/yellow/32/tool.png" name="NONE" value="=0" tabindex="1" border="0" onclick="return edit<?php echo $_object . "( " . $_id . ")" ;?> ;" />
<?php
}

/**
 *
 */
function	cellBtnGoTo( $_formName, $_script, $_mainAction, $_iName, $_iValue, $_target="") {
	echo "<td class=\"fEdit\">\n" ;
	buttonGoTo( $_formName, $_script, $_mainAction, $_iName, $_iValue, $_target) ;
	echo "</td>\n" ;
}

/**
 *
 */
function	buttonGoTo( $_formName, $_action, $_iName, $_iValue, $_pN, $_target) {

?>
	<form name="<?php echo $_formName ; ?>" method="POST" action="<?php echo $_action ; ?>" target="<?php echo $_target ; ?>">
		<input type="hidden" name="<?php echo $_iName ; ?>" value="<?php echo $_iValue ; ?>" tabindex="1" border="0" />
		<input type="hidden" name="_pN" value="<?php echo $_pN ; ?>" tabindex="2" border="0" />
		<input type="image" type="submit" src="/Rsrc/licon/yellow/24/tool.png" name="VOID" value="E" border="0" />
		</form>
<?php
}

/**
 *
 */
function	buttonMoveUp( $_formName, $_action, $_subAction, $_iName, $_iValue) {
	global	$_execId ;
?>
	<form name="<?php echo $_formName ; ?>" method="POST" action="<?php echo $_action ; ?>">
		<input type="hidden" name="_execId" readonly value="<?php echo $_execId ; ?>" />
		<input type="hidden" name="_action" value="<?php echo $_subAction ; ?>" tabindex="1" border="0" />
		<input type="hidden" name="<?php echo $_iName ; ?>" value="<?php echo $_iValue ; ?>" tabindex="1" border="0" />
		<input type="image" type="submit" src="/Rsrc/licon/yellow/24/up_01.png" name="actionMoveUp" value="E" border="0" />
		</form>
<?php
}

/**
 *
 */
function	buttonMoveDown( $_formName, $_action, $_subAction, $_iName, $_iValue) {
	global	$_execId ;
?>
	<form name="<?php echo $_formName ; ?>" method="POST" action="<?php echo $_action ; ?>">
		<input type="hidden" name="_execId" readonly value="<?php echo $_execId ; ?>" />
		<input type="hidden" name="_action" value="<?php echo $_subAction ; ?>" tabindex="1" border="0" />
		<input type="hidden" name="<?php echo $_iName ; ?>" value="<?php echo $_iValue ; ?>" tabindex="1" border="0" />
		<input type="image" type="submit" src="/Rsrc/licon/yellow/24/down.png" name="actionMoveDown" value="E" border="0" />
		</form>
<?php
}

/**
 *
 */
function	cellBtnDelete( $_formName, $_script, $_mainAction, $_iName, $_iValue, $_btnName) {
	echo "<td class=\"fEdit\">\n" ;
	buttonDelete( $_formName, $_script, $_mainAction, $_iName, $_iValue, $_btnName) ;
	echo "</td>\n" ;
}

/**
 *
 */
function	cellBtnLookup( $_jsCall) {
	echo "<td class=\"fEdit\">\n" ;
	buttonLookup( $_jsCall) ;
	echo "</td>\n" ;
}

/**
 *
 */
function	buttonDelete( $_formName, $_script, $_mainAction, $_iName, $_iValue, $_btnName="actionDelete", $_addText="") {
	global	$_execId ;
?>
	<form name="<?php echo $_formName ; ?>" method="POST" action="<?php echo $_script ; ?>" onsubmit="return conf( 'Wirklich l�schen ?') ; ">
		<input type="hidden" name="_execId" readonly value="<?php echo $_execId ; ?>" />
		<input type="hidden" name="_action" value="<?php echo $_mainAction ; ?>" tabindex="1" border="0" />
		<input type="hidden" name="<?php echo $_iName ; ?>" value="<?php echo $_iValue ; ?>" tabindex="1" border="0" />
		<?php echo $_addText ;		?>
		<input type="image" type="submit" src="/Rsrc/licon/yellow/24/Recycle.png" name="<?php echo $_btnName ; ?>" value="E" border="0" />
		</form>
<?php
}

/**
 *
 */
function	buttonSubEdit( $_formName, $_script, $_mainAction, $_iName, $_iValue, $_btnName, $_target) {
	global	$_execId ;
?>
	<form name="<?php echo $_formName ; ?>" method="POST" action="<?php echo $_script ; ?>" target="<?php echo $_target ; ?>">
		<input type="hidden" name="_execId" readonly value="<?php echo $_execId ; ?>" />
		<input type="hidden" name="_action" value="<?php echo $_mainAction ; ?>" tabindex="1" border="0" />
		<input type="hidden" name="<?php echo $_iName ; ?>" value="<?php echo $_iValue ; ?>" tabindex="1" border="0" />
		<input type="image" type="submit" src="/Rsrc/licon/yellow/24/tool.png" name="<?php echo $_btnName ; ?>" value="E" border="0" />
		</form>
<?php
}

/**
 *
 */
function	buttonExpand( $_formName, $_script, $_mainAction, $_iName, $_iValue) {
	global	$_execId ;
?>
	<form name="<?php echo $_formName ; ?>" method="POST" action="<?php echo $_script ; ?>" onsubmit="return conf( 'Wirklich alle Unterpositionen einfuegen ?') ; ">
		<input type="hidden" name="_execId" readonly value="<?php echo $_execId ; ?>" />
		<input type="hidden" name="_action" value="<?php echo $_mainAction ; ?>" tabindex="1" border="0" />
		<input type="hidden" name="<?php echo $_iName ; ?>" value="<?php echo $_iValue ; ?>" tabindex="1" border="0" />
		<input type="image" type="submit" src="/Rsrc/licon/Blue/18/object_10.png" name="actionExpand" value="E" border="0" />
		</form>
<?php
}

/**
 *
 */
function	buttonForward( $_formName, $_script, $_mainAction, $_iName, $_iValue) {
	global	$_execId ;
?>
	<form name="<?php echo $_formName ; ?>" method="POST" action="<?php echo $_script ; ?>" onsubmit="return conf( 'Wirklich weitergehen ?') ; ">
		<input type="hidden" name="_execId" readonly value="<?php echo $_execId ; ?>" />
		<input type="hidden" name="_action" value="<?php echo $_mainAction ; ?>" tabindex="1" border="0" />
		<input type="hidden" name="<?php echo $_iName ; ?>" value="<?php echo $_iValue ; ?>" tabindex="1" border="0" />
		<input type="image" type="submit" src="/Rsrc/licon/Blue/18/Forward_01.png" name="actionForward" value="E" border="0" />
		</form>
<?php
}

/**
 *
 */
function	buttonCollapse( $_formName, $_script, $_mainAction, $_iName, $_iValue) {
	global	$_execId ;
?>
	<form name="<?php echo $_formName ; ?>" method="POST" action="<?php echo $_script ; ?>" onsubmit="return conf( 'Wirklich alle Unterpositionen loeschen ?') ; ">
		<input type="hidden" name="_execId" readonly value="<?php echo $_execId ; ?>" />
		<input type="hidden" name="_action" value="<?php echo $_mainAction ; ?>" tabindex="1" border="0" />
		<input type="hidden" name="<?php echo $_iName ; ?>" value="<?php echo $_iValue ; ?>" tabindex="1" border="0" />
		<input type="image" type="submit" src="/Rsrc/licon/Blue/18/object_11.png" name="actionCollapse" value="E" border="0" />
		</form>
<?php
}

/**
 *
 */
function	buttonText( $_formName, $_script, $_mainAction, $_action, $_iName, $_iValue, $_text) {
	global	$_execId ;
?>
	<form name="<?php echo $_formName ; ?>" method="POST" action="<?php echo $_script ; ?>" onsubmit="return conf( 'Wirklich l�schen ?') ; ">
		<input type="hidden" name="_execId" readonly value="<?php echo $_execId ; ?>" />
		<input type="hidden" name="_action" value="<?php echo $_mainAction ; ?>" tabindex="1" border="0" />
		<input type="hidden" name="<?php echo $_iName ; ?>" value="<?php echo $_iValue ; ?>" tabindex="1" border="0" />
		<input type="submit" name="action<?php echo $_action ; ?>" value="<?php echo $_text ; ?>" border="0" />
		</form>
<?php
}

/**
 *
 */
function	buttonLookup( $_jsCall) {
?>
    <input type="image" src="/Rsrc/licon/yellow/18/question.png" name="actionSel" value="S" tabindex="10015" border="0" onclick="<?php echo $_jsCall ; ?>" />
<?php
}

/**
 *
 */
function	buttonUpdate( $_jsCall) {
?>
    <input type="image" src="/Rsrc/licon/yellow/18/object_03.png" name="actionUpd" value="S" tabindex="10015" border="0" onclick="<?php echo $_jsCall ; ?>" />
<?php
}

/**
 *
 */
function	getBrowserVar( $_name) {
	if ( isset( $_POST[$_name])) {
		return $_POST[$_name] ;
	} else if ( isset( $_GET[$_name])) {
        return $_GET[$_name] ;
	} else {
		return "UNDEFINED" ;
	}
}

/**
 *
 */
function    getFloat( $buf, $dc) {
	$val    =   0.0 ;
	$div    =   1.0 ;
	$dcFound    =   false ;
	for ( $i=0 ; $i<strlen($buf) ; $i++) {
		if ( '0' <= $buf[$i] && $buf[$i] <= '9') {
			if ( ! $dcFound) {
				$val    *=  10.0 ;
				$val    +=  ord( $buf[$i]) - 48 ;
			} else {
				$div    *=  10.0 ;
				$val    +=  ( ord( $buf[$i]) - 48) / $div ;
			}
		} else if ( $buf[$i] == $dc) {
			$dcFound    =   true ;
		}
	}
	return $val ;
}

/**
 *
 */
function    getInt( $buf) {
	$val    =   0 ;
	for ( $i=0 ; $i<strlen($buf) ; $i++) {
		if ( '0' <= $buf[$i] && $buf[$i] <= '9') {
			$val    *=  10 ;
			$val    +=  ord( $buf[$i]) - 48 ;
		}
	}
	return $val ;
}

/**
 * 
 */
function	showKunde( $_kunde, $_kundeKontakt, $_addCode="") {

?>
<table>
	<tr>
	<td>
	<fieldset>
	<legend><?php echo FTr::tr("Customer") ; ?>
		<input type="image" src="/Rsrc/licon/yellow/18/zoom.png" name="auswahlKunde" value="Ausw&auml;hlen" <?php echo $_addCode ?>
				title="&Ouml;ffnet ein popup Fenster f&uuml;r Auswahl" />
		</legend>
	<table>
		<?php
			rowEditDbl( FTr::tr( "Customer no."), "_IKundeNr", 10, 10, $_kunde->KundeNr, "_DKundeKontaktNr", 10, 10, $_kundeKontakt->KundeKontaktNr, "") ;
			rowEditDblBR( FTr::tr( "Company"), "_IFirmaName1", 48, 64, $_kunde->FirmaName1, "_IFirmaName2", 48, 64, $_kunde->FirmaName2, "",
								"onkeypress=\"newK() ;\" ") ;
			rowEditDbl( FTr::tr( "Street / No."), "_IStrasse", 24, 32, $_kunde->Strasse, "_IHausnummer", 6, 12, $_kunde->Hausnummer, "") ;
			rowEditDbl( FTr::tr( "ZIP / City"), "_IPLZ", 6, 10, $_kunde->PLZ, "_IOrt", 24, 32, $_kunde->Ort, "") ;
			rowOption( FTr::tr( "Country"), "_ILand", Opt::getRLaender(), $_kunde->Land) ;
			rowEdit( FTr::tr( "Phone"), "_ITelefon", 24, 32, $_kunde->Telefon, "") ;
			rowEdit( FTr::tr( "FAX"), "_IFAX", 24, 32, $_kunde->FAX, "") ;
			rowEdit( FTr::tr( "E-Mail"), "_IeMail", 24, 32, $_kunde->eMail, "") ;
			?>
	</table>
	</fieldset>
	</td>
	<!--								-->
	<!--								-->
	<!--								-->
	<td>
	<fieldset>
	<legend><?php echo FTr::tr( "Contact") ; ?>
		<input type="image" src="/Rsrc/licon/yellow/18/zoom.png" name="auswahlKunde" value="Ausw&auml;hlen"
				title="&Ouml;ffnet ein popup Fenster f&uuml;r Auswahl" />
		</legend>
	<table> 
		<?php
			rowEdit( FTr::tr( "Customner contact no."), "_IKundeKontaktNr", 10, 10, $_kundeKontakt->KundeKontaktNr, "") ;
			rowOption( FTr::tr( "Anrede"), "_IAnrede", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Salutation'"), "Herr", FTr::tr( "HELP-Salutation")) ;
			rowOption( FTr::tr( "Title"), "_ITitel", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Title'"), "", FTr::tr( "HELP-Titel")) ;
			rowEdit( FTr::tr( "First name"), "_IVorname", 24, 64, $_kundeKontakt->Vorname, "",
								"onkeypress=\"newKK() ;\" ") ;
			rowEdit( FTr::tr( "Name"), "_IName", 24, 64, $_kundeKontakt->Name, "",
								"onkeypress=\"newKK() ;\" ") ;
			rowEdit( FTr::tr( "Addendum"), "_IAdrZusatz", 16, 16, $_kundeKontakt->AdrZusatz, "") ;
			rowEdit( FTr::tr( "Phone"), "_IKKTelefon", 16, 16, $_kundeKontakt->Telefon, "") ;
			rowEdit( FTr::tr( "FAX"), "_IKKFAX", 16, 16, $_kundeKontakt->FAX, "") ;
			rowEdit( FTr::tr( "E-Mail"), "_IKKeMail", 16, 16, $_kundeKontakt->eMail, "") ;
			?>
	</table>
	</fieldset>
	</td>
	</tr>
</table>
<?php

}

?>
