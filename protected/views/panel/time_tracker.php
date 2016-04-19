<?php
/**
 *
 * @var PanelController $this
 * @var $app CWebApplication
 * @var $timeProjects TimeProject[]
 */
$app = Yii::app();
?>
<div class="widget-header">
  <h2>Time-tracker</h2>
  <span class="help">?</span>
  <div class="controls">
    <b>&EmptySmallSquare;</b>
    <b>&#10005;</b>
  </div>
</div>
<div class="projects">
  <ol class="pr-list">
    <li class="project header">
      <div class="time">Today</div>
      <div class="time">Week</div>
      <div class="time">Month</div>
      <div class="time calendar">
        <i class="glyphicon glyphicon-calendar"></i>Custom
        <div id="custom_date_value">
          <div>
            <label for="date_from">С</label>
            <input name="date_from" id="date_from">
            <i class="glyphicon glyphicon-remove red-text"></i>
          </div>
          <div>
            <label for="date_to">по</label>
            <input name="date_to" id="date_to">
            <i class="glyphicon glyphicon-remove red-text"></i>
          </div>
        </div>
      </div>
    </li>
    <? foreach($timeProjects as $project) : ?>
    <li class="project" data-id="<?= $project->id ?>" data-status="<?= $project->status ?>" data-item="<?= $project->lastItem ? $project->lastItem->id : '' ?>">
      <div class="position"><?= $project->position ?></div>
      <div class="name"><span class="value"><?= $project->name ?></span>
        <div class="hid-control">
          <input>
          <i class="ok glyphicon glyphicon-ok"></i>
          <i class="cancel glyphicon glyphicon-remove"></i>
        </div>
        <i class="delete right glyphicon glyphicon-remove"></i>
        <i class="edit right glyphicon glyphicon-pencil"></i>
      </div>
      <div class="controls">
        <? if(false) { ?>
        <i class="glyphicon glyphicon-time"></i>
        <i class="glyphicon glyphicon-play-circle"></i>
        <? } ?>
        <i class="start <?= $project->status==TimeProject::STATUS_STOPPED? '': 'hidden' ?> green-text glyphicon glyphicon-play"></i>
        <i class="stop <?= $project->status==TimeProject::STATUS_STARTED? '': 'hidden' ?> red-text glyphicon glyphicon-stop"></i>
      </div>
      <div class="time today" data-value="<?= $project->today ?>" >
        <?= $project->todayFormatted ?>&nbsp;
      </div>
      <div class="time week" data-value="<?= $project->week ?>">
        <?= $project->weekFormatted ?>&nbsp;
      </div>
      <div class="time month" data-value="<?= $project->month ?>">
        <?= $project->monthFormatted ?>&nbsp;
      </div>
      <div class="time custom">
        &nbsp;
      </div>
    </li>
    <? endforeach; ?>
  </ol>
  <div id="new_project_box">
    <div class="wrapper-shadow" id="new_project_loader">
    </div>
    <input id="new_project_name" name="Project[name]" placeholder="Наименование задачи">
    <i id="create_project" class="glyphicon glyphicon-ok"></i>
    <i class="glyphicon glyphicon-remove"></i>
  </div>
  <div class="project-add"><i class="glyphicon glyphicon-plus-sign"></i> Добавить</div>
</div>
<script type="text/javascript">
  $(function(){

    $.datepicker.setDefaults($.datepicker.regional['ru']);

    $('.project-add').click(function(){
      $('#new_project_box').show();
      $('#new_project_name').focus();
    });

    $('#new_project_name').keyup(function(e){
      var keyCode = e.which;
      var $parent = $(this).parent();
      if(keyCode == 13) { //ENTER
        $parent.find('#create_project').click();
        return;
      }
      if(keyCode == 27) {
        $parent.find('.glyphicon-remove').click();
        return;
      }
    });

    $('#new_project_box .glyphicon-remove').click(
      function(){$(this).parent().hide()}
    );

    $('#create_project').click(
      function() {
        var name = $('#new_project_name').val().trim();
        $.ajax({
          url: '<?= $app->createUrl('timeTracker/create') ?>',
          type : 'post',
          data : {
            'timeProject[name]' : name,
            ajax : true
          },
          beforeSend : function() {
            $('#new_project_loader').show();
          },
          success : function(res){
            data = {};
            try {
              data = JSON.parse(res);
            } catch (exc) {
              console.dir(exc);
              console.dir(res);
            }
            if(isAjaxGood(data)) {
              var $newProj = $(data.data.html);
              $newProj.appendTo($('.pr-list'));
              animatePopup(null, 'Проект успешно открыт');
              initTimeProjectButtons();
            } else {
              animateAjaxMessage(data);
            }
          },
          error : function(err){
            animatePopup(null, err.status + ' : ' + err.statusText, 'error' );
          },
          complete : function(){
            $('#new_project_loader').hide();
            $('#new_project_box').hide();
          }
        });
      }
    );

    function projectDatepicker(selector) {
      var oposite = (selector == '#date_to') ? '#date_from' : '#date_to';
      var opositeOption = (selector == '#date_to') ? 'maxDate' : 'minDate';
      $(selector).datepicker({
        changeMonth: true,
        onClose: function( selectedDate ) {
          $( oposite ).datepicker( "option", opositeOption, selectedDate );
        },
        beforeShow: function(input, inst) {
          var cal = inst.dpDiv;
          var top = $(this).offset().top + $(this).outerHeight();
          var left = $(this).offset().left - 120;
          setTimeout(function () {
            cal.css({
              'top': top,
              'left': left
            });
          }, 10);
        }
      }).datepicker('show');
    }

    $('.time.calendar>i').click(function(){
      var $this = $(this).parent();
      $this.find('#custom_date_value').slideToggle('fast');
    });
    $('#date_from').click(function(){
      projectDatepicker('#date_from');
    });

    $('#date_to').click(function(){
      projectDatepicker('#date_to');
    });

    initTimeProjectButtons();

    window.timeProjectsLoop = setInterval(function(){
      $('li.project .time').each(function(i, el){
        var $this = $(this);
        $proj = $this.parents('li');
        if($proj.data('status') == <?= TimeProject::STATUS_STARTED?>) {
          seconds = parseInt($this.data('value')) + 1;
          $this.data('value', seconds);
          $this.text(seconds2Time(seconds));
        }
      });
    }, 1000);

  });

  function initTimeProjectButtons() {
    $('.name .edit').click(function(){
      var $this = $(this);
      var $parent = $this.parent();
      $parent.find('.hid-control input').val($parent.text().trim());
      $parent.find('.hid-control').show();
      $this.hide();
      $parent.find('.hid-control input').focus();
    });

    $('.name .delete').click(function(){
      var $this = $(this);
      var $parent = $this.parent().parent();
      if(confirm('Действительно удалить проект?')) {
        $.ajax({
          url : '<?= $app->createUrl('timeTracker/delete') ?>',
          data : {
            id : $parent.data('id')
          },
          error : function(e){
            animatePopup(null, 'Невозможно удалить запись', 'error');
          },
          success : function(data){
            try {
              data = JSON.parse(data);
              if(isAjaxGood(data)) {
                $parent.remove();
              } else {
                animateAjaxMessage(data);
                console.dir(data);
              }
            } catch (exc) {
              console.dir(exc);
              console.dir(data);
            }
          }
        });
      }
    });

    $('.name .hid-control input').keyup(function(e){
      var keyCode = e.which;
      var $parent = $(this).parent();
      if(keyCode == 13) { //ENTER
        $parent.find('.ok').click();
        return;
      }
      if(keyCode == 27) { //ESCAPE
        $parent.find('.cancel').click();
        return;
      }
    });

    $('.name .value').dblclick(function(){
      var $parent = $(this).parent();
      $parent.find('.edit').click();
    });

    $('.name .ok').click(function(){
      var $this = $(this);
      var $controls = $this.parent();
      var $parent = $controls.parent();
      var newName = $controls.find('input').val().trim();
      $.ajax({
        url : '<?= $app->createUrl('timeTracker/update') ?>',
        data : {
          id : $this.parents('li').data('id'),
          attributes : {
            name : newName
          },
          success : function(res){
            data = {};
            try {
              data = JSON.parse(res);
            } catch (exc) {
              console.dir(exc);
              console.dir(res);
            }
            animateAjaxMessage(data);
            if(isAjaxGood(data)) {
              $parent.find('.value').text(newName);
            }
          },
          error : function(e) {
            animatePopup(null, 'Невозможно переименовать проект', 'error');
          }
        }
      });
      $controls.hide();
      $parent.find('.edit').show();
    });

    $('.name .cancel').click(function(){
      var $this = $(this);
      var $controls = $this.parent();
      var $parent = $controls.parent();
      $controls.hide();
      $parent.find('.edit').show();
    });



    $('#custom_date_value .glyphicon-remove').click(function(){
      $(this).parent().find('input').val('');
      $(this).parent().siblings().find('input').datepicker('option', 'maxDate' , '');
      $(this).parent().siblings().find('input').datepicker('option', 'minDate' , '');
    });

    $('li.project .start').click(function(){
      var $this = $(this);
      var time = Math.floor(Date.now()/1000);
      var status = '<?= TimeProject::STATUS_STARTED ?>';
      var $parent = $this.parents('li');
      $parent.data('item', '');
      $this.addClass('hidden');
      var $stop = $parent.find('.stop');
      $stop.removeClass('hidden');
      $stop.data('start', time);
      var project = {};
      project.id = $parent.data('id');
      $parent.data('status', status );
      $parent.data('start', time );
      project.status = status;
      var pkey = 'timeProject['+project.id+']';
      var objData = {};
      var strData = localStorage.getItem(pkey);
      if(typeof strData == 'undefined' || !strData) {
        objData = {};
      } else {
        try{
          objData = JSON.parse(strData);
        } catch(exc){
          objData = {};
        }
      }
      if(typeof objData['untracked'] == 'undefined') {
        objData['untracked'] = {};
      }
      objData['untracked'][time] = {start : time, status : <?= TimeItem::STATUS_STARTED ?>, id_time_project : project.id};
      localStorage.setItem(pkey, JSON.stringify(objData));
      $.ajax({
        url : '<?= $app->createUrl('timeTracker/start') ?>',
        data : {
          timeProject : project,
          timeItem : {start : time}
        },
        type : 'post',
        error : function(e){
          console.dir(e);
          $this.removeClass('hidden');
          $parent.find('.stop').addClass('hidden');
          $parent.data('status', <?= TimeProject::STATUS_STOPPED ?>);
        },
        success : function(e){
          data = e;
          try {
            data = JSON.parse(e);
          } catch (exc){
            data = e;
          }
          if(isAjaxGood(data)) {
//            console.dir(data);
            var item = data.data.timeItem;
            var iid = item.id;
            $parent.data('item', iid);
            var start = item.start;
            var status = item.status;
            var pid = item.time_project_id;
            var pkey = 'timeProject['+pid+']';
            var strData = localStorage.getItem(pkey);
            if(typeof strData == 'undefined' || !strData) {
              objData = {};
            } else {
              try{
                objData = JSON.parse(strData);
              } catch(exc){
                objData = {};
              }
            }
            if(typeof objData['untracked'] != 'undefined') {
              if(typeof objData['untracked'][start] != 'undefined') {
                if(objData['untracked'][start]['status'] == <?= TimeItem::STATUS_STOPPED ?>) {
                  item.status = objData['untracked'][start]['status'];
                  item.end = objData['untracked'][start]['end'];
                  $.ajax({
                    url : '<?= $app->createUrl('TimeProject/updateItem') ?>',
                    data : {
                      id : item.id,
                      attributes : {
                        status : item.status,
                        end : item.end
                      }
                    },
                    success : function(res2){
                      try {
                        data2 = JSON.parse(res2);
                      } catch (exc) {
                        data2 = {};
                      }
                      if(isAjaxGood(data2)) {
                        delete objData['untracked'][start];
                      } else {
                        animateAjaxMessage(data2);
                        console.dir(data2);
                      }
                    }
                  });
                }
              }
            }
            objData[iid] = {
              id : iid,
              start : start,
              status : typeof(item.status) == 'undefined' ? status : item.status,
              id_time_project : pid
            };
            if(typeof item.end != 'undefined' && item.end) {
              objData[iid]['end'] = item.end;
            }
            strData = JSON.stringify(objData);
            localStorage.setItem(pkey, strData);
          } else {
            animateAjaxMessage(data)
            console.dir(data);
          }
        }
      });
    });

    $('li.project .stop').click(function(){
      var time = Math.floor(Date.now()/1000);
      var $this = $(this); //stop button
      var $parent = $this.parents('li'); //project list item
      var pid = $parent.data('id'); //project ID
      var pkey = 'timeProject['+pid+']'; //localStorage project key
      var start = $this.data('start');
      $this.data('start', '');
      $this.addClass('hidden'); //swapping stop/start buttons
      $parent.find('.start').removeClass('hidden');
      var project = {}, item = {};
      item.id = $parent.data('item');
      $parent.data('item', ''); //removing item id data because of deletion
      item.end = time;
      item.start = start;
      item.status = <?= TimeItem::STATUS_STOPPED?>;
      project.id = pid;
      project.status = $parent.data('status'); //if start fired right after stop status will be TimeProject::STATUS_STARTED
      if(!item.id) { //if stop fired right after stop there will be empty id, so write end time to localStorage
        var strData = localStorage.getItem(pkey);
        if(typeof strData == 'undefined' || !strData) {
          objData = {};
        } else {
          try{
            objData = JSON.parse(strData);
          } catch(exc){
            objData = {};
          }
        }
        if(objData && typeof objData['untracked'] != 'undefined') {
          objData['untracked'][start]['end']  = item.end;
          objData['untracked'][start]['status']  = item.status;
        }
        strData = JSON.stringify(objData);
        localStorage.setItem(pkey, strData);
      }
      //if no id set we wait some time for item inserting
      setTimeout(function(){
        $.ajax({
          url : '<?= $app->createUrl('timeTracker/stop') ?>',
          data : {
            timeProject : project,
            timeItem : item
          },
          error : function(e){
            console.dir(e);
            $this.removeClass('hidden');
            $parent.find('.start').addClass('hidden');
            animatePopup(null, 'Невозможно обработать запрос', 'error');
          },
          success : function(e){
            data = e;
            try {
              data = JSON.parse(e);
            } catch (exc) {
              data = e;
            }
            if(isAjaxGood(data)) {
//            console.dir(data);
              var item = data.data.timeItem;
              var iid = item.id;
              var start = item.start;
              var status = item.status;
              var pid = item.time_project_id;
              var pkey = 'timeProject['+pid+']';
              var strData = localStorage.getItem(pkey);
              if(typeof strData == 'undefined' || !strData) {
                objData = {};
              } else {
                try{
                  objData = JSON.parse(strData);
                } catch(exc){
                  objData = {};
                }
              }
              if(objData && typeof objData['untracked'] != 'undefined') {
                delete objData['untracked'][start];
              }
              objData[iid] = {
                id : iid,
                start : start,
                status : status,
                id_time_project : pid,
                end : item.end,
                seconds : item.seconds
              };
              strData = JSON.stringify(objData);
              localStorage.setItem(pkey, strData);
            } else {
              console.dir(data);
            }
            animateAjaxMessage(data);
          }
        });
      })

    });
  }

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

</script>
