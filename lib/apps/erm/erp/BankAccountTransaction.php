<?php
/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * 
 * 
 * Beispiele:
 * ==========
 * 
 * Buchungss�tze:
 * --------------
 * 
 * Erstellung einer Ausgangsrechnung (Rechnung an Kunden)
 * 	- normal:	Rechnung an Kunden
 *					- Forderungen an Warenbestand+Steuer
 *	- Dienstl.:	Rechnung an Kunden
 *					- Forderungen an 
 *
 * Bezahlung einer Ausgangsrechnung (Rechnung an Kunden)
 *	- normal:	Kunde zahlt per Bank�berweisung
 *					- Bank an Forderungen
 * 	- Paypal:	Kunde zahlt an Paypal, Paypal beh�lt Geb�hren ein
 * 					- Paypal an Forderungen
 * 					- Geb�hren an Paypal
 * 
 * Eingang einer Eingangsrechnung (Rechnung vom Lieferanten)
 * 	- normal:	Rechnung vom Lieferanten
 *					- Warenbestand+Steuer an Verbindlichkeiten
 *
 * Bezahlung einer Eingangsrechnung (Rechnung vom Lieferanten)
 * 	- normal:	Lieferant wird per Bank�berweisung bezahlt
 * 					- Verbindlichkeiten an Bank
 *	- bar:		Lieferantn wird per Bargeld bezahlt
 *
 *	- EC:		Lieferant wird per EC-Karte bezahlt, Post
 *					- Post an Bank
 *	- EC:		Lieferant wird per EC-Karte bezahlt, mit Mwst. 
 * 					- Materialeingang+Steuer an Bank
 * 
 * Abschlussbuchung:
 * -----------------
 * 
 * Steuern
 * 	- USt. Soll > Haben
 * 					- USt. Verbindlichkeiten an USt.
 *	- USt. Soll < Haben
 *					- USt. an USt. Verbindlichkeiten
 *
 *
 */
class	BankAccountTransaction	extends	FDbObject	{
	/**
	 * 
	 */
	function	__construct( $_id=-1) {
		parent::__construct( "BankAccountTransaction", "Id") ;
		if ( $_id >= 0) {
			$this->setId( $_id) ;
		} else {
		}
	}
	/**
	 * 
	 * 
	 */
	public	function	book( $_accountNo) {
		require_once( "FaJournalLineItem.php" );
		$myJournalLine	=	new FaJournalLine() ;
		$myJournalLine->JournalNo	=	"2013" ;
		$myJournalLine->getNextLineNo() ;
		$myJournalLine->Description	=	$this->Action . ", " . $this->Description ;
		$myJournalLine->storeInDb() ;
		/**
		 *	update the BankAccount* record with the JournalNo and LineNo
		 */
		$this->JournalNo	=	$myJournalLine->JournalNo ;
		$this->LineNo	=	$myJournalLine->LineNo ;
		$this->updateColInDb( "JournalNo") ;
		$this->updateColInDb( "LineNo") ;
		/**
		 *
		 */
		$myJournalLineItem	=	new FaJournalLineItem() ;
		$myJournalLineItem->JournalNo	=	$myJournalLine->JournalNo ;
		$myJournalLineItem->LineNo	=	$myJournalLine->LineNo ;
		/**
		 *	do the debit booking (Soll)
		 */
		if ( $this->Amount < 0) {
			/**
			 *	do the debit booking (Soll)
			 */
			$myJournalLineItem->getNextLineItemNo() ;
			$myJournalLineItem->AccountDebit	=	$_accountNo ;
			$myJournalLineItem->AccountCredit	=	"" ;
			$myJournalLineItem->AmountDebit		=	-1 * $this->Amount ;
			$myJournalLineItem->AmountCredit	=	0.0 ;
			$myJournalLineItem->storeInDb() ;
			/**
			 *	do the credit booking (Haben)
			 */
			$myJournalLineItem->getNextLineItemNo() ;
			$myJournalLineItem->AccountDebit	=	"" ;
			$myJournalLineItem->AccountCredit	=	"****" ;
			$myJournalLineItem->AmountDebit		=	0.0 ;
			$myJournalLineItem->AmountCredit	=	-1 * $this->Amount ;
			$myJournalLineItem->storeInDb() ;
		} else {
			/**
			 *	do the debit booking (Soll)
			 */
			$myJournalLineItem->getNextLineItemNo() ;
			$myJournalLineItem->AccountDebit	=	"****" ;
			$myJournalLineItem->AccountCredit	=	"" ;
			$myJournalLineItem->AmountDebit		=	$this->Amount ;
			$myJournalLineItem->AmountCredit	=	0.0 ;
			$myJournalLineItem->storeInDb() ;
			/**
			 *	do the credit booking (Haben)
			 */
			$myJournalLineItem->getNextLineItemNo() ;
			$myJournalLineItem->AccountDebit	=	"" ;
			$myJournalLineItem->AccountCredit	=	$_accountNo ;
			$myJournalLineItem->AmountDebit		=	0.0 ;
			$myJournalLineItem->AmountCredit	=	$this->Amount ;
			$myJournalLineItem->storeInDb() ;
		}
	}
}
?>
