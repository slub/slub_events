$(document).ready(function() {

	$('.slubevents-category input').change(function() {
		if ($(this).is(':checked')) {
			$(this).parent().find('input').attr("checked","checked");
		} else {
			$(this).parent().find('input').removeAttr("checked");
		};
		$('#calendar').fullCalendar('refetchEvents');
	});

	 $('.slubevents-discipline input').change(function() {
		if ($(this).is(':checked')) {
			$(this).parent().find('input').attr("checked","checked");
		} else {
			$(this).parent().find('input').removeAttr("checked");
		};
		$('#calendar').fullCalendar('refetchEvents');
	});

});





