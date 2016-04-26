<?php
/**
 * @var $this TaskTrackerController
 * @var $tasks TaskItem[]
 * @var $app CWebApplication
 **/
$app = Yii::app();
$this->breadcrumbs=array(
	'Task Tracker'=>array('/taskTracker')
);
?>
<link rel="stylesheet" href="/css/task_tracker.css">
<div class="widget-header">
  <h2>Task-Tracker</h2>
  <span class="help">?</span>
  <div class="controls">
    <b>&EmptySmallSquare;</b>
    <b>&#10005;</b>
  </div>
</div>
<div id="new_task">
  <i class="glyphicon glyphicon-plus"></i>Открыть таск
</div>
<div id="tasks">
  <?
  //EXPERIMENTAL can add class="table" for display as table
  ?>
  <?
  /**
   * @var $task TaskItem
   */
  if(empty($tasks)) {
    echo 'Нет активных проектов';
  } else {
    $today = array();
    $soon = array();
    foreach($tasks as $task) {
      if ($task->close_date_int < Helpers::getTomorrow()) {
        $today[] = $task;
      } else {
        $soon[] = $task;
      }
    }
    ?>

    <div class="today <?= empty($today) ? 'hidden' : ''; ?>">
      <div class="task theader">
          <div class="tname">Сегодня:</div>
          <div class="tdate">Дата выполнения:</div>
          <div class="tstatus">Статус:</div>
          <div class="tproject">Проект:</div>
          <div class="tdescription">Описание:</div>
          <div class="texecute">Выполнить:</div>
      </div>
      <?
      foreach($today as $task) {
        $this->renderPartial('taskItem', array('task' => $task));
        } ?>
    </div>
    <div class="soon <?= empty($soon) ? 'hidden' : ''; ?>">
      <div class="task theader">
        <div class="tname">Сегодня:</div>
        <div class="tdate">Дата выполнения:</div>
        <div class="tstatus">Статус:</div>
        <div class="tproject">Проект:</div>
        <div class="tdescription">Описание:</div>
        <div class="texecute">Выполнить:</div>
      </div>
      <? foreach($soon as $task) {
        $this->renderPartial('taskItem', array('task' => $task));
      } ?>
    </div>
<?  } ?>
</div>
<div id="task_form">
  <input type="hidden" id="task_id">
  <div class="w50">
    <div><input id="task_name" placeholder="Название задачи"></div>
    <div style="margin-top: 24px;"><textarea id="task_description" placeholder="Описание задачи"></textarea></div>
  </div>
  <div class="w50">
    <div><input id="task_choose" placeholder="Выбрать или добавить проект"></div>
    <div style="margin-top: 24px;"><input id="task_close_date" placeholder="Дата выполнения"></div>
    <div style="margin-top: 24px;"><label for="task_repeated">Повторяющаяся задача?</label><input type="checkbox" id="task_repeated"></div>
    <div><label for="task_repeat_every">Через N дней</label><input id="task_repeat_every" class="wauto pull-right"></div>
    <div>
      <div id="task_week_schedule"><label>В следующие дни недели:</label>
        <div id="task_week_schedule_values" class="pull-right">
          <b>ПН</b>
          <b>ВТ</b>
          <b>СР</b>
          <b>ЧТ</b>
          <b>ПТ</b>
          <b>СБ</b>
          <b>ВС</b>
          <? if(false) { ?>
            <span class="justify-hack" style="
    width: 100%;
    display: inline-block;
    line-height: 0;
    height: 0;
">&nbsp;</span>
          <? } ?>

        </div>
      </div>
    </div>
    <div id="task_month_schedule"><label>В следующие дни месяца:</label> <i id="task_month_schedule_add" class="glyphicon glyphicon-plus" title="Добавить дату"></i>
    </div>
  </div>
  <div class="controls centered-content">
    <button type="button" class="submit">OK</button>
    <button type="button" class="cancel">Отмена</button>
  </div>

</div>


<script type="text/javascript">
  $(function() {
    $.datepicker.setDefaults($.datepicker.regional['ru']);
    $('#task_form').find('.submit').click(function () {
      var $this = $(this),
        $form = $this.parents('#task_form'),
        $tid = $('#task_id'),
        tid = $.trim($tid.val()),
        is_new = tid.length ? false : true,
        url = is_new ?
          '<?= $app->createUrl('taskTracker/create') ?>' :
          '<?= $app->createUrl('taskTracker/update') ?>' ,
        $name = $('#task_name'),
        $desc = $('#task_description'),
        $proj = $('#task_choose'),
        $end = $('#task_close_date'),
        $repeated = $('#task_repeated'),
        $repeat_every = $('#task_repeat_every'),
        $week_schedule = $('#task_week_schedule'),
        $month_schedule = $('#task_month_schedule');
      $.ajax({
        url : url,
        data : {
          id : tid,
          name : $name.val(),
          description : $desc.val(),
          close_date : $end.val(),
          task_project_id : $proj.data('id'),
          task_project_name : $proj.val(),
          repeated : $repeated.is(':checked'),
          repeat_every : $repeat_every.val(),
          week_schedule : $week_schedule.data('value'),
          month_schedule : getMonthScheduleArray()
        },
        error : function (res) {
          animatePopup(null, 'Невозможно обработать запрос', 'error');
        },
        success : function(res){
          data = {};
          try {
            data = JSON.parse(res);
            if(isAjaxGood()) {
              createNewTask(data.data);
            }
            animateAjaxMessage(data);
          } catch (exc) {
            console.dir(exc);
            console.dir(res);
          }
        }
      });
      $tid.val('');
      $('#task_form').hide();
      $('#tasks').show();
    });
    $('#task_form').find('.cancel').click(function () {
      $('#task_id').val('');
      $('#task_form').hide();
      $('#tasks').show();
    });
    $('#new_task').click(function () {
      $('#task_id').val('');
      $('#task_form').show();
      $('#tasks').hide();
    });

    $('#task_close_date').datepicker({
      changeMonth: true,
      beforeShow: function(input, inst) {
        var cal = inst.dpDiv;
        var top = $(this).offset().top + $(this).outerHeight();
        var left = $(this).offset().left - 120;
        setTimeout(function () {
          cal.css({
            'top': top,
//              'left': left
          });
        }, 10);
      }
    });
    $('#task_close_date').click(function(){
      var $this = $(this);
      $this.datepicker('show');
    });

    var createNewTask = function(data) {
      var proj = data.project;
      var task = data.task;

    };

    var getMonthScheduleArray = function(){
      var $month_schedule = $('#month_schedule'),
        result = {};
      $month_schedule.find('.day').each(function(){
        var $this = $(this),
          val = $.trim($this.val());
        try {
          val = parseInt(val);
          if(val>31 || val < 1) {
            return;
          }
        } catch (exc) {
          return;
        }
        if(val.length) {
          result[val] = val;
        }
      });
      return result;
    }
  });
</script>
