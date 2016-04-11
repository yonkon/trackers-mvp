<?php

class TimeTrackerController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

  public function actionCreate(){
    $app = Yii::app();
    if(!$app->user->isGuest) {
      if(!empty($_REQUEST['timeProject']['name']) ) {
        $timeProject = new TimeProject($app->user->id);
        $timeProject->setAttributes($_REQUEST['timeProject']);
        $html = '';
        if($timeProject->validate()) {
          if($timeProject->save()) {
            $html = $this->renderPartial('newTimeProject', array('project' => $timeProject), true);
          }
        }
        if($timeProject->hasErrors()) {
          echo json_encode(array(
            'status' => 'error',
            'message' => 'Невозможно создать проект',
            'data' => array(
              'errors' => $timeProject->getErrors()
            )
          ));
        } else {
          echo json_encode(array(
              'status' => 'OK',
              'message' => 'OK',
              'data' => array(
                'html' => $html,
                'project' => $timeProject->getAttributes()
              )
            )
          );
        }
        die();
      } else {
        echo json_encode(array(
          'status' => 'error',
          'message' => 'Невозможно создать проект без названия',
        ));
      }
    } else {
      echo json_encode(array(
        'status' => 'error',
        'message' => 'Нужна авторизация'
        ));
    }
    die();
  }

  public function filters()
  {
    return array(
      'accessControl',
      'rights'
    );
  }

  public function accessRules()
  {
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
