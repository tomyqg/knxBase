/**
 * valve
 *
 * registers the module in the central database
 */
/**
 * @returns {valve}
 */
new valve() ;
function	valve() {
	dBegin( 1, "valve.js", "valve", "__constructor()") ;
	wapScreen.call( this, "valve") ;
	this.package	=	"ModBase" ;
	this.module	=	"Valve" ;
	this.coreObject	=	"Valve" ;
	this.keyForm	=	"ValveKeyData" ;
	this.keyField	=	getFormField( 'ValveKeyData', 'ValveId') ;
//	this.delConfDialog	=	"/ModBase/Valve/confValveDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Valve"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	dTrace( 2, "valve.js", "valve", "*", "creating wapSelector") ;
	this.select	=	new wapSelector( this, "selValve", {
										objectClass:	"Valve"
									,	module:			"ModBase"
									,	screen:			"Valve"
									,	selectorName:	"selValve"
									,	formFilterName: "formSelValveFilter"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "valve{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcValveMain", "tcValveMain_cpGeneral") ;
															dEnd( 102, "valve{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
													}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "valve.js", "valve", "*", "creating wapGrid") ;
	this.gridValveOV	=	new wapGrid( this, "gridValveOV", {
										object:			"Valve"
									,	screen:			"valve"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainvalve{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainvalve{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "valve{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageValveMainDataEntry") ;
															dEnd( 102, "valve{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "valve.js", "valve", "*", "calling this.gridValveOV._onFirstPage()") ;
	this.gridValveOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "valve.js", "valve", "*", "creating gridLeverLength") ;
	this.gridValveParameters	=	new wapGrid( this, "gridValveParameters", {
										object:			"ValveParameter"
									,	screen:			"Valve"
									,	parentDS:		this.dataSource
									,	editorName:		"edtValveParameter"
									,	moduleName: 	"ModMainData"
									,	subModuleName:	"Valve"
									,	onDataSourceLoadFi:	function( _parent, _xmlData) {
															dBegin( 102, "mainvalve{valve.js}", "wapGrid{gridValveParameters}", "onDataSoddurceLoaded( <_parent>, <_xmlData>)") ;
//															this.show() ;
															var	valveGraph	=	document.getElementById( "valveGraph") ;
															this.parent	=	_parent ;
															valveGraph.width	=	"300" ;
															valveGraph.height	=	"200" ;
															var	valveCtx	=	valveGraph.getContext( "2d") ;
															valveCtx.fillStyle	=	"red" ;
															valveCtx.strokeStyle	=	"blue" ;
															valveCtx.lineWidth	=	1 ;
			/**
			 * paint the graph
			 */
			var	rows	=	_xmlData.getElementsByTagName( "ValveParameter") ;
			var	x1	=	0 ;
			var	y1	=	0 ;
			var	x2	=	0 ;
			var	y2	=	0 ;
			var	x1d	=	0 ;
			var	y1d	=	0 ;
			var	x2d	=	0 ;
			var	y2d	=	0 ;
			var	fromX	=	0 ;
			var	toX	=	0 ;
			var	a	=	0 ;
			var	b	=	0 ;
			var	b	=	0 ;
			if ( valveGraph.points)
				delete valveGraph.points ;
			valveGraph.points	=	[] ;
			valveGraph.draggingPoint	=	false ;
			for ( var il0=0 ; il0 < rows.length ; il0++) {
				id		=	parseFloat( rows[il0].getElementsByTagName( "Id")[0].childNodes[0].nodeValue) ;
				toX		=	parseFloat( rows[il0].getElementsByTagName( "ToX")[0].childNodes[0].nodeValue) ;
				a		=	parseFloat( rows[il0].getElementsByTagName( "a")[0].childNodes[0].nodeValue) ;
				b		=	parseFloat( rows[il0].getElementsByTagName( "b")[0].childNodes[0].nodeValue) ;
				c		=	parseFloat( rows[il0].getElementsByTagName( "c")[0].childNodes[0].nodeValue) ;

				x1	=	fromX ;
				y1	=	( fromX * fromX * a) + ( fromX * b) + c;
				x2	=	toX ;
				y2	=	( toX * toX * a) + ( toX * b) + c ;

				x1d	=	x1 * 30 ;
				y1d	=	200 - y1 * 20 ;
				x2d	=	x2 * 30 ;
				y2d	=	200 - y2 * 20 ;

				valveGraph.points[il0]	=	{ i: il0, id: id, x1: x1, y1: y1, x2: x2, y2: y2, x1d: x1d, y1d: y1d, x2d: x2d, y2d: y2d, fromX: fromX, toX: toX, a: a, b: b, c: c} ;

				valveCtx.strokeStyle	=	"blue" ;
				valveCtx.beginPath() ;
				valveCtx.moveTo( x1d, y1d) ;
				valveCtx.lineTo( x2d, y2d) ;
				valveCtx.stroke() ;

				valveCtx.strokeStyle	=	"red" ;
				if ( y1d < 0)
					y1d	=	0 ;
				else if ( y1d > 200) {
					y1d	=	200 ;
				}
				valveCtx.beginPath() ;
				valveCtx.arc( x1d, y1d, 5, 0, Math.PI * 2) ;
				valveCtx.stroke() ;

				if ( y2d < 0)
					y2d	=	0 ;
				else if ( y2d > 200) {
					y2d	=	200 ;
				}
				valveCtx.beginPath() ;
				valveCtx.arc( x2d, y2d, 5, 0, Math.PI * 2) ;
				valveCtx.stroke() ;
				fromX	=	toX ;
			}
			dTrace( 2, "valve.js", "valve", "eventInGraph", "il0 := " + il0.toString()) ;
			valveGraph.points[il0]	=	{ i: il0, x1: x2, y1: y2, x2: 0, y2: 0, x1d: x2d, y1d: y2d, x2d: 0, y2d: 0, fromX: fromX, toX: 0, a: 0, b: 0, c: 0} ;
															dEnd( 102, "mainvalve{valve.js}", "wapGrid{gridValveParameters}", "onDataSourceLoaded( <_parent>, <_xmlData>)") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "valve.js", "valve", "fncLink()") ;
		dEnd( 1, "valve.js", "valve", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "valve.js", "valve", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "valve.js", "valve", "fncShow( <_response>)") ;
		dEnd( 1, "valve.js", "valve", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "valve.js", "valve", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "valve.js", "valve", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridValveParameters._onFirstPage() ;							// refresh the dataSource
		}
		dEnd( 1, "valve.js", "valve", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	this.mousedown	=	function( _evt) {
		var	valveGraph	=	document.getElementById( "valveGraph") ;
		var	valveCtx	=	valveGraph.getContext( "2d") ;
		var	relX, relY ;
		dBegin( 1, "valve.js", "valve", "mousedown( <_evt>)") ;
		relX	=	Math.round( _evt.pageX - valveGraph.getBoundingClientRect().left)
		relY	=	Math.round( _evt.pageY - valveGraph.getBoundingClientRect().top)
		dTrace( 2, "valve.js", "valve", "eventInGraph", "x := " + relX.toString() + ", y = " + relY.toString()) ;
		for ( var il0=0 ; il0 < this.points.length ; il0++) {
			var	myPoint	=	this.points[il0] ;
			dTrace( 2, "valve.js", "valve", "mousedown", "x := " + myPoint.x1d.toString() + ", y = " + myPoint.y1d.toString()) ;
			if ( ( myPoint.x1d - 10) < relX && relX < ( myPoint.x1d + 10) && ( myPoint.y1d - 10) < relY && relY < ( myPoint.y1d + 10) && valveGraph.draggingPoint === false) {
				dTrace( 2, "valve.js", "valve", "eventInGraph", "Hittttttttttttt on il0 := " + il0.toString()) ;
															valveCtx.fillStyle	=	"red" ;
															valveCtx.lineWIdth	=	5 ;
				valveCtx.beginPath() ;
				valveCtx.arc( myPoint.x1d, myPoint.y1d, 10, 0, Math.PI * 2) ;
				valveCtx.stroke() ;
				valveGraph.draggingPoint	=	true ;
				valveGraph.point	=	il0 ;
				_evt.target.setCapture();
			}
		}
		dEnd( 1, "valve.js", "valve", "mousedown( <_evt>)") ;
	}
	this.mouseup	=	function( _evt) {
		var	relX, relY ;
		var	valveGraph	=	document.getElementById( "valveGraph") ;
		var	valveCtx	=	valveGraph.getContext( "2d") ;
		dBegin( 1, "valve.js", "valve", "mouseup( <_evt>)") ;
		valveGraph.draggingPoint	=	false ;
		/**
		 * now perform the necessary updates in the database table to reflect the changes
		 */
		if ( valveGraph.point == 0) {
			this.parent.gridValveParameters.dataSource.id	=	valveGraph.points[0].id ;
			this.parent.gridValveParameters.dataSource.dispatch( true, "updDep", null, "b="+valveGraph.points[0].b.toString() + "&c="+valveGraph.points[0].c.toString()) ;
		} else if ( valveGraph.point == this.points.length-1) {
			var	myPoint	=	this.points[ valveGraph.point-1] ;
			this.parent.gridValveParameters.dataSource.id	=	myPoint.id ;
			this.parent.gridValveParameters.dataSource.dispatch( true, "updDep", null, "b="+myPoint.b.toString() + "&c="+myPoint.c.toString() + "&ToX="+myPoint.toX.toString()) ;
		} else {
			var	myPoint	=	this.points[ valveGraph.point] ;
			this.parent.gridValveParameters.dataSource.id	=	myPoint.id ;
			this.parent.gridValveParameters.dataSource.dispatch( true, "updDep", null, "b="+myPoint.b.toString() + "&c="+myPoint.c.toString() + "&ToX="+myPoint.toX.toString()) ;
			var	myPoint	=	this.points[ valveGraph.point-1] ;
			this.parent.gridValveParameters.dataSource.id	=	myPoint.id ;
			this.parent.gridValveParameters.dataSource.dispatch( true, "updDep", null, "b="+myPoint.b.toString() + "&c="+myPoint.c.toString() + "&ToX="+myPoint.toX.toString()) ;
		}
		dEnd( 1, "valve.js", "valve", "mouseup( <_evt>)") ;
	}
	this.mousemove	=	function( _evt) {
		var	valveGraph	=	document.getElementById( "valveGraph") ;
		var	valveCtx	=	valveGraph.getContext( "2d") ;
		var	relX, relY ;
//		dBegin( 1, "valve.js", "valve", "eventInGraph( <_evt>)") ;
		if ( valveGraph.draggingPoint) {
			relX	=	Math.round( _evt.pageX - valveGraph.getBoundingClientRect().left)
			relY	=	Math.round( _evt.pageY - valveGraph.getBoundingClientRect().top)
			/**
			 *	calculate:
			 */
			valveCtx.clearRect( 0, 0, valveGraph.width, valveGraph.height) ;
			var	newX	=	relX / 30 ;
			var	newY	=	( 200 - relY) / 20 ;
			/**
			 *	check plausibilty of new-X; we can not move across the limiting x's
			 */
			if ( newX < 0) {
				newX	=	0 ;
			} else if ( newX > 9.9) {
				newX	=	9.9 ;
			}
			if ( newY < 0) {
				newY	=	0 ;
			} else if ( newY > 9.9) {
				newY	=	9.9 ;
			}
			var	myPoint	=	this.points[ valveGraph.point] ;
			if ( valveGraph.point == 0) {
				if ( newX >= myPoint.x2)
					newX	=	myPoint.x2 - 0.1 ;
			} else if ( valveGraph.point == this.points.length-1) {
				if ( newX <= valveGraph.points[valveGraph.point-2].x2)
					newX	=	valveGraph.points[valveGraph.point-2].x2 + 0.1 ;
			} else {
				if ( newX >= myPoint.x2)
					newX	=	myPoint.x2 - 0.1 ;
				if ( newX <= valveGraph.points[valveGraph.point-1].x1)
					newX	=	valveGraph.points[valveGraph.point-1].x1 + 0.1 ;
			}
			/**
			 *
			 */
			if ( valveGraph.point < this.points.length) {
				var	myPoint	=	this.points[ valveGraph.point] ;
				myPoint.x1	=	newX ;
				myPoint.y1	=	newY ;
				myPoint.b	=	( myPoint.y2 - myPoint.y1) / ( myPoint.x2 - myPoint.x1) ;
				myPoint.c	=	myPoint.y1  - ( myPoint.b * myPoint.x1) ;
			}
			/**
			 *
			 */
			if ( valveGraph.point > 0) {
				var	myPoint	=	this.points[ valveGraph.point - 1] ;
				myPoint.x2	=	newX ;
				myPoint.y2	=	newY ;
				myPoint.b	=	( myPoint.y2 - myPoint.y1) / ( myPoint.x2 - myPoint.x1) ;
				myPoint.c	=	myPoint.y1  - ( myPoint.b * myPoint.x1) ;
			}
			/**
			 *	re-draw the line
			 */
			var	fromX	=	0 ;
			for ( var il0=0 ; il0 < this.points.length-1 ; il0++) {
				var	myPoint	=	this.points[il0] ;
				var	toX		=	myPoint.x2 ;
				var	a		=	myPoint.a ;
				var	b		=	myPoint.b ;
				var	c		=	myPoint.c ;
				var	id		=	myPoint.id ;

				x1	=	fromX ;
				y1	=	(fromX * fromX * a) + ( fromX * b) + c ;
				x2	=	toX ;
				y2	=	(toX * toX * a) + ( toX * b) + c ;

				x1d	=	x1 * 30 ;
				y1d	=	200 - y1 * 20 ;
				x2d	=	x2 * 30 ;
				y2d	=	200 - y2 * 20 ;
//				dTrace( 2, "valve.js", "valve", "eventInGraph",
//							"  i := " + il0.toString()
//						+	", x1 := " + x1.toString()
//						+	", y1 := " + y1.toString()
//						+	", x2 := " + x2.toString()
//						+	", y2 := " + y2.toString()
//						) ;

				valveGraph.points[il0]	=	{ i: il0, id: id, x1: x1, y1: y1, x2: x2, y2: y2, x1d: x1d, y1d: y1d, x2d: x2d, y2d: y2d, fromX: fromX, toX: toX, a: a, b: b, c: c} ;

				valveCtx.beginPath() ;
				valveCtx.moveTo( x1d, y1d) ;
				valveCtx.lineTo( x2d, y2d) ;
				valveCtx.stroke() ;

				if ( y1d < 0)
					y1d	=	0 ;
				else if ( y1d > 200) {
					y1d	=	200 ;
				}
				valveCtx.beginPath() ;
				valveCtx.arc( x1d, y1d, 5, 0, Math.PI * 2) ;
				valveCtx.stroke() ;

				if ( y2d < 0)
					y2d	=	0 ;
				else if ( y2d > 200) {
					y2d	=	200 ;
				}
				valveCtx.beginPath() ;
				valveCtx.arc( x2d, y2d, 5, 0, Math.PI * 2) ;
				valveCtx.stroke() ;
				fromX	=	toX ;
			}
			valveGraph.points[il0]	=	{ i: il0, id: id, x1: x2, y1: y2, x2: 0, y2: 0, x1d: x2d, y1d: y2d, x2d: 0, y2d: 0, fromX: fromX, toX: 0, a: 0, b: 0, c: 0} ;
		}
//		dEnd( 1, "valve.js", "valve", "eventInGraph( <_evt>)") ;
	}
	var	valveGraph	=	document.getElementById( "valveGraph") ;
	valveGraph.parent	=	this ;
	valveGraph.addEventListener( "mousedown", this.mousedown, false) ;
	valveGraph.addEventListener( "mouseup", this.mouseup, false) ;
	valveGraph.addEventListener( "mousemove", this.mousemove, false) ;
	/**
	 *
	 */
	dTrace( 2, "valve.js", "valve", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "valve.js", "valve", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageValveSurveyEntry") ;
	dEnd( 1, "valve.js", "valve", "main()") ;
}
