<?php
/**
 * @var $this TaskTrackerController
 * @var $task TaskItem
 */
?>
<div class="task">
  <div class="tname"><?= $task->name ?></div>
  <div class="tdate"><?= Helpers::getLocaleDate( $task->close_date) ?></div>
  <div class="tstatus tstatus-<?= $task->status ?>"><?= $task->status_text ?></div>
  <div class="tproject"><?= $task->taskProject->name ?></div>
  <div class="tdescription"><?= $task->description?></div>
  <div class="texecute"><input type="checkbox"></div>
</div>
