<?php
/**
 * @var $app CWebApplication
 */

if(Yii::app()->user->isGuest) : ?>
  <a href="javascript:void(0);" id="login_btn"><?= Yii::t('general', 'Log in') ?></a>
  <a href="javascript:void(0);" id="register_btn"><?= Yii::t('general', 'Register') ?></a>
  <?php ; else : ?>

  <a href="<?php $app->createUrl('/panel'); ?>" ><?= tr('Перейти в панель') ?></a>
  <a href="/logout" id="logout_btn"><?= Yii::t('general', 'Log out') ?></a>
  <?php ; if(UserModule::isAdmin()) { ?>
    <a href="<?= $app->CreateUrl('rights/assignment') ?>"><?= tr('Права') ?></a>
    <a href="<?= $app->CreateUrl('user/admin') ?>"><?= tr('Пользователи') ?></a>
    <a href="<?= $app->CreateUrl('user/profilefield') ?>"><?= tr('Поля профиля') ?></a>
  <?php  } endif;
