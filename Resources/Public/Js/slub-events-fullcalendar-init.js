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

		// add event name to title attribute on mouseover
		eventMouseover: function(event, jsEvent, view) {
		  if (view.name !== 'agendaDay') {
				  $(jsEvent.target).attr('title', event.title);
		  }
		},

		loading: function(bool) {
			if (bool) {
				$('#loading').show();
			} else {
				$('#loading').hide();
			}
		},

	});

});
