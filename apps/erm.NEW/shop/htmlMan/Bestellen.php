<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "option.php") ;

require_once( "mzlibrary.inc.php") ;

if ( isset( $_POST["step"])) {
	$step	=	$_POST["step"] ;
} else if ( isset( $_GET["step"])) {
	$step	=	$_GET["step"] ;
}

if ( $step == 3) {
?>
<script>
function enable( element) {
	if ( document.f3.AGBFrage[1].checked && document.f3.BestellFrage[1].checked) {
		document.f3.bestellen.disabled  =       false ;
	} else {
		document.f3.bestellen.disabled  =       true ;
	}
}
</script>
<?php
}
/**
 *	determine 'how' we are called. This code is only executed in case the customer is valid.
 *	$step ==
 *	1	nothing will be done
 *	2	do nothing, display customer data, wishlist and choice of payment and delivery mode
 *	3	store payment and delivery methond to wishlist
 *		display customer data, wishlist and terms-acceptance button
 *	4	store oder in the database, if customer is B2B create XML package towards
 *		backend, create order confirmation as PDF document, mail to admin, customer
 *	5	shall be called as callback only from a payment provider, status of the order will be
 *		set according to the outcome of the transaction. if payment status of the order is
 *		SP_DONE create XML package towards backend
 *	11
 */

if ( isset( $_POST['_neuKunde'])) {
	include( "regKunde.php") ;
}
/**
 * only continue if we have a valid customer
 */
if ( $myKunde->_valid) {
	if ( $step == 1) {
	} else if ( $step == 2) {
		zeigeMerkzettelSchritt( 2) ;
		echo "<h2>".FTr::tr( "Determine Shipping and Payment Mode")."</h2>" ;
		echo "<h3>".FTr::tr( "Customer Data")."</h3>" ;
		zeigeKunde( 2, $myKunde, $myKundeKontakt) ;
		echo "<h3>".FTr::tr( "Current wishlist")."</h3>" ;
		if ( $myMerkzettel->_valid) {
			zeigeMerkzettel( 2, $myMerkzettel) ;
			zeigeKdBestFunktionen( 2, $myKunde) ;
		}
	} else if ( $step == 3) {
		zeigeMerkzettelSchritt( 3) ;
		echo "<h2>".FTr::tr( "Acceptance of Terms-of-Sale and total cost of order")."</h2>" ;
		echo "<h3>".FTr::tr( "Customer Data")."</h3>" ;
		zeigeKunde( 2, $myKunde, $myKundeKontakt) ;
		echo "<h3>".FTr::tr( "Current wishlist")."</h3>" ;
		if ( $myMerkzettel->_valid) {
			$myMerkzettel->ModusLief	=	$_POST[ 'ModusLief'] ;
			$myMerkzettel->ModusZahl	=	$_POST[ 'ModusZahl'] ;
			$myMerkzettel->updateInDb() ;
			zeigeMerkzettel( 3, $myMerkzettel) ;
			zeigeKdBestFunktionen( 3, $myKunde) ;
		}
	} else if ( $step == 4) {
		/**
		 * bestellen4:
		 *
		 * create the customer order
		 */
		zeigeMerkzettelSchritt( 4) ;
		if ( $myMerkzettel->_valid && ( $myMerkzettel->Status == 0 || $myMerkzettel->Status == 7)) {
			/**
			 * @var unknown_type
			 */
			$myKdBest	=	new KdBest() ;
			$myKdBest->newFromMZ( '', '', $myMerkzettel->MerkzettelNr) ;
			$myKdBest->fetchFromDb() ;
			$myKdBest->KdRefNr	=	$_POST['_IKdRefNr'] ;
			$myKdBest->CustRem	=	"<div>" . $_POST['_ICustText'] . "</div>" ;
			$myKdBest->updateInDb() ;
			$myKdBest->updateHdlg() ;
			/**
			 *
			 */
			echo "<h3>".FTr::tr( "SHOP-ORDER-SUBMISSION-OK-HEADER")."</h3>" ;
			echo FTr::tr( "SHOP-ORDER-SUBMISSION-OK-BODY") ;
			/**
			 *
			 */
			echo "<h3>".FTr::tr( "Customer Data")."</h3>" ;
			if ( $myKunde->_valid) {
				zeigeKunde( 2, $myKunde, $myKundeKontakt) ;
			}
			/**
			 *
			 */
			zeigeMerkzettel( 4, $myMerkzettel) ;
			/**
			 *
			 */
			if ( $myConfig->cuWishlist->autoCancel) {
				$myMerkzettel->Status	=	2 ;		// merkzettel "entwerten"
				$myMerkzettel->updateInDb() ;
				$mySession->MerkzettelNr	=	"" ;
				$mySession->MerkzettelUniqueId	=	"" ;
				$mySession->updateInDb() ;
			}
			/**
			 *
			 */
			$myKdBest->infoMailSales( "") ;
			/**
			 * create the PDF version of the "order confirmation" document
			 * @var unknown_type
			 */
			$myKdBestDoc	=	new KdBestDoc() ;
			$myKdBestDoc->setKey( $myKdBest->KdBestNr) ;
			$myKdBestDoc->createPDF( '', '', '') ;
			$pdfName	=	$myConfig->path->Archive . "KdBest/" . $myKdBest->KdBestNr . ".pdf" ;
			$myKdBest->infoMailCust( $pdfName, $myKunde->eMail) ;
			/**
			 * if the order is not marked as CoO (Kunde::PT_COO
			 */
			if ( ( ! (( $myKdBest->ModusZahl & Kunde::MP_COO) == Kunde::MP_COO)) || $myConfig->shop->forceExport) {
				error_log( "KdBest '$myKdBest->KdBestNr' being handled NOT as CoO") ;
				$oFile	=	fopen( $myConfig->path->Archive."XML/down/KdBest".$myKdBest->KdBestNr.".xml", "w+") ;
				fwrite( $oFile, "<KdBestPaket>\n") ;
				$buffer	=	$myKdBest->getXML() ;
				fwrite( $oFile, $buffer) ;
				$myKdBestPosten	=	new KdBestPosten() ;
				$myKdBestPosten->KdBestNr	=	$myKdBest->KdBestNr ;
				for ( $myKdBestPosten->_firstFromDb( "KdBestNr='$myKdBest->KdBestNr' ORDER BY PosNr ") ;
							$myKdBestPosten->_valid ;
							$myKdBestPosten->_nextFromDb()) {
					$buffer	=	$myKdBestPosten->getXML() ;
					fwrite( $oFile, $buffer) ;
				}
				fwrite( $oFile, "</KdBestPaket>\n") ;
				fclose( $oFile) ;
			} else {
				error_log( "KdBest '$myKdBest->KdBestNr' being handled as CoO") ;
			}
			/**
			 * show order functions
			 */
			zeigeKdBestFunktionen( 4, $myKunde, $myKdBest) ;
		} else {
			$nextFunction	=	"bestellen2.php" ;
			include( "frag_kunde.php") ;
		}
	} else if ( $step == 5) {
		echo "<h3>".FTr::tr( "Customer Data")."</h3>" ;
		if ( $myKunde->_valid) {
			zeigeKunde( 2, $myKunde, $myKundeKontakt) ;
		}
		/**
		 * start diagnostics
		 */
//		EISSCoreObject::dumpGET() ;
//		EISSCoreObject::dumpPOST() ;
		/**
		 * end diagnostics
		 */
		/**
		 * if POST[�order_id'] is set we seem to have a GiroPay reply
		 */
		try {
			if ( isset( $_GET['order_id'])) {
				require_once( "Pay.php") ;
				$myGiroPay	=	new Pay_GiroPay() ;
				$myGPConfig	=	$myGiroPay->getConfig() ;
				$myMerchantId	=	$myGPConfig->GiroPay->MerchantId ;
				$myProjectId	=	$myGPConfig->GiroPay->ProjectId ;
				$myOrderId	=	$_GET['order_id'] ;
				$myGPCode	=	$_GET['gpCode'] ;
				$myGPHash	=	$_GET['gpHash'] ;
				/**
				 * break up te received order id to obtain:
				 * order no., customer no. and customer contact no.
				 * fetch the order from the database
				 * @var unknown_type
				 */
				$myData	=	explode( "/", $myOrderId) ;
				$myKdBest	=	new KdBest( $myData[0]) ;
				$myKundeNr	=	$myData[1] ;
				$myKundeKontaktNr	=	$myData[2] ;
				$myTransactionId	=	$myKdBest->KdBestNr."/".$myKdBest->KundeNr."/".$myKdBest->KundeKontaktNr ;
				if ( ! ( $myKdBest->KundeNr == $myKundeNr && $myKdBest->KundeKontaktNr == $myKundeKontaktNr)) {
					throw new Exception( FTr::tr( "Reply data from intermediate does not resemble valid transaction!")) ;
				}
				if ( $myKdBest->StatPayment >= Option::SP_DONE) {
					throw new Exception( FTr::tr( "This order has been paid already!")) ;
				}
//				echo "Returned data matches own expactation<br/>" ;
				require_once( "Pay.php") ;
				$myGiroPay	=	new Pay_GiroPay() ;
				$myGPConfig	=	$myGiroPay->getConfig() ;
				$myHash	=	$myGiroPay->generateHash( $myMerchantId.$myProjectId.$myTransactionId.$myGPCode, "psion0") ;
				/**
				 * check if the hash value meets, if not ... bail out
				 */
				if ( $myHash != $myGPHash) {
					throw new Exception(FTr::tr( "Reply data from intermediate does not resemble valid transaction!")) ;
				}
//				echo "Life's good ... since the reply is valid, now let's see ..." ;
//				echo "and set the order to status: paid" ;
				switch ( $myGPCode) {
				case	"4000"	:		//paymewnt ok
					$myKdBest->StatPayment	=	Option::SP_DONE ;
					$myKdBest->updateColInDb( "StatPayment") ;
					break ;
				case	"4500"	:		// payment with unknown result
					$myKdBest->StatPayment	=	Option::SP_PNDNG ;
					$myKdBest->updateColInDb( "StatPayment") ;
					throw new Exception( FTr::tr( "SHOP-GIROPAY-ERROR-4500")) ;
					break ;
				case	"4900"	:		// payment refused
					$myKdBest->StatPayment	=	Option::SP_RFSD ;
					$myKdBest->updateColInDb( "StatPayment") ;
					throw new Exception( FTr::tr( "SHOP-GIROPAY-ERROR-4900")) ;
					break ;
				default	:
					throw new Exception( FTr::tr( "SHOP-GIROPAY-ERROR-#1", array( "$myGPCode:%s"))) ;
					break ;
				}
				echo "<h3>".FTr::tr( "SHOP-PAYMENT-SUBMISSION-OK-HEADER")."</h3>" ;
				echo FTr::tr( "SHOP-PAYMENT-SUBMISSION-OK-BODY") ;
				/**
				 *
				 */
				$oFile	=	fopen( $myConfig->path->Archive."XML/down/KdBest".$myKdBest->KdBestNr.".xml", "w+") ;
				fwrite( $oFile, "<KdBestPaket>\n") ;
				$buffer	=	$myKdBest->getXML() ;
				fwrite( $oFile, $buffer) ;
				$myKdBestPosten	=	new KdBestPosten() ;
				$myKdBestPosten->KdBestNr	=	$myKdBest->KdBestNr ;
				for ( $myKdBestPosten->_firstFromDb( "KdBestNr='$myKdBest->KdBestNr' ORDER BY PosNr ") ;
							$myKdBestPosten->_valid ;
							$myKdBestPosten->_nextFromDb()) {
					$buffer	=	$myKdBestPosten->getXML() ;
					fwrite( $oFile, $buffer) ;
				}
				fwrite( $oFile, "</KdBestPaket>\n") ;
				fclose( $oFile) ;
			} else {
				throw new Exception( FTr::tr( "Can't identify response from payment intermediate!")) ;
			}
		} catch( Exception $e) {
			echo "<h3>".FTr::tr( "SHOP-PAYMENT-SUBMISSION-NOT-OK-HEADER")."</h3>" ;
			echo FTr::tr( "SHOP-PAYMENT-SUBMISSION-NOT-OK-BODY") ;
			$msg	=	$e->getMessage() ;
			echo "<br/>" ;
			echo FTr::tr( "Error message: '#1'", array( "$msg:%s"))."<br/>" ;
		}
	} else if ( $step == 11) {
		/**
		 * bestellen4:
		 *
		 * create the customer order
		 */
		zeigeMerkzettelSchritt( 4) ;
		$myKdBest	=	new KdBest( $_POST['_DKdBestNr']) ;
		if ( $myKdBest->_valid) {
			/**
			 *
			 */
			echo "<h3>".FTr::tr( "SHOP-ORDER-SUBMISSION-OK-HEADER")."</h3>" ;
			echo FTr::tr( "SHOP-ORDER-SUBMISSION-OK-BODY") ;
			/**
			 *
			 */
			echo "<h3>".FTr::tr( "Customer Data")."</h3>" ;
			if ( $myKunde->_valid) {
				zeigeKunde( 2, $myKunde, $myKundeKontakt) ;
			}
			/**
			 *
			 */
//			zeigeMerkzettel( 4, $myMerkzettel) ;
			/**
			 * show order functions
			 */
			zeigeKdBestFunktionen( 4, $myKunde, $myKdBest) ;
		} else {
			$nextFunction	=	"bestellen2.php" ;
			include( "frag_kunde.php") ;
		}
	}
} else {
	$nextFunction	=	"Bestellen.php" ;
	include( "frag_kunde.php") ;
}

?>
