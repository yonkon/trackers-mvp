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
    <li class="project" data-id="<?= $project->id ?>" data-status="<?= $project->status ?>">
      <div class="position"><?= $project->position ?></div>
      <div class="name"><span class="value"><?= $project->name ?></span>
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
        <?= $project->today ?>&nbsp;
      </div>
      <div class="time week">
        <?= $project->week ?>&nbsp;
      </div>
      <div class="time month">
        <?= $project->month ?>&nbsp;
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

    $.datepicker.setDefaults($.datepicker.regional['ru'])

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

    $('.name .edit').click(function(){
      var $this = $(this);
      var $parent = $this.parent();
      $parent.find('.hid-control input').val($parent.text().trim());
      $parent.find('.hid-control').show();
      $this.hide();
      $parent.find('.hid-control input').focus();
    });

    $('.name .hid-control input').keyup(function(e){
      var keyCode = e.which;
      var $parent = $(this).parent();
      if(keyCode == 13) { //ENTER
        $parent.find('.ok').click();
        return;
      }
      if(keyCode == 27) {
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
      $parent.find('.value').text($controls.find('input').val().trim());
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

    $('#custom_date_value .glyphicon-remove').click(function(){
      $(this).parent().find('input').val('');
      $(this).parent().siblings().find('input').datepicker('option', 'maxDate' , '');
      $(this).parent().siblings().find('input').datepicker('option', 'minDate' , '');
    });



  });
</script>
