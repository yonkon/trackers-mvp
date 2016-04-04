<?php


class PanelController extends Controller
{

  public $defaultAction = 'index';

	public function actionIndex()
	{
    /**
     * @var $app CWebApplication
     */
    $app = Yii::app();
    if(!$app->user->checkAccess('Panel.*')) {
      $this->redirect($app->createUrl('login'));
    }
		$this->render('index');
	}

  public function filters()
  {
    return array(
      'accessControl',
      'rights'
    );
  }

  public function accessRules(){
    return array(

      array('allow',
        'roles' => array('Authenticated')),

      array('deny')
    );
  }
	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
