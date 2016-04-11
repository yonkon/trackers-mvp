<?php
/**
 *
 * @var $app CWebApplication
 * @var TimeTrackerController $this
 * @var TimeProject $project
 */
$app = Yii::app();
?>
<li class="project" data-id="<?= $project->id ?>" data-status="<?= $project->status ?>">
  <div class="position"><?= $project->position ?></div>
  <div class="name"><?= $project->name ?>
    <div class="hid-control">
      <input>
      <i class="ok glyphicon glyphicon-ok"></i>
      <i class="cancel glyphicon glyphicon-remove"></i>
    </div>
    <i class="edit right glyphicon glyphicon-pencil"></i>
  </div>
  <div class="controls">
    <? if(false) { ?>
      <i class="glyphicon glyphicon-time"></i>
      <i class="glyphicon glyphicon-play-circle"></i>
    <? } ?>
    <i class="start green-text glyphicon glyphicon-play"></i>
    <i class="stop red-text glyphicon glyphicon-stop"></i>
  </div>
  <div class="time today">
    <?= $project->today ?>
  </div>
  <div class="time week">
    <?= $project->week ?>
  </div>
  <div class="time month">
    <?= $project->month ?>
  </div>
  <div class="time custom">
    &nbsp;
  </div>
</li>
