<!--

-->
<?php
	require_once( "config.inc.php") ;
	if ( isset( $_SESSION['lastStockId'])) {
		$lastStockId	=	$_SESSION['lastStockId'] ;
	} else {
		$lastStockId	=	"" ;
	}
	if ( isset( $_POST['_IStockId'])) {
		$stockId	=	$_POST['_IStockId'] ;
	} else {
		$stockId	=	"" ;
	}
	if ( isset( $_POST['_IArticleNo'])) {
		$articleNo	=	$_POST['_IArticleNo'] ;
	} else {
		$articleNo	=	"" ;
	}
	$debug	=	"" ;
	if ( isset( $_POST['_IStockId']) && isset( $_POST['_IArticleNo'])) {
		try {
			$article	=	new Artikel( $articleNo) ;
			if ( $article->isValid()) {
				if ( $lastStockId != $stockId) {
					$debug	=	"lastStockId different from current, nothing changed<br/>" ;
				} else {
					$article->setStockLocationForArticle( $articleNo, -1, $stockId) ;	
				}
			} else {
				$debug	=	"article not valid, nothing changed<br/>" ;
			}
			$articleStock	=	new ArtikelBestand() ;
			$articleStock->getDefault( $articleNo) ;
		} catch ( Exception $e) {
			$error	=	"EXCEPTION" ;
		}
	}
	$_SESSION['lastStockId']	=	$stockId ;
	error_log( "mobileStockScan.php: stockId = '$stockId', articleNo = '$articleNo'") ;
?>
<html dir="ltr">
	<head>
		<script type="text/javascript">
var	lastArticleNo	=	"*" ;
var	lastStockId		=	"*" ;
function	dataEntered() {
	data	=	getFormField( "mobileStockScanData", "_IData") ;
	stockId	=	getFormField( "mobileStockScanData", "_IStockId") ;
	articleNo	=	getFormField( "mobileStockScanData", "_IArticleNo") ;
	if ( data.value.substr(0,3).toUpperCase() == "WMS") {
		stockId.value	=	data.value.toUpperCase() ;
	} else {
		articleNo.value	=	data.value ;
	}
	myForm	=	getForm( "mobileStockScanData") ;
	myForm.submit() ;
	return false ;
}

function	refocus() {
	data	=	getFormField( "mobileStockScanData", "_IData") ;
	data.focus() ;
	data.select() ;
}
function	focus() {
	data	=	getFormField( "mobileStockScanData", "_IFocus") ;
	data.click() ;
}
function	getFormField( _form, _field) {
	var	myForm = null ;
	var	myFields ;
	var	targetField	=	null ;
	/**
	 * find the form we shall fill out
	 */
	for ( var i=0 ; i < document.forms.length ; i++) {
		if ( document.forms[i].name == _form) {
			myForm	=	document.forms[i] ;
		}
	}
	if ( myForm) {
		myFields	=	myForm.elements ;
		for ( var i=0 ; i < myFields.length && targetField == null ; i++) {
			if ( myFields[i].name == _field) {
				targetField	=	myFields[i] ;
			}
		}
		if ( targetField) {
		} else {
		}
	} else {
	}
	return targetField ;
}
function	getForm( _form) {
	var	myForm ;
	var	targetForm	=	null ;
	/**
	 * find the form
	 */
	for ( var i=0 ; i < document.forms.length ; i++) {
		if ( document.forms[i].name == _form) {
			targetForm	=	document.forms[i] ;
		}
	}
	return targetForm ;
}
		</script>
	</head>
	<?php
		date_default_timezone_set( "Europe/Berlin") ;	// needs to be here, otherwise php complains
		if ( isset( $_POST['_IStockId'])) {
			$stockId	=	$_POST['_IStockId'] ;
		} else {
			$stockId	=	"" ;
		}
		if ( isset( $_POST['_IArticleNo'])) {
			$articleNo	=	$_POST['_IArticleNo'] ;
		} else {
			$articleNo	=	"" ;
		}
		error_log( "mobileStockScan.php: stockId = '$stockId', articleNo = '$articleNo'") ;
	?>
	<body onload="focus() ;">
		<form enctype="multipart/form-data" method="POST" action="mobileStockScan.php" name="mobileStockScanData" id="mobileStockScanData">  
			<table>
				<tr>
					<th>Data:</th>
					<td><input type="text" name="_IData" id="Data" value="" onkeypress="if(event.keyCode==13){dataEntered();}else{return true ;} return false ; "/>
					</td>
				</tr>
				<tr>
					<th>Stock no.:</th>
					<td><input type="text" name="_IStockId" id="_IStockId" value="<?php echo $stockId ; ?>" onkeypress="return false ; "/>
					</td>
				</tr>
				<tr>
					<th>Article no.:</th>
					<td><input type="text" name="_IArticleNo" id="_IArticleNo" value="<?php echo $articleNo ; ?>" onkeypress="return false ; "/>
					</td>
				</tr>
				<tr>
					<th>Refocus:</th>
					<td><button type="button" name="_IFocus" id="_IFocus" value="Focus" onclick="refocus() ; ">Focus
					</td>
				</tr>
			</table>
		</form>
		<?php if ( isset( $error)) {			?>
			<b>EXCEPTION</b>
		<?php }									?>
		<?php if ( isset( $debug)) {			?>
			<b>Debug</b><br/><?php echo $debug ; ?><br/>
		<?php }									?>
		<?php if ( isset( $articleStock)) {		?>
		<table>
			<tbody>
				<tr><th>Bestand:</th><td><?php echo $articleStock->Lagerbestand ; ?></td></tr>
				<tr><th>Artikelno.:</th><td><?php echo $article->ArtikelNr ; ?></td></tr>
				<tr><th rowspan="2">Bezeichnung:</th>
					<td><?php echo $article->ArtikelBez1 ; ?></td>
					<td><?php echo $article->ArtikelBez2 ; ?></td>
				</tr>
			</tbody>
		</table>
		<?php }									?>
		Version: 0.0.3 RC <?php include( "../../rc.txt") ; ?><br/>
	</body>
</html>
