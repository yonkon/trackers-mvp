<?php
/**
 * @var $this Controller
 */
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="language" content="ru">

  <!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print">
  <!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection">
  <![endif]-->

  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css">
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css">

  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-theme.css">

  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/autocomplete/jquery.autocomplete.css">
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/jui/css/base/jquery-ui.css">
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/treeview/jquery.treeview.css">
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/yiitab/jquery.yiitab.css">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/styles.css">
  <title><?php echo CHtml::encode($this->pageTitle); ?></title>
  <script type="text/javascript" src="/js/jquery.js"></script>
  <script type="text/javascript" src="/js/jquery.yii.js"></script>
  <script type="text/javascript" src="/js/jquery.yiiactiveform.js"></script>
  <script type="text/javascript" src="/js/jquery.yiitab.js"></script>
  <script type="text/javascript" src="/js/jquery.maskedinput.js"></script>
  <script type="text/javascript" src="/js/jui/js/jquery-ui.min.js"></script>
  <script type="text/javascript" src="/js/jui/js/jquery-ui-i18n.min.js"></script>

  <link rel="stylesheet" href="/js/fancybox/source/jquery.fancybox.css" type="text/css" media="screen" />
  <script type="text/javascript" src="/js/fancybox/source/jquery.fancybox.js"></script>

  <link rel="stylesheet" href="/css/content_only.css" type="text/css" media="all" />

</head>

<body>

<div class="container" id="page">

  <div id="header">
    <div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
  </div>
  <?php if(isset($this->breadcrumbs)):?>
    <?php $this->widget('zii.widgets.CBreadcrumbs', array(
      'links'=>$this->breadcrumbs,
    )); ?><!-- breadcrumbs -->
  <?php endif?>
  <!-- header -->
  <?php echo $content; ?>
  <div class="clear"></div>

  <div id="footer">
  </div>
  <!-- footer -->

</div>
<!-- page -->

</body>
</html>
