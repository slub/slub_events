function checkBoxes(objThis) {
  // Checkbox selected? (true/false)
  var blnChecked = objThis.checked;
  // parent node
  var objHelp = objThis.parentNode;

  while(objHelp.nodeName.toUpperCase() != "LI"){
    // next parent node
    objHelp = objHelp.parentNode;
  }

  var arrInput = objHelp.getElementsByTagName("input");
  var intLen = arrInput.length;

  for(var i=0; i<intLen; i++){
    // select/unselect Checkbox
    if(arrInput[i].type == "checkbox"){
      arrInput[i].checked = blnChecked;
    }
  }
}

$(document).ready(function() {

    $('.slubevents-category input').change(function() {
	  var cal = $(this).attr('id').split("-")[2];
	  var eventurl = 'eventcat' + cal;
      if($(this).is(":checked")) {
        $('#calendar')
        .fullCalendar('addEventSource', window[eventurl]);
      } else {
        $('#calendar')
        .fullCalendar('removeEventSource', window[eventurl]);
      };
    });

    $(function() {
		$('.slubevents-category input:checked').each(function() {
			var cal = $(this).attr('id').split("-")[2];
			var eventurl = 'eventcat' + cal;
			$('#calendar')
				.fullCalendar('addEventSource', window[eventurl]);
     });
    });

  $('#calendar').fullCalendar({

	monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
	monthNamesShort: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
	dayNames: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
	dayNamesShort: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
	buttonText: {
	  prev: "<span class='fc-text-arrow'>&lsaquo;</span>",
	  next: "<span class='fc-text-arrow'>&rsaquo;</span>",
	  prevYear: "<span class='fc-text-arrow'>&laquo;</span>",
	  nextYear: "<span class='fc-text-arrow'>&raquo;</span>",
	  today: 'heute',
	  month: 'Monat',
	  week: 'Woche',
	  day: 'Tag'
	},
	allDayText: 'ganztags',
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
    titleFormat: {
      month: 'MMMM yyyy',
      week: "d. [ MMM] [ yyyy]{ - d. MMM yyyy}",
      day: 'dddd, d.M.yyyy'
    },
    columnFormat: {
      month: 'ddd',
      week: 'ddd d.M.',
      day: 'dddd d.M.yyyy'
    },
    timeFormat: { // for event elements
      // for agendaWeek and agendaDay
      agenda: 'H:mm{ - H:mm}', // 5:00 - 6:30
      '': 'H:mm',
    },
    firstHour: 9,
    minTime: 7,
    maxTime: 22,
    defaultEventMinutes: 60,
    axisFormat: 'H:mm',

    // add event name to title attribute on mouseover
    eventMouseover: function(event, jsEvent, view) {
      if (view.name !== 'agendaDay') {
              $(jsEvent.target).attr('title', event.title);
      }
    },

    loading: function(bool) {
	  if (bool) {
		$('#loading').show();
	  }
      else {
		$('#loading').hide();
		}
    },

    });
  });

