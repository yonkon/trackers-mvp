<?php



/**
 * Class SiteController
 */
class SiteController extends Controller
{
  public $layout = 'content_only';
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
    /**
     * @var $app CWebApplication
     */
    $app = Yii::app();
    $this->layout = 'content_only';

    $loginModel=new UserLogin;
    $regModel = new RegistrationForm;
    $regProfile=new Profile;
    $regProfile->regMode = true;

    if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
    {
      echo CActiveForm::validate($loginModel);
      $app->end();
    }

    // collect user input data
    if(isset($_POST['UserLogin']))
    {
      $loginModel->attributes=$_POST['LoginForm'];
      // validate user input and redirect to the previous page if valid
      if($loginModel->validate() ) {
//        $loginModel->authenticate();
        if(!$loginModel->hasErrors()) {
          $this->redirect($app->createUrl('/profile'));
        }
      }
    }

    /**
     * @var $geoip CGeoIP
     */

//    $app->IpGeoBase->UpdateDB();
    $geoip = $app->IpGeoBase->getLocation();
		$this->render('index', array(
      'loginModel' => $loginModel,
      'geoip' =>$geoip,
      'regModel' => $regModel,
      'regProfile' => $regProfile
    ));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
    /**
     * @var $app CWebApplication
     */
    $app = Yii::app();

		if($error=$app->errorHandler->error)
		{
			if($app->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
    /**
     * @var $app CWebApplication
     */
    $app = Yii::app();

		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail($app->params['adminEmail'],$subject,$model->body,$headers);
				$app->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
    /**
     * @var $app CWebApplication
     */
    $app = Yii::app();
		$model=new UserLogin();

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			$app->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login()) {
        $this->redirect($app->createUrl('/profile'));
      }
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
    /**
     * @var $app CWebApplication
     */
    $app = Yii::app();
		$app->user->logout();
		$this->redirect($app->homeUrl);
	}

  public function filters() {
    return array(
      'rights', // perform access control for CRUD operations
    );
  }

  public function accessRules()
  {
    return array(
      array('allow', // Allow superusers to access Rights
        'actions'=>array(
          'index',
          'error',
          'contact',
          'login',
        ),
      ),
//      array('deny', // Deny all users
//        'users'=>array('*'),
//      ),
    );
  }
}
