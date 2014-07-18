$(document).ready(function() {

$('#calendar').fullCalendar({

		aspectRatio: 0.6,
		firstDay: 1,
		weekNumbers: true,
		weekMode: 'liquid',
		header: {
		  left:   'title',
		  center: 'month,agendaWeek,agendaDay',
		  right:  'today  prev,next'
		},
		theme: true,
		minTime: '07:00:00',
		maxTime: '22:00:00',
		defaultEventMinutes: 60,

	});

});
