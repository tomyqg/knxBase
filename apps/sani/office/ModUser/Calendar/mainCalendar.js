/**
 *
 */
var	screen	=	new wapScreen( "Calendar", "tabPageCalendarSurveyEntry") ;
	screen.package	=	"ModUser" ;
	screen.module	=	"Calendar" ;
	screen.coreObject	=	"Calendar" ;

$( "#modUser_mainCalendar").fullCalendar({
	header: {
		left:	'prev,next today myCustomButton',
		center:	'title',
		right:	'month,agendaWeek,agendaDay'
	},
	editable:	true,
	weekNumbers:	true,
	selectable:	true,
	selectHelper:	true,
	select:	function( start, end) {
		var title	=	prompt( 'Event Title:') ;
		var eventData ;
		if ( title) {
			eventData = {
				title:	title,
				start:	start,
				end:	end
			};
			$( '#calendar').fullCalendar( 'renderEvent', eventData, true) ;	// stick? = true
		}
		$( '#calendar').fullCalendar( 'unselect') ;
	},
	events: function(start, end, timezone, callback) {
		$.ajax({
			url: 'dispatchXML.php',
			dataType: 'xml',
			data:	{
					// our hypothetical feed requires UNIX timestamps
					start:	start.unix(),
					end:	end.unix()
			},
			success: function(doc) {
				var events = [];
				$(doc).find('event').each(function() {
					events.push({
						title: $(this).attr('title'),
						start: $(this).attr('start') // will be parsed
					});
				});
				callback(events);
			}
		});
	}
}) ;

var	date = new Date();
var	d = date.getDate();
var	m = date.getMonth();
var	y = date.getFullYear();
var	myevent	=	{title: 'All Day Event',start: new Date(y, m, d)};

$( "#modUser_mainCalendar").fullCalendar( 'renderEvent', myevent, true);
