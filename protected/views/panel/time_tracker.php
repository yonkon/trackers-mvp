<?php
/**
 *
 * @var PanelController $this
 */
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
      <div class="time">today</div>
      <div class="time">week</div>
      <div class="time">month</div>
      <div class="time">custom</div>
    </li>
    <? foreach($projects as $project) : ?>
    <li class="project" data-id="{id}" data-status="{status}">
      <div class="position">{position}</div>
      <div class="name">{name}
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
        {today}
      </div>
      <div class="time week">
        {week}
      </div>
      <div class="time month">
        {month}
      </div>
      <div class="time custom">
        &nbsp;
      </div>
    </li>
    <? endforeach; ?>
  </ol>
  <div class="project-add"><i class="glyphicon glyphicon-plus-sign"></i> Добавить</div>
</div>
