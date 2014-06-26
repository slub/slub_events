function checkBoxes(objThis){
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
        $('#calendar').fullCalendar('refetchEvents');
      } else {
        $('#calendar')
        .fullCalendar('removeEventSource', window[eventurl]);
        $('#calendar').fullCalendar('refetchEvents');
      };
    });

});





