/**
 *
 */
function	showTableFrontEndCust( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divFrontEndClients	=	document.getElementById( "tableFrontEndCust") ;
	divFrontEndClients.innerHTML	=	"Hier sind wir\n" ;

	myData	=	"" ;
	tableClients	=	response.getElementsByTagName( "Filename") ;
	if ( tableClients) {
		myData	+=	"TableClients contains " + tableClients.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Filename</th>" ;
		myData	+=	"<th>Funktion</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableClients.length ; i++) {
			filename	=	response.getElementsByTagName( "Filename")[i] ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + filename.textContent + "</td>" ;
			myLine	+=	importButton( 'Base', 'FrontEndConnect', '/Base/hdlObject.php', 'importCust', filename.textContent, '', '', null, 'showTableFrontEndCust') ;
			myLine	+=	deleteButton( 'Base', 'FrontEndConnect', '/Base/hdlObject.php', 'deleteCust', filename.textContent, '', '', null, 'showTableFrontEndCust') ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divFrontEndClients.innerHTML	=	myData ;
}

function	showTableFrontEndMZ( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divFrontEndMZ	=	document.getElementById( "tableFrontEndMZ") ;
	divFrontEndMZ.innerHTML	=	"Hier sind wir\n" ;

	myData	=	"" ;
	tableClients	=	response.getElementsByTagName( "Filename") ;
	if ( tableClients) {
		myData	+=	"TableClients contains " + tableClients.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Filename</th>" ;
		myData	+=	"<th>Funktion</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableClients.length ; i++) {
			filename	=	response.getElementsByTagName( "Filename")[i] ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + filename.textContent + "</td>" ;
			myLine	+=	importButton( 'Base', 'FrontEndConnect', '/Base/hdlObject.php', 'importCuWish', filename.textContent, '', '', null, 'showTableFrontEndMZ') ;
			myLine	+=	deleteButton( 'Base', 'FrontEndConnect', '/Base/hdlObject.php', 'deleteCuWish', filename.textContent, '', '', null, 'showTableFrontEndMZ') ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divFrontEndMZ.innerHTML	=	myData ;
}

function	showTableFrontEndCuRfqt( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divFrontEndCuRfqt	=	document.getElementById( "tableFrontEndCuRfqt") ;
	divFrontEndCuRfqt.innerHTML	=	"Hier sind wir\n" ;

	myData	=	"" ;
	tableClients	=	response.getElementsByTagName( "Filename") ;
	if ( tableClients) {
		myData	+=	"TableClients contains " + tableClients.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Filename</th>" ;
		myData	+=	"<th>Funktion</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableClients.length ; i++) {
			filename	=	response.getElementsByTagName( "Filename")[i] ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + filename.textContent + "</td>" ;
			myLine	+=	importButton( 'Base', 'FrontEndConnect', '/Base/hdlObject.php', 'importCuRfqt', filename.textContent, '', '', null, 'showTableFrontEndCuRfqt') ;
			myLine	+=	deleteButton( 'Base', 'FrontEndConnect', '/Base/hdlObject.php', 'deleteCuRfqt', filename.textContent, '', '', null, 'showTableFrontEndCuRfqt') ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divFrontEndCuRfqt.innerHTML	=	myData ;
}

function	showTableFrontEndCuOrdr( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divFrontEndCuOrdr	=	document.getElementById( "tableFrontEndCuOrdr") ;
	divFrontEndCuOrdr.innerHTML	=	"Hier sind wir\n" ;

	myData	=	"" ;
	tableClients	=	response.getElementsByTagName( "Filename") ;
	if ( tableClients) {
		myData	+=	"TableClients contains " + tableClients.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Filename</th>" ;
		myData	+=	"<th>Funktion</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableClients.length ; i++) {
			filename	=	response.getElementsByTagName( "Filename")[i] ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + filename.textContent + "</td>" ;
			myLine	+=	importButton( 'Base', 'FrontEndConnect', '/Base/hdlObject.php', 'importCuOrdr', filename.textContent, '', '', null, 'showTableFrontEndCuOrdr') ;
			myLine	+=	deleteButton( 'Base', 'FrontEndConnect', '/Base/hdlObject.php', 'deleteCuOrdr', filename.textContent, '', '', null, 'showTableFrontEndCuOrdr') ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divFrontEndCuOrdr.innerHTML	=	myData ;
}

