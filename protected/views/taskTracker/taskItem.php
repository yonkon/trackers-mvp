<?php
/**
 * @var $this TaskTrackerController
 * @var $task TaskItem
 */
?>
<div class="task"
     data-id="<?= $task->id ?>"
     data-repeated="<?= $task->repeated ?>"
     data-every="<?= $task->repeat_every ?>"
     data-week="<?= $task->week_schedule ?>"
     data-month="<?= $task->getMonthScheduleDaysString() ?>">
  <div class="tname"><?= $task->name ?></div>
  <div class="tedit"><i class="glyphicon glyphicon-pencil"></i> </div>
  <div class="tdate" data-value="<?= Helpers::formatTime($task->close_date_int, 'd.m.Y')?>"><?= Helpers::getLocaleDate( $task->close_date) ?></div>
  <div class="tstatus tstatus-<?= $task->status ?>"><?= $task->status_text ?></div>
  <div class="tproject"><?= $task->taskProject->name ?></div>
  <div class="tdescription"><?= $task->description?></div>
  <div class="texecute"><input type="checkbox"></div>
</div>
