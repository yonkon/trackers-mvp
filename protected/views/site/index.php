<?php
/** 
 * @var $this SiteController 
 * @var $loginModel LoginForm
 **/

$this->pageTitle=Yii::app()->name;
?>

<a href="javascript:void(0);" id="login_btn"><?= Yii::t('general', 'Log in') ?></a>
<a href="javascript:void(0);" id="register_btn"><?= Yii::t('general', 'Register') ?></a>

<div id="login_box">
  <div class="form">
    <?php $form=$this->beginWidget('CActiveForm', array(
      'id'=>'login-form',
      'enableClientValidation'=>true,
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
    )); ?>

    <p class="note"> Fields with <span class="required">*</span> are required.</p>

    <div class="row">
      <?php echo $form->labelEx($loginModel,'username'); ?>
      <?php echo $form->textField($loginModel,'username'); ?>
      <?php echo $form->error($loginModel,'username'); ?>
    </div>

    <div class="row">
      <?php echo $form->labelEx($loginModel,'password'); ?>
      <?php echo $form->passwordField($loginModel,'password'); ?>
      <?php echo $form->error($loginModel,'password'); ?>
      <p class="hint">
        Hint: You may login with <kbd>demo</kbd>/<kbd>demo</kbd> or <kbd>admin</kbd>/<kbd>admin</kbd>.
      </p>
    </div>

    <div class="row rememberMe">
      <?php echo $form->checkBox($loginModel,'rememberMe'); ?>
      <?php echo $form->label($loginModel,'rememberMe'); ?>
      <?php echo $form->error($loginModel,'rememberMe'); ?>
    </div>

    <div class="row buttons">
      <?php echo CHtml::submitButton('Login'); ?>
    </div>

    <?php $this->endWidget(); ?>
  </div><!-- form -->
</div>

<div id="register_box">
  
</div>

<script>
  $(document).ready(function(){
    $('#login_btn').click(function() {
      $.fancybox.open('#login_box', {
        'transitionIn': 'none',
        'transitionOut': 'ease',
//        'modal': true,
        'centerOnScroll': true,
        'width' : 600
      });
    });
    $('#register_btn').click(function(){
      $('#register_box').fancybox();
    });
  });
</script>
