<?php
/**
 * required include files
 */
require_once( "globalLib.php" );
function	gridTop( $_dtvObj, $_form) {
?>
<form name="<?php echo $_form ; ?>" id="<?php echo $_form ; ?>" style="margin-top:0px;margin-bottom:0px;">
	<?php	if ( strpos( $_form, "Top") !== false) {		?>
	<input type="hidden" name="_SOrdMode" id="_SOrdMode" value="INoASINoA" />
	<?php	}											?>
	<table style="border:0; width:100%">
		<thead>
		<tr>
			<td align="left"><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1"/>
			<select id="_SRowCOunt" name="_SRowCount">
				<option selected="" value="5">5</option>
				<option selected="true" value="10">10</option>
				<option selected="" value="20">20</option>
			</select></td>
			<td align="right">
				Search:&nbsp;
				<input type="text" name="_ISearch" onkeypress="return <?php echo $_dtvObj ; ?>.search( event, '<?php echo $_form ; ?>') ;"/>
			</td>
			<td align="left"><div class="memu-icon sprite-search" onclick="<?php echo $_dtvObj ; ?>.searchWE( '<?php echo $_form ; ?>') ;"></div></td>
			<td><div class="memu-icon sprite-dleft" onclick="<?php echo $_dtvObj ; ?>.onFirstPage('<?php echo $_form ; ?>') ;"></div>
			<div class="memu-icon sprite-left" onclick="<?php echo $_dtvObj ; ?>.onPreviousPage('<?php echo $_form ; ?>') ;"></div>
			<div class="memu-icon sprite-reload" onclick="<?php echo $_dtvObj ; ?>.onThisPage('<?php echo $_form ; ?>') ;"></div>
			<div class="memu-icon sprite-right" onclick="<?php echo $_dtvObj ; ?>.onNextPage('<?php echo $_form ; ?>') ;"></div>
			<div class="memu-icon sprite-dright" onclick="<?php echo $_dtvObj ; ?>.onLastPage('<?php echo $_form ; ?>') ;"></div></td>
		</tr>
		</thead>
	</table>
</form>
<?php
}
function	gridBot( $_dtvObj, $_form) {
?>
<form name="<?php echo $_form ; ?>" id="<?php echo $_form ; ?>" style="margin-top:0px;margin-bottom:0px;">
	<?php	if ( strpos( $_form, "Top") !== false) {		?>
	<input type="hidden" name="_SOrdMode" id="_SOrdMode" value="INoASINoA" />
	<?php	}											?>
	<table style="border:0; width:100%">
		<thead>
		<tr>
			<td align="left"><div class="memu-icon sprite-add" onclick="<?php echo $_dtvObj ; ?>.addItem() ;"></div></td>
		</tr>
		</thead>
	</table>
</form>
<?php
}
?>
