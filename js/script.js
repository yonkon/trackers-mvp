var seconds2Time = function (seconds){
  var
    minutes = 0,
    hours = 0,
    days = 0,
    weeks = 0,
    months = 0,
    years = 0;
  while(seconds >= seconds2Time.year) {
    years++;
    seconds -= seconds2Time.year;
  }
  while(seconds >= seconds2Time.month) {
    months++;
    seconds -= seconds2Time.month;
  }
  while(seconds >= seconds2Time.week) {
    weeks++;
    seconds -= seconds2Time.week;
  }
  while(seconds >= seconds2Time.day) {
    days++;
    seconds -= seconds2Time.day;
  }
  while(seconds >= seconds2Time.hour) {
    hours++;
    seconds -= seconds2Time.hour;
  }
  while(seconds >= seconds2Time.minute) {
    minutes++;
    seconds -= seconds2Time.minute;
  }
  if(years>0) {
    return years + ' г. ' + months + ' мес.';
  }
  if(months>0) {
    return months + ' мес.' + weeks + ' нед.';
  }
  if(weeks>0) {
    return weeks + ' нед. ' + days + ' дн.';
  }
  if(days>0) {
    return days + ' дн. ' + hours + ' ч.';
  }
  if(hours>0) {
    return hours + ' ч. ' + minutes + ' мин.';
  }
  if(minutes>0) {
    return minutes + ' мин. ' + seconds + ' сек.';
  }
  return seconds + ' сек.'
};

seconds2Time.minute = 60;
seconds2Time.hour = 3600;
seconds2Time.day = 86400;
seconds2Time.week = 604800;
seconds2Time.month = 2592000;
seconds2Time.year = 31557600;

function createPopup(message, type) {
    if('undefined' == typeof type) {
        type = 'info';
    }
    var $flashpopup = $('#flash_popup');
    if($flashpopup.length) {
        $flashpopup.removeClass();
        $flashpopup.addClass(type);
        $flashpopup.html(message);
    } else {
        $flashpopup = $('<div id="flash_popup" class="' + type + '">'+message+"</div>");
        $flashpopup.appendTo(document.body);
    }
    return $flashpopup;
}

function animatePopup($element, message, type) {
    if(!$element || typeof($element) == 'undefined' || !$element.length) {
        $element = createPopup(message, type);
    }
    $element.fadeIn().animate({opacity: 1.0}, 3000).fadeOut("slow");
}

function animateAjaxMessage(data) {
    if(hasAjaxMessage(data)) {
        var type = 'error';
        if(typeof data.status != 'undefined') {
            type = data.status;
        }
        animatePopup(null, data.message, type);
    }
}

function isAjaxGood(parsed) {
    return (parsed && typeof (parsed.status) != 'undefined' && parsed.status == "OK");
}

function hasAjaxMessage(parsed) {
    return (parsed && typeof (parsed.message) != 'undefined' && parsed.message.length);
}
