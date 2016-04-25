<?php
/**
 * @var $this TaskTrackerController
 * @var $tasks Task[]
 **/

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
<div class="tasks">
  <div class="today">

  </div>
  <div class="soon">

  </div>
</div>
<div id="task_form">
  <div class="w50">
    <div><input id="task_name" placeholder="Название задачи"></div>
    <div style="margin-top: 24px;"><textarea id="task_description" placeholder="Описание задачи"></textarea></div>
  </div>
  <div class="w50">
    <div><input id="task_choose" placeholder="Выбрать или добавить проект"></div>
    <div style="margin-top: 24px;"><input id="task_close_at" placeholder="Дата выполнения"></div>
    <div style="margin-top: 24px;"><label>Повторяющаяся задача?<input type="checkbox" id="task_repeated"></label></div>
    <div><label style="width: 100%;">Через N дней<input id="task_repeat_every" class="wauto pull-right"></label></div>
    <div>
      <div id="task_week_schedule">В следующие дни недели:
        <b>ПН</b>
        <b>ВТ</b>
        <b>СР</b>
        <b>ЧТ</b>
        <b>ПТ</b>
        <b>СБ</b>
        <b>ВС</b>
      </div>
    </div>
    <div id="task_month_schedule">В следующие дни месяца: <i id="task_month_schedule_add" class="glyphicon glyphicon-plus" title="Добавить дату"></i>
    </div>
  </div>
</div>

<? if(empty($tasks)) {
  echo 'Нет активных проектов';
} else foreach($tasks as $task) { ?>
;
<? } ?>
