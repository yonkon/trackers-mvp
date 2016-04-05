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
      <div class="p-tab active" id="time_tab">Time tracker  </div>
      <div class="p-tab" id="task_tab">       Task tracker  </div>
      <div class="p-tab" id="rss_tab">        Rss reader    </div>
      <div class="p-tab" id="notes_tab">      Notes         </div>
      <div class="p-tab" id="fav_tab">        Favourites    </div>
      <div class="p-tab" id="radio_tab">      Radio         </div>
    </div>
    <div class="p-content">
      <div class="p-item active" id="time_content"><?php echo $timeTracker ?></div>
      <div class="p-item" id="task_content">        Task tracker  </div>
      <div class="p-item" id="rss_content">         Rss reader    </div>
      <div class="p-item" id="notes_content">       Notes         </div>
      <div class="p-item" id="fav_content">         Favourites    </div>
      <div class="p-item" id="radio_content">       Radio         </div>
    </div>
    <div class="clearfix"></div>
  </div>
  <div class="p-footer">
    <div class="p-icons">
      <ul>
        <li class="p-icon"> Time tracker  </li>
        <li class="p-icon"> Task tracker  </li>
        <li class="p-icon"> Rss reader    </li>
        <li class="p-icon"> Notes         </li>
        <li class="p-icon"> Favourites    </li>
        <li class="p-icon"> Radio         </li>
      </ul>
    </div>
    <nav class="p-links">
      <a>Main</a>
      <a>FAQ</a>
      <a>Blog</a>
      <a>Contacts</a>
    </nav>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('.p-tab').click(function(){
        var $this = $(this);
        var tid = $this.attr('id');
        var cid = tid.replace('_tab', '')+'_content';
        $('.p-tab').removeClass('active');
        $this.addClass('active');
        $('.p-item').removeClass('active');
        $('#'+cid).addClass('active');
      });
  });
</script>
