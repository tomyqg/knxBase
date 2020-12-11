<?php

function	zeigeMerkzettel( $_step, $_merkzettel) {
	global	$myKunde ;
	$totalProMwstSatz	=	array() ;
	$mwstSatz	=	array( "A" => 19.0, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	//
	// generate the basic artikel data
	//
	$where	=	"MerkzettelNr = '$_merkzettel->MerkzettelNr' " ;
	$where	.=	"ORDER BY MerkzettelNr, PosNr ASC " ;
	$myMerkzettelPosten	=	new MerkzettelPosten() ;
?>
<fieldset>
<legend><b><?php echo FTr::tr( "Wishlist") ; ?></b></legend>
	<table>
		<tr>
			<td><?php echo FTr::tr( "Wishlist Nr") ; ?></td>
			<td><?php echo $_merkzettel->MerkzettelNr ; ?></td>
			<td>
				<form method="post" action="/Merkzettel.html">
					<input type="hidden" name="_action" value="deleteMZ"/>
					<input type="submit" value="L&ouml;schen" class="buttonBasic" />
				</form>
			</td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Date") ; ?></td>
			<td><?php echo $_merkzettel->Datum ; ?></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Unique Id") ; ?></td>
			<td><?php echo $_merkzettel->MerkzettelUniqueId ; ?></td>
		</tr>
		<tr>
		<td><?php echo FTr::tr( "Items") ; ?></td>
			<td><?php echo $_merkzettel->Positionen ; ?></td>
		</tr>
	</table>
	<table class="tab_a">
		<tr class="zeile_i_1">
		<th class="zelle_m_1"><?php echo FTr::tr( "Item") ; ?></th>
		<th class="zelle_m_2"><?php echo FTr::tr( "Article No.") ; ?></th>
		<th class="zelle_m_3"><?php echo FTr::tr( "Description") ; ?></th>
		<th class="zelle_m_4"><?php echo FTr::tr( "Qty.") ; ?></th>
		<th class="zelle_m_5"><?php echo FTr::tr( "Price/pc.") ; ?></th>
		<th class="zelle_m_6"><?php echo FTr::tr( "Price/total") ; ?></th>
<?php
	if ( $_step == 0) {
		echo "<th class=\"zelle_m_7\">Optionion</th> \n" ;
	}
?>
	<th class="zelle_m_7"><?php echo FTr::tr( "Stock") ; ?></th>
<?php
	echo "</tr> \n" ;
	for ( $myMerkzettelPosten->_firstFromDb( $where) ;
			$myMerkzettelPosten->_valid ;
			$myMerkzettelPosten->_nextFromDb()) {
		$myArtikel	=	new Artikel() ;
		$myArtikel->ArtikelNr	=	$myMerkzettelPosten->ArtikelNr ;
		$myArtikel->fetchFromDb() ;

		if ( $_step < 4) {

			echo "<div id=\"MZId" . $myMerkzettelPosten->Id . "\" style=\"display: inline\"> \n" ;
			echo "<form action=\"/Merkzettel.html\" method=\"post\">" ;
			echo "<tr class=\"zeile_i_n\"> \n" ;
			echo "<td class=\"zelle_m_1\">" . $myMerkzettelPosten->PosNr . "</td> \n" ;
			echo "<td class=\"zelle_m_2\">" . $myMerkzettelPosten->ArtikelNr . "</td> \n" ;
			echo "<td class=\"zelle_m_3\">" . $myArtikel->getFullText( $myMerkzettelPosten->MengeProVPE) ;
			echo "</td>\n" ;
			if ( $_step == 0) {
				echo "<td class=\"zelle_m_4\">\n" ;
				printf( "<input size=\"4\" name=\"_IQty\" value=\"%d\"></input>", $myMerkzettelPosten->Menge) ;
				echo "</td> \n" ;
			} else {
				echo "<td class=\"zelle_m_4\">" ;
				printf( "%3d", $myMerkzettelPosten->Menge) ;
				echo "</td> \n" ;
			}
			echo "<td class=\"zelle_m_5\">" ;
			printf( "%9.2f", $myMerkzettelPosten->Preis) ;
			echo "</td> \n" ;
			echo "<td class=\"zelle_m_6\">" . sprintf( "%9.2f", $myMerkzettelPosten->GesamtPreis) . "</td> \n" ;
			if ( $_step == 0) {
				echo "<td class=\"zelle_m_7\"><form action=\"/Merkzettel.html\" method=\"post\">" ;
				echo "<input type=\"hidden\" name=\"_action\" value=\"updatemzposten\">" ;
				echo "<input type=\"hidden\" name=\"_HId\" value=\"".$myMerkzettelPosten->Id."\">" ;
				echo "<input type=\"submit\" value=\"Update\" class=\"buttonBasic\" />" ;
				echo "</td> \n" ;
			}

			$myArtikelBestand	=	new ArtikelBestand() ;
			$myArtikelBestand->ArtikelNr	=	$myArtikel->ArtikelNr ;
			$myArtikelBestand->getDefault() ;
			if ( $myKunde->_valid) {
				if ( ($myArtikelBestand->Lagerbestand - $myArtikelBestand->Reserviert - $myArtikelBestand->Kommissioniert) >= $myMerkzettelPosten->Menge) {
					echo "<td class=\"zelle_m_7\">Yes</td>" ;
				} else if ( ($myArtikelBestand->Lagerbestand - $myArtikelBestand->Reserviert - $myArtikelBestand->Kommissioniert + $myArtikelBestand->Bestellt) >= $myMerkzettelPosten->Menge) {
					echo "<td class=\"zelle_m_7\">Ordered</td>" ;
				} else {
					echo "<td class=\"zelle_m_7\">No</td>" ;
				}
			} else {
				echo "<td class=\"zelle_m_7\">LOGIN!</td>" ;
			}

			echo "</tr> \n" ;
			echo "</form> \n" ;
			echo "</div> \n" ;
		}
		if ( ! isset( $totalProMwstSatz[ $myArtikel->MwstSatz])) {
			$totalProMwstSatz[ $myArtikel->MwstSatz]	=	0.0 ;
		}
		$totalProMwstSatz[ $myArtikel->MwstSatz]	+=	$myMerkzettelPosten->GesamtPreis ;
	}
	echo "<tr class=\"zeile_i_1\"> \n" ;
	echo "<td class=\"zelle_m_1\"></td> \n" ;
	echo "<td class=\"zelle_m_2\"></td> \n" ;
	echo "<td class=\"zelle_m_3\">".FTr::tr( "Total net") . ":</td> \n" ;
	echo "<td class=\"zelle_m_4\"></td> \n" ;
	echo "<td class=\"zelle_m_5\"></td> \n" ;
	echo "<td class=\"zelle_m_6\">" . sprintf( "%9.2f", $_merkzettel->GesamtPreis) . "</td> \n" ;
	if ( $_step == 0) {
		echo "<td class=\"zelle_m_7\"></td> \n" ;
	}
	echo "<td class=\"zelle_m_7\"></td> \n" ;
	echo "</tr> \n" ;
	//
	//
	//
	foreach ( $totalProMwstSatz as $satz => $total) {
?>
		<tr class="zeile_i_1">
			<td class="zelle_m_1"></td>
			<td class="zelle_m_2"></td>
			<td class="zelle_m_3"><?php echo FTr::tr( "Total tax(es)") ; ?>:</td>
			<td class="zelle_m_4"><?php echo "$mwstSatz[$satz] %" ; ?>:</td>
			<td class="zelle_m_5"><?php echo "[" . sprintf( "%.2f", $total) . "]" ?></td>
			<td class="zelle_m_6"><?php printf( "[%9.2f]", $totalProMwstSatz[ $satz] * $mwstSatz[$satz] / 100.0) ; ?></td>
<?php
		if ( $_step == 0) {
			echo "<td class=\"zelle_m_7\"></td> \n" ;
		}
?>
			<td class="zelle_m_7"></td>
		</tr>
<?php
	}
?>
		<tr class="zeile_i_1">
			<td class="zelle_m_1"></td>
			<td class="zelle_m_2"></td>
			<td class="zelle_m_3"><?php echo FTr::tr( "Total taxes") ; ?>:</td>
			<td class="zelle_m_4"></td>
			<td class="zelle_m_5"></td>
			<td class="zelle_m_6"><?php echo sprintf( "%9.2f", $_merkzettel->GesamtMwst) ; ?></td>
<?php
	if ( $_step == 0) {
		echo "<td class=\"zelle_m_7\"></td> \n" ;
	}
?>
			<td class="zelle_m_7"></td>
		</tr>
		<tr class="zeile_i_1">
			<td class="zelle_m_1"></td>
			<td class="zelle_m_2"></td>
			<td class="zelle_m_3"><?php echo FTr::tr( "Total gross value") ; ?>:</td>
			<td class="zelle_m_4"></td>
			<td class="zelle_m_5"></td>
			<td class="zelle_m_6"><?php echo sprintf( "%9.2f", $_merkzettel->GesamtPreis + $_merkzettel->GesamtMwst) ; ?></td>
<?php
	if ( $_step == 0) {
		echo "<td class=\"zelle_m_7\"></td> \n" ;
	}
?>
			<td class="zelle_m_7"></td>
		</tr>
	</table>
</fieldset>
<?php
}

function	zeigeKdAnf( $_step, $_kdAnf) {
	/**
	 *
	 */
	$myKdAnfPosten	=	new KdAnfPosten() ;
	$myArtikel	=	new Artikel() ;
?>
<fieldset>
	<legend><b><?php echo FTr::tr( "Your enquiry") ; ?></b></legend>
	<table>
		<tr>
			<td><?php echo FTr::tr ( "Our enquiry No.") ; ?></td>
			<td><?php echo $_kdAnf->KdAnfNr ; ?></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Date") ; ?></td>
			<td><?php echo $_kdAnf->Datum ; ?></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Items") ; ?></td>
			<td><?php echo $_kdAnf->Positionen ; ?></td>
		</tr>
	</table>
	<table class="tab_a">
		<tr class="zeile_i_1">
			<th class="zelle_m_1"><?php echo FTr::tr( "Item") ; ?></th>
			<th class="zelle_m_2"><?php echo FTr::tr( "Article No.") ; ?></th>
			<th class="zelle_m_3"><?php echo FTr::tr( "Description") ; ?></th>
			<th class="zelle_m_4"><?php echo FTr::tr( "Qty.") ; ?></th>
			<th class="zelle_m_5"><?php echo FTr::tr( "Price/pc.") ; ?></th>
			<th class="zelle_m_6"><?php echo FTr::tr( "Price/total") ; ?></th>
		</tr>
<?php
	$bestellungPosCtr	=	1 ;
	$where	=	"KdAnfNr = '$_kdAnf->KdAnfNr' " ;
	$where	.=	"ORDER BY PosNr ASC " ;
	for ( $myKdAnfPosten->_firstFromDb( $where) ;
			$myKdAnfPosten->_valid ;
			$myKdAnfPosten->_nextFromDb()) {
		$myArtikel	=	new Artikel() ;
		$myArtikel->ArtikelNr	=	$myKdAnfPosten->ArtikelNr ;
		$myArtikel->fetchFromDb() ;
?>
		<tr class="zeile_i_n">
			<td class="zelle_m_1" align="right"><?php echo $myKdAnfPosten->PosNr ; ?></td>
			<td class="zelle_m_2"><?php echo $myKdAnfPosten->ArtikelNr ; ?></td>
			<td class="zelle_m_3"><?php echo $myArtikel->ArtikelBez1 ; ?><br />
				<?php echo $myArtikel->ArtikelBez2 ; ?><br/>
				<?php echo $myArtikel->textFromMenge( $myKdAnfPosten->MengeProVPE) ; ?>
				</td>
			<td class="zelle_m_4" align="right"><?php echo $myKdAnfPosten->Menge ; ?></td>
			<td class="zelle_m_5" align="right"><?php echo sprintf( "%9.2f", $myKdAnfPosten->Preis) ; ?></td>
			<td class="zelle_m_6" align="right"><?php echo sprintf( "%9.2f", $myKdAnfPosten->GesamtPreis) ; ?></td>
		</tr>
<?php
	}
?>
		<tr class="zeile_i_1">
			<td class="zelle_m_1"></td>
			<td class="zelle_m_2"></td>
			<td class="zelle_m_3"><?php echo FTr::tr( "Total net") ; ?>:</td>
			<td class="zelle_m_4"></td>
			<td class="zelle_m_5"></td>
			<td class="zelle_m_6" align="right"><?php printf( "%9.2f", $_kdAnf->GesamtPreis) ; ?></td>
		</tr>
		<tr class="zeile_i_1">
			<td class="zelle_m_1"></td>
			<td class="zelle_m_2"></td>
			<td class="zelle_m_3"><?php echo FTr::tr( "Total taxes") ; ?>:</td>
			<td class="zelle_m_4"></td>
			<td class="zelle_m_5"></td>
			<td class="zelle_m_6" align="right"><?php printf( "%9.2f", $_kdAnf->GesamtMwst) ; ?></td>
		</tr>
		<tr class="zeile_i_1">
			<td class="zelle_m_1"></td>
			<td class="zelle_m_2"></td>
			<td class="zelle_m_3"><?php echo FTr::tr( "Total gross value") ; ?>:</td>
			<td class="zelle_m_4"></td>
			<td class="zelle_m_5"></td>
			<td class="zelle_m_6" align="right"><?php printf( "%9.2f", $_kdAnf->GesamtPreis + $_kdAnf->GesamtMwst) ; ?></td>
		</tr>
	</table>
</fieldset>
<?php
}

function	zeigeKdBest( $_step, $_kdBest) {

	//

	$myKdBestPosten	=	new KdBestPosten() ;

	// generate the basic artikel data

?>
<fieldset>
	<legend><b>Ihre Bestellung</b></legend>
	<table>
		<tr>
			<td>Unsere Bestellung Nr</td>
			<td><?php echo $_kdBest->KdBestNr ; ?></td>
		</tr>
		<tr>
			<td>Datum</td>
			<td><?php echo $_kdBest->Datum ; ?></td>
		</tr>
		<tr>
			<td>Anzahl Positionen</td>
			<td><?php echo $_kdBest->Positionen ; ?></td>
		</tr>
	</table>
	<table class="tab_a">
		<tr class="zeile_i_1">
			<th class="zelle_m_1"><?php echo FTr::tr( "Item") ; ?></th>
			<th class="zelle_m_2"><?php echo FTr::tr( "Article No.") ; ?></th>
			<th class="zelle_m_3"><?php echo FTr::tr( "Description") ; ?></th>
			<th class="zelle_m_4"><?php echo FTr::tr( "Qty.") ; ?></th>
			<th class="zelle_m_5"><?php echo FTr::tr( "Price/pc.") ; ?></th>
			<th class="zelle_m_6"><?php echo FTr::tr( "Price/total") ; ?></th>
			<th class="zelle_m_6"><?php echo FTr::tr( "Stock") ; ?></th>
		</tr>
<?php
	// generate the basic artikel data
	$where	=	"KdBestNr = '$__kdBest->KdBestNr' " ;
	$where	.=	"ORDER BY PosNr ASC " ;
	for ( $myKdBestPosten->_firstFromDb( $where) ;
			$myKdBestPosten->_valid ;
			$myKdBestPosten->_nextFromDb()) {
		$myArtikel	=	new Artikel() ;
		$myArtikel->ArtikelNr	=	$myKdBestPosten->ArtikelNr ;
		$myArtikel->fetchFromDb() ;
	?>
		<div><tr class="zeile_i_n">
			<td class="zelle_i_1" align="right"><?php echo $myKdBestPosten->PosNr ; ?></td>
			<td class="zelle_i_2"><?php echo $myKdBestPosten->ArtikelNr ; ?></td>
			<td class="zelle_i_3"><?php echo $myArtikel->ArtikelBez1 . "<br />" . $myArtikel->ArtikelBez2 ; ?>
				<br/><?php echo textFromMenge( $myArtikel->MengenEinheit, $myKdBestPosten->MengeProVPE) ; ?>
			</td>
			<td class="zelle_i_4" align="right"><?php echo $myKdBestPosten->Menge ; ?></td>
			<td class="zelle_i_5" align="right"><?php echo sprintf( "%9.2f", $myKdBestPosten->Preis) ; ?></td>
			<td class="zelle_i_6" align="right"><?php echo sprintf( "%9.2f", $myKdBestPosten->GesamtPreis) ; ?></td>
			<td class="zelle_i_1" align="center">
<?php
		$myArtikelBestand	=	new ArtikelBestand() ;
		$myArtikelBestand->ArtikelNr	=	$myArtikel->ArtikelNr ;
		$myArtikelBestand->fetchFromDb() ;
		if ( ($myArtikelBestand->Lagerbestand - $myArtikelBestand->Reserviert - $myArtikelBestand->Kommissioniert) >= $myKdBestPosten->Menge) {
			echo "Yes" ;
		} else if ( ($myArtikelBestand->Lagerbestand - $myArtikelBestand->Reserviert - $myArtikelBestand->Kommissioniert + $myArtikelBestand->Bestellt) >= $myKdBestPosten->Menge) {
			echo "Ordered" ;
		} else {
			echo "No" ;
		}
?>
		</td>
			</tr></div>
<?php
	}
?>
		<tr class="zeile_i_1">
			<td class="zelle_m_1"></td>
			<td class="zelle_m_2"></td>
			<td class="zelle_m_3"><?php echo FTr::tr( "Total net value") ; ?>:</td>
			<td class="zelle_m_4"></td>
			<td class="zelle_m_5"></td>
			<td class="zelle_m_6" align="right"><?php printf( "%9.2f", $_kdBest->GesamtPreis) ; ?></td>
		</tr>
		<tr class="zeile_i_1">
			<td class="zelle_m_1"></td>
			<td class="zelle_m_2"></td>
			<td class="zelle_m_3"><?php echo FTr::tr( "Total taxes") ; ?>:</td>
			<td class="zelle_m_4"></td>
			<td class="zelle_m_5"></td>
			<td class="zelle_m_6" align="right"><?php printf( "%9.2f", $_kdBest->GesamtMwst) ; ?></td>
		</tr>
		<tr class="zeile_i_1">
			<td class="zelle_m_1"></td>
			<td class="zelle_m_2"></td>
			<td class="zelle_m_3"><?php echo FTr::tr( "Total gross value") ; ?>:</td>
			<td class="zelle_m_4"></td>
			<td class="zelle_m_5"></td>
			<td class="zelle_m_6" align="right"><?php printf( "%9.2f", $_kdBest->GesamtPreis + $_kdAnf->GesamtMwst) ; ?></td>
		</tr>
	</table>
</fieldset>
<?php
}

function	zeigeKdBestFunktionen( $_step, $_kunde, $_kdBest=null) {
	global	$myConfig ;
	switch ( $_step) {
	case	0	:				// mzzeigen: was wollen wir mit dem Merkzettel jetzt machen ???
		break ;
	case	1	:				// bestellen1: anmeldung/registrierung
		break ;
	case	2	:				// bestellen2: wie wird bezahlt, geliefert ???
?>
<form name="f2" method="post" action="/Bestellen.php">
	<input type="hidden" name="step" value="3" />
	<table>
		<tr>
			<td><?php echo FTr::tr( "Terms of payment") ; ?>:</td>
			<td>
<!--
				<input type="radio" name="ModusZahl" value="<?php echo Kunde::MP_COP ; ?>" title="<?php echo FTr::tr( "SHOP-INFO-COP") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-COP") ; ?></input>
				<input type="radio" name="ModusZahl" value="<?php echo Kunde::MP_COD ; ?>" title="<?php echo FTr::tr( "SHOP-INFO-COD") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-COD") ; ?></input>
-->
				<input type="radio" name="ModusZahl" value="<?php echo Kunde::MP_COD ; ?>" title="<?php echo FTr::tr( "SHOP-INFO-COPD") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-COPD") ; ?></input>
				<?php	if ( $_kunde->TypeCust == kunde::TC_C) {					?>
					<input type="radio" name="ModusZahl" value="<?php echo Kunde::MP_COO_GP ; ?>" checked title="<?php echo FTr::tr( "SHOP-INFO-GiroPay") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-GiroPay") ; ?></input>
				<?php	} else if ( $_kunde->TypeCust == kunde::TC_B) {				?>
					<input type="radio" name="ModusZahl" value="<?php echo Kunde::MP_COO_GP ; ?>" title="<?php echo FTr::tr( "SHOP-INFO-GiroPay") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-GiroPay") ; ?></input>
					<input type="radio" name="ModusZahl" value="<?php echo Kunde::MP_INVC ; ?>" checked title="<?php echo FTr::tr( "SHOP-INFO-INVOICE") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-INVOICE") ; ?></input>
				<?php	}															?>
			</td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Delivery preferences") ; ?>:</td>
			<td>
				<input type="radio" name="ModusLief" value="<?php echo Kunde::MD_DPAC ; ?>" title="<?php echo FTr::tr( "SHOP-INFO-DLVR-PART-AT-COST") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-DLVR-PART-AT-COST") ; ?></input>
				<input type="radio" name="ModusLief" value="<?php echo Kunde::MD_DP ; ?>" title="<?php echo FTr::tr( "SHOP-INFO-DLVR-PART") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-DLVR-PART") ; ?></input>
				<input type="radio" name="ModusLief" value="<?php echo Kunde::MD_DA ; ?>" title="<?php echo FTr::tr( "SHOP-INFO-DLVR-ALL") ; ?>" checked><?php echo FTr::tr( "SHOP-TEXT-DLVR-ALL") ; ?></input>
			</td>
		</tr>
<?php	if ( $_kunde->TypeCust == Kunde::TC_B) {			?>
		<tr>
			<td><?php echo FTr::tr( "Invoicing preferences") ; ?></td>
			<td>
				<input type="radio" name="ModusRech" value="<?php echo Kunde::MI_IPP ; ?>" title="<?php echo FTr::tr( "SHOP-INFO-INVOICE-PART") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-INVOICE-PART") ; ?></input>
				<input type="radio" name="ModusRech" value="<?php echo Kunde::MI_IPC ; ?>" title="<?php echo FTr::tr( "SHOP-INFO-INVOICE-ALL") ; ?>"><?php echo FTr::tr( "SHOP-TEXT-INVOICE-ALL") ; ?></input>
			</td>
		</tr>
<?php	}										?>
		<tr>
			<td></td>
			<td>
				<input name="bestellen" type="submit" value="Akzeptieren & zu Schritt 3" class="buttonBasic" />
			</td>
		</tr>
	</table>
</form>
<?php
		break ;
	case	3	:				// bestellen3: Anerkennung AGB und Preis
?>
<form name="f3" method="post" action="/Bestellen.php">
	<input type="hidden" name="step" value="4" />
	<table>
		<tr>
			<td colspan="5">Ich habe die <a class="hier" href="/hinweise.html" target="_subInfo" title="<?php echo FTr::tr( "SHOP-INFO-OPEN-IN-NEW-WINDOW") ; ?>">AGB</a> (Allgemeinen Gesch&auml;ftsbedingungen) von <?php echo $myConfig->shop->company ; ?> gelesen und erkenne diese an:</td>
			<td>
				<input type="radio" name="AGBFrage" value="Nein" checked onClick="enable( 'bestellen') ; "><?php echo FTr::tr( "No") ; ?></input>
				<input type="radio" name="AGBFrage" value="Ja" onClick="enable( 'bestellen') ; "><?php echo FTr::tr( "Yes") ; ?></input>
			</td>
		</tr>
		<tr>
<!--
Ich habe den Gesamtpreis dieser Bestellung einschl. Mwst. und der mit dieser Bestellung verbundenen Versandkosten zur Kenntnis genommen.
-->
			<td colspan="5"><?php echo FTr::tr( "SHOP-TEXT-ORDER-ACCEPTHDLG") ; ?>
			</td>
			<td>
				<input type="radio" name="BestellFrage" value="Nein" checked onClick="enable( 'bestellen') ; "><?php echo FTr::tr( "No") ; ?></input>
				<input type="radio" name="BestellFrage" value="Ja" onClick="enable( 'bestellen') ; "><?php echo FTr::tr( "Yes") ; ?></input>
			</td>
		</tr>
		<tr>
<!--
Gutschein Code
-->
			<td><?php echo FTr::tr( "SHOP-TEXT-ORDER-PROMOTIONCODE") ; ?></td>
			<td colspan="4"><input type="text" name="_ICouponCode" value="" title="<?php echo FTr::tr( "SHOP-INFO-PROMOTION-CODE") ; ?>"/></td>
		</tr>
		<tr>
<!--
Eigene Referenznummer
-->
		<td><?php echo FTr::tr( "SHOP-TEXT-OWN-REFERENCE-NO") ; ?>
		</td>
			<td colspan="4"><input type="text" name="_IKdRefNr" value="webShop" title="<?php echo FTr::tr( "SHOP-INFO-OWN-REFERENCE-NO") ; ?>" /></td>
		</tr>
		<tr>
<!--
Anmerkungen
-->
		<td><?php echo FTr::tr( "SHOP-TEXT-REMARK") ; ?></td>
			<td colspan="4"><textarea name="_ICustText" cols="30" rows="4"></textarea></td>
		</tr>
		<tr>
			<td colspan="5"></td>
			<td><input name="bestellen" type="submit" value="<?php echo FTr::tr( "SHOP-BUTTON-SUBMIT-ORDER") ; ?>" disabled class="buttonBasic" /></td>
		</tr>
	</table>
</form>
<?php
		break ;
	case	4	:				// bestellen4: Goto GiroPay in case selected
		$myTransactionId	=	$_kdBest->KdBestNr."/".$_kdBest->KundeNr."/".$_kdBest->KundeKontaktNr ;

		?>
<form name="f4" method="post" action="/redirectToGiroPay.php">
	<input type="hidden" name="transactionId" value="<?php echo $myTransactionId ; ?>" />
	<input type="hidden" name="amount" value="<?php echo $_kdBest->GesamtPreis + $_kdBest->GesamtMwst ; ?>" />
	<table>
		<tr>
			<td><img class="text_links" src="/Rsrc/logos/logo_giropay_small.gif" alt="wimtecc.de"/></td>
			<td><?php echo FTr::tr( "SHOP-TEXT-BANK-CODE") ; ?></td>
			<td><input type="text" name="bankcode" size="8" value="" title="SHOP-INFO-BANKCODE" /></td>
			<td><input name="bestellen" type="submit" value="<?php echo FTr::tr( "SHOP-BUTTON-CONT-WIITH-GIROPAY") ; ?>" class="buttonBasic" /></td>
		</tr>
	</table>
</form>
		<?php
		break ;
	}
}

function	zeigeKdAnfFunktionen( $_step, $_kunde) {
	switch ( $_step) {
	case	0	:				// mzzeigen: was wollen wir mit dem Merkzettel jetzt machen ???
		break ;
	case	1	:				// bestellen1: anmeldung/registrierung
		break ;
	case	2	:				// bestellen3: Anerkennung AGB und Preis
?>
<form name="f2" method="post" action="/Anfrage.php">
	<input type="hidden" name="step" value="3" />
	<table>
		<tr>
			<td colspan="5">Ich habe die <a class="hier" href="/hinweise.html" target="_subInfo" title="Wird in neuem Fenster ge&ouml;ffnet">AGB</a> (Allgemeinen Gesch&auml;ftsbedingungen) von mein-mikroskop.de Karl-Heinz Welter gelesen und erkenne diese an:
			</td>
			<td>
				<input type="radio" name="AGBFrage" value="Nein" checked onClick="enable( 'anfrage') ; "><?php echo FTr::tr( "No") ; ?></input>
				<input type="radio" name="AGBFrage" value="Ja" onClick="enable( 'anfrage') ; "><?php echo FTr::tr( "Yes") ; ?></input>
			</td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "SHOP-TEXT-OWN-REFERENCE-NO") ; ?>:</td>
			<td colspan="4"><input type="text" name="_IKdRefNr" value="webShop" /></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "SHOP-TEXT-REMARK") ; ?>:</td>
			<td colspan="4"><textarea name="_ICustText" cols="30" rows="4"></textarea></td>
		</tr>
		<tr>
			<td colspan="5"></td>
			<td><input name="anfrage" type="submit" value="<?php echo FTr::tr( "SHOP-BUTTON-SUBMIT-ENQUIRY") ; ?>" disabled class="buttonBasic" /></td>
		</tr>
	</table>
</form>
<?php
		break ;
	}
}

function	zeigeMZFunktionen( $_step, $_kunde, $_kdBest=null) {
	switch ( $_step) {
	case	0	:				// mzzeigen: was wollen wir mit dem Merkzettel jetzt machen ???
?>
<table>
	<tr>
	<td>
		<form method="post" action="htmlMan/prnMZ.php" target="pdfwin">
			<input type="hidden" name="action" value="wlGetPDF" />				<!-- get as PDF				-->
			<input type="submit" value="Merkzettel drucken" class="buttonBasic" />
		</form>
	</td>
	<td>
		<form method="post" action="/Anfrage.php">
			<input type="hidden" name="step" value="2" />
			<input type="hidden" name="action" value="wlGetQuote" />			<!-- request for quotation	-->
			<input type="submit" value="Als Angebot anfordern" class="buttonBasic" />
		</form>
	</td>
	<td>
		<form method="post" action="/Bestellen.php">
			<input type="hidden" name="step" value="2" />
			<input type="hidden" name="action" value="wlOrder" />				<!-- order					-->
			<input type="submit" value="Als Bestellung aufgeben" class="buttonBasic" />
		</form>
	</td>
	<td>
		<form method="post" action="/kundeMerkzettel.html">
			<input type="hidden" name="action" value="wlStore" />				<!-- store wishlist			-->
			<input type="submit" value="Merkzettel speichern" class="buttonBasic" />
		</form>
	</td>
</table>
<?php
		break ;
	case	1	:				// bestellen1: anmeldung/registrierung
		break ;
	case	2	:				// bestellen2: wie wird bezahlt, geliefert ???
		break ;
	case	3	:				// bestellen3: Anerkennung AGB und Preis
		break ;
	case	4	:				// bestellen4: Goto GiroPay in case selected
		break ;
	}
}

function	zeigeKunde( $_step, $_kunde, $_kundeKontakt) {
?>
<fieldset>
	<legend><b><?php echo FTr::tr( "Customer Data") ; ?>:</b></legend>
	<table>
		<tr>
			<td><?php echo FTr::tr( "Customer No.") ; ?>:</td>
			<td><?php echo $_kunde->KundeNr ; ?></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Company/School/University") ; ?>:</td>
			<td><input name="_IFirmaName1" value="<?php echo $_kunde->FirmaName1 ; ?>"/></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "more Company") ; ?>:</td>
			<td><input name="_IFirmaName2" value="<?php echo $_kunde->FirmaName2 ; ?>"/></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Customer Contact No.") ; ?>:</td>
			<td><?php echo $_kundeKontakt->KundeKontaktNr ; ?></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Salutation") ; ?>:</td>
			<td><?php	echo Option::optionRet( Option::getRAnreden(), $_kundeKontakt->Anrede, "_IIAnrede") ;	?></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Title") ; ?>:</td>
			<td><?php	echo Option::optionRet( Option::getRTitel(), $_kundeKontakt->Titel, "_IITitel") ;	?></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "First (given) name") ; ?>:</td>
			<td><input name="_IIVorname" value="<?php echo $_kundeKontakt->Vorname ; ?>"/></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo FTr::tr( "Last name") ; ?>:</td>
			<td><input name="_IIName" value="<?php echo $_kundeKontakt->Name ; ?>"/></td>
			<td></td>
		</tr>
	</table>
</fieldset>
<?php
}

function	zeigeMerkzettelSchritt( $_step) {
	global	$myMerkzettel ;
	global	$myKunde ;
	echo "<table>\n" ;
	echo "<tr>\n" ;
	switch ( $_step) {
	case	0	:
		if ( $myMerkzettel->_valid) {
			echo "<td class=\"zelle_zg\">".FTr::tr( "Wishlist")."</td> \n" ;
		} else {
			echo "<td class=\"zelle_zr\">".FTr::tr( "Wishlist")."</td> \n" ;
		}
		if ( $myKunde->_valid) {
			echo "<td class=\"zelle_zg\">".FTr::tr( "Registration")."</td> \n" ;
		} else {
			echo "<td class=\"zelle_zr\">".FTr::tr( "Registration")."</td> \n" ;
		}
		echo "<td class=\"zelle_zr\">".FTr::tr( "Terms of payment")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Verification")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Confirmation")."</td> \n" ;
		break ;
	case	1	:
		echo "<td class=\"zelle_zg\">".FTr::tr( "Wishlist")."</td> \n" ;
		echo "<td class=\"zelle_zy\">".FTr::tr( "Registration")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Terms of payment")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Verification")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Confirmation")."</td> \n" ;
		break ;
	case	2	:
		echo "<td class=\"zelle_zg\">".FTr::tr( "Wishlist")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Registration")."</td> \n" ;
		echo "<td class=\"zelle_zy\">".FTr::tr( "Terms of payment")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Verification")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Confirmation")."</td> \n" ;
		break ;
	case	3	:
		echo "<td class=\"zelle_zg\">".FTr::tr( "Wishlist")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Registration")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Terms of payment")."</td> \n" ;
		echo "<td class=\"zelle_zy\">".FTr::tr( "Verification")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Confirmation")."</td> \n" ;
		break ;
	case	4	:
		echo "<td class=\"zelle_zg\">".FTr::tr( "Wishlist")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Registration")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Terms of payment")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Verification")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Confirmation")."</td> \n" ;
		break ;
	}
	echo "</tr>\n" ;
	echo "</table>\n" ;
}

function	zeigeKdAnfSchritt( $_step) {
	echo "<table>\n" ;
	echo "<tr>\n" ;
	switch ( $_step) {
	case	1	:
		echo "<td class=\"zelle_zg\">".FTr::tr( "Wishlist")."</td> \n" ;
		if ( $myKunde->_valid) {
			echo "<td class=\"zelle_zg\">".FTr::tr( "Registration")."</td> \n" ;
		} else {
			echo "<td class=\"zelle_zr\">".FTr::tr( "Registration")."</td> \n" ;
		}
		echo "<td class=\"zelle_zr\">".FTr::tr( "Submit enquiry")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Confirmation")."</td> \n" ;
		break ;
	case	2	:
		echo "<td class=\"zelle_zg\">".FTr::tr( "Wishlist")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Registration")."</td> \n" ;
		echo "<td class=\"zelle_zy\">".FTr::tr( "Submit enquiry")."</td> \n" ;
		echo "<td class=\"zelle_zr\">".FTr::tr( "Confirmation")."</td> \n" ;
		break ;
	case	3	:
		echo "<td class=\"zelle_zg\">".FTr::tr( "Wishlist")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Registration")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Submit enquiry")."</td> \n" ;
		echo "<td class=\"zelle_zg\">".FTr::tr( "Confirmation")."</td> \n" ;
		break ;
	}
	echo "</tr>\n" ;
	echo "</table>\n" ;
}

?>
