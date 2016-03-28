<?php
/** 
 * @var $this SiteController 
 * @var $loginModel LoginForm
 * @var $app CApplication
 * @var $model = new RegistrationForm;
 * @var $profile=new Profile;
 **/
 $app = Yii::app();
$this->pageTitle=$app->name;
function tr($msg) { return Yii::t('general', $msg);}
?>
<? $this->renderPartial('top_links', array('app' => $app));?>

<div id="login_box">
  <h2><?= tr('Log in') ?></h2>
  <div class="form">
    <?php
    /**
     * @var $form CActiveForm
     */
    $form=$this->beginWidget('CActiveForm', array(
      'id'=>'login-form',
      'enableClientValidation'=>true,
      'action'=>$app->createUrl('/login'),
      'clientOptions'=>array(
        'validateOnSubmit'=>true,
      ),
    )); ?>

    <? if(false) { ?><p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p><? } ?>

    <div class="row">
      <?php echo $form->labelEx($loginModel,'username'); ?>
      <?php echo $form->textField($loginModel,'username'); ?>
      <?php echo $form->error($loginModel,'username'); ?>
    </div>

    <div class="row">
      <?php echo $form->labelEx($loginModel,'password'); ?>
      <?php echo $form->passwordField($loginModel,'password'); ?>
      <?php echo $form->error($loginModel,'password'); ?>
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
  <h2><?= tr('Регистрация') ?></h2>

  <?php if(Yii::app()->user->hasFlash('registration')): ?>
    <div class="success">
      <?php echo Yii::app()->user->getFlash('registration'); ?>
    </div>
  <?php else: ?>
    <div class="form">
      <?php $form=$this->beginWidget('UActiveForm', array(
        'id'=>'registration-form',
        'action' => $app->createUrl('user/registration'),
        'enableAjaxValidation'=>true,
        'disableAjaxValidationAttributes'=>array('RegistrationForm_verifyCode'),
        'clientOptions'=>array(
          'validateOnSubmit'=>true,
        ),
        'htmlOptions' => array('enctype'=>'multipart/form-data', )
      )); ?>

      <? if(false) { ?><p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p><? } ?>

      <?php echo $form->errorSummary(array($regModel,$regProfile)); ?>

      <div class="row">
        <?php echo $form->labelEx($regModel,'username'); ?>
        <?php echo $form->textField($regModel,'username'); ?>
        <?php echo $form->error($regModel,'username'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($regModel,'password'); ?>
        <?php echo $form->passwordField($regModel,'password'); ?>
        <?php echo $form->error($regModel,'password'); ?>
        <p class="hint">
          <?php echo UserModule::t("Minimal password length 4 symbols."); ?>
        </p>
      </div>

      <div class="row">
        <?php echo $form->labelEx($regModel,'verifyPassword'); ?>
        <?php echo $form->passwordField($regModel,'verifyPassword'); ?>
        <?php echo $form->error($regModel,'verifyPassword'); ?>
      </div>

      <div class="row">
        <?php echo $form->labelEx($regModel,'email'); ?>
        <?php echo $form->textField($regModel,'email'); ?>
        <?php echo $form->error($regModel,'email'); ?>
      </div>

      <?php
      $regProfileFields=$regProfile->getFields();
      if ($regProfileFields) {
        foreach($regProfileFields as $field) {
          ?>
          <div class="row">
            <?php echo $form->labelEx($regProfile,$field->varname); ?>
            <?php
            if ($widgetEdit = $field->widgetEdit($regProfile)) {
              echo $widgetEdit;
            } elseif ($field->range) {
              echo $form->dropDownList($regProfile,$field->varname,Profile::range($field->range));
            } elseif ($field->field_type=="TEXT") {
              echo$form->textArea($regProfile,$field->varname,array('rows'=>6, 'cols'=>50));
            } else {
              echo $form->textField($regProfile,$field->varname,array('size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
            }
            ?>
            <?php echo $form->error($regProfile,$field->varname); ?>
          </div>
          <?php
        }
      }
      ?>
      <?php if( false && UserModule::doCaptcha('registration')) { ?>
        <div class="row">
          <?php echo $form->labelEx($regModel,'verifyCode'); ?>

          <?php $this->widget('CCaptcha'); ?>
          <?php echo $form->textField($regModel,'verifyCode'); ?>
          <?php echo $form->error($regModel,'verifyCode'); ?>

          <p class="hint"><?php echo UserModule::t("Please enter the letters as they are shown in the image above."); ?>
            <br/><?php echo UserModule::t("Letters are not case-sensitive."); ?></p>
        </div>
      <?php } ?>

      <div class="row submit">
        <?php echo CHtml::submitButton(UserModule::t("Register")); ?>
      </div>
      <div class="forgot_password">
        <a href="<?= $app->createUrl('/user/recovery'); ?>"><?= tr("Забыли пароль?")?></a>
      </div>

      <?php $this->endWidget(); ?>
    </div><!-- form -->
  <?php endif; ?>
</div>

<script>
  $(document).ready(function(){
    $('#login_btn').click(function() {
      $.fancybox.open('#login_box', {
        'transitionIn': 'none',
        'transitionOut': 'ease',
//        'modal': true,
        'centerOnScroll': true,
        'width' : 800
      });
    });
    $('#register_btn').click(function(){
      $.fancybox.open('#register_box', {
        'transitionIn': 'none',
        'transitionOut': 'ease',
//        'modal': true,
        'centerOnScroll': true,
        'width' : 800
      });
    });
  });
</script>
