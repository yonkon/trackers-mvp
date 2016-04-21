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
  <div class="name">
    <span class="value"><?= $project->name ?></span>
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
    <i class="start green-text glyphicon glyphicon-play"></i>
    <i class="stop hidden red-text glyphicon glyphicon-stop"></i>
  </div>
  <div class="time today">
    <span class="value"><?= $project->todayFormatted ?></span>&nbsp;
  </div>
  <div class="time week">
    <span class="value"><?= $project->weekFormatted ?></span>&nbsp;
  </div>
  <div class="time month">
    <span class="value"><?= $project->monthFormatted ?></span>&nbsp;
  </div>
  <div class="time custom">
    <span class="value"><?= $project->customFormatted ?></span>&nbsp;
  </div>
</li>
