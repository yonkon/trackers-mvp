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
          $proj = $timeProject->getAttributes(null, true);
          self::jsonAsnwer(
            array(
            'html' => $html,
            'project' => $proj
            ),
            self::STATUS_OK,
            'OK'
            );
        }
        die();
      } else {
        self::jsonAsnwer(null, self::STATUS_ERROR, 'Невозможно создать проект без названия' );
      }
    } else {
      self::jsonAsnwer(null, self::STATUS_ERROR, 'Нужна авторизация' );
    }
    die();
  }

  public function actionStart(){
    $item = new TimeItem();
    $item->setAttributes($_REQUEST['timeItem']);
    $item->setAttribute('time_project_id', $_REQUEST['timeProject']['id']);
    $item->start = time();
    if($item->validate() && $item->save()) {
      $proj = $item->getAttributes(null, true);
      $this->jsonAsnwer(array('timeItem' => $proj));
    } else {
      $this->jsonAsnwer($item->getErrors(), self::STATUS_ERROR, CHtml::errorSummary($item));
    }
    die();
  }

  public function actionStop()  {
    $req_iid = intval($_REQUEST['timeItem']['id']);
    $item = TimeItem::model()->findByPk($req_iid);
    /**
     * @var TimeItem $item
     **/
    if(!empty($item)){
      $item->status = $item::STATUS_STOPPED;
      $item->end = intval($_REQUEST['timeItem']['end']);
      if($item->save()) {
        self::jsonAsnwer($item->getAttributes(null, true));
      } else {
        self::jsonAsnwer(null, self::STATUS_ERROR, CHtml::errorSummary($item));
      }
    } else {
      self::jsonAsnwer(null, self::STATUS_ERROR, 'Не удалось найти указанную запись');
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
