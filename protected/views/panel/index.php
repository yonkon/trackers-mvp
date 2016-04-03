<?php
/* @var $this PanelController */

$this->breadcrumbs=array(
	'Panel',
);
?>
<h1><?php echo $this->id . '/' . $this->action->id; ?></h1>

<div class="panel">
  <div class="p-heading">
    wrlcome user / info /
  </div>
  <div class="p-body">
    <div class="p-tabs">
      <div class="p-tab">Time tracker</div>
      <div class="p-tab">Task tracker</div>
      <div class="p-tab">Rss reader</div>
      <div class="p-tab">Notes</div>
      <div class="p-tab">Favourites</div>
      <div class="p-tab">Radio</div>
      <!--$this->widget('zii.widgets.jui.CJuiTabs',array(
      'tabs'=>array(
      'StaticTab 1'=>'Content for tab 1',
      'StaticTab 2'=>array('content'=>'Content for tab 2', 'id'=>'tab2'),
      // panel 3 contains the content rendered by a partial view
      'AjaxTab'=>array('ajax'=>$ajaxUrl),
      ),
      // additional javascript options for the tabs plugin
      'options'=>array(
      'collapsible'=>true,
      ),
      ));-->
    </div>
    <div class="ui-widget-content">
      widget content
    </div>
  </div>
  <div class="panel-footer"></div>
</div>
