<?php
/**
 *
 * @var PanelController $this
 * @var $app CWebApplication
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
      <div class="time">today</div>
      <div class="time">week</div>
      <div class="time">month</div>
      <div class="time">custom</div>
    </li>
    <? foreach($timeProjects as $project) : ?>
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
    $('.project-add').click(function(){
      $('#new_project_box').show();
      $('#new_project_name').focus();
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
          }
        })
          .success(function(data){
            alert('ok');
          })
          .error(function(err){
            alert(err.status + ' : ' + err.statusText );
          })
          .complete(function(){
            $('#new_project_loader').hide();
            $('#new_project_box').hide();
          });
      }
    );
  });
</script>
