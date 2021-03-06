<?php

class RegistrationController extends Controller
{
	public $defaultAction = 'registration';
	
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}
	/**
	 * Registration user
	 */
	public function actionRegistration() {
    /**
     * @var $app CWebApplication
     */
    $app = Yii::app();
            $model = new RegistrationForm;
            $profile=new Profile;
            $profile->regMode = true;
            
			// ajax validator
			if(isset($_POST['ajax']) && $_POST['ajax']==='registration-form')
			{
				echo UActiveForm::validate(array($model,$profile));
				$app->end();
			}
			
		    if ($app->user->id) {
		    	$this->redirect($app->controller->module->profileUrl);
		    } else {
		    	if(isset($_POST['RegistrationForm'])) {
					$model->attributes=$_POST['RegistrationForm'];
					$profile->attributes=((isset($_POST['Profile'])?$_POST['Profile']:array()));
            $location = $app->IpGeoBase->getLocation();
            $profile->city= $location['city'];
            $profile->ip =  $location['ip'];
					if($model->validate()&&$profile->validate())
					{
						$soucePassword = $model->password;
						$model->activkey=UserModule::encrypting(microtime().$model->password);
						$model->password=UserModule::encrypting($model->password);
						$model->verifyPassword=UserModule::encrypting($model->verifyPassword);
						$model->superuser=0;
						$model->status=(($app->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE);
						
						if ($model->save()) {
							$profile->user_id=$model->id;
							$profile->save();
							if ($app->controller->module->sendActivationMail) {
								$activation_url = $this->createAbsoluteUrl('/user/activation/activation',array("activkey" => $model->activkey, "email" => $model->email));
								UserModule::sendMail($model->email,UserModule::t("You registered from {site_name}",array('{site_name}'=>$app->name)),UserModule::t("Please activate you account go to {activation_url}",array('{activation_url}'=>$activation_url)));
							}
							
							if (($app->controller->module->loginNotActiv||($app->controller->module->activeAfterRegister&&$app->controller->module->sendActivationMail==false))&&$app->controller->module->autoLogin) {
									$identity=new UserIdentity($model->username,$soucePassword);
									$identity->authenticate();
									$app->user->login($identity,0);
									$this->redirect($app->controller->module->returnUrl);
							} else {
								if (!$app->controller->module->activeAfterRegister&&!$app->controller->module->sendActivationMail) {
									$app->user->setFlash('registration',UserModule::t("Thank you for your registration. Contact Admin to activate your account."));
								} elseif($app->controller->module->activeAfterRegister&&$app->controller->module->sendActivationMail==false) {
									$app->user->setFlash('registration',UserModule::t("Thank you for your registration. Please {{login}}.",array('{{login}}'=>CHtml::link(UserModule::t('Login'),$app->controller->module->loginUrl))));
								} elseif($app->controller->module->loginNotActiv) {
									$app->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email or login."));
								} else {
									$app->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email."));
								}
								$this->refresh();
							}
						}
					} else $profile->validate();
				}
			    $this->render('/user/registration',array('model'=>$model,'profile'=>$profile));
		    }
	}
}
