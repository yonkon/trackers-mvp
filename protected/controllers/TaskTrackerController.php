<?php

class TaskTrackerController extends Controller
{
	public function actionAjax()
	{
    $app = Yii::app();
    $uid = $app->user->id;
    $tasks = TaskItem::model()->findAll("user_id = {$uid}");
		$html = $this->renderPartial('ajax', array('tasks' => $tasks), true);
    $this->jsonAsnwer(array('html' => $html));
    die();
	}

	public function actionIndex()
	{
    /**
     * @var $app CWebApplication
     */
    $app = Yii::app();
    if($app->user->isGuest) {
      $this->redirect($app->createUrl('login'));
    }
    $uid = $app->user->id;
    $tasks = TaskItem::model()->findAll("user_id = {$uid}");
    $this->render('ajax', array('tasks' => $tasks));
	}

  /**
   * @var $task TaskItem
   */
  public function actionExecute() {
    /**
     * @var $app CWebApplication
     * @var $task TaskItem
     */
    $app = Yii::app();
    if($app->user->isGuest) {
      $this->redirect($app->createUrl('login'));
    }
    $uid = $app->user->id;
    if(empty($_REQUEST['id'])) {
      $this->jsonAsnwer(null, self::STATUS_ERROR, 'Не указан таск');
      die();
    }
    $task = TaskItem::model()->findByPk(intval($_REQUEST['id']));
    if(empty($task)) {
      $this->jsonAsnwer(null, self::STATUS_ERROR, 'Не найден таск');
      die();
    }
    if(empty($_REQUEST['execute'])) {
      if($task->unexecute()) {
        $this->jsonAsnwer(array(
          'task' => $task,
          'html' => $this->renderPartial('taskItem', array('task' => $task), true))
          , self::STATUS_OK, 'Проект успешно выполнен');
      } else {
        $this->jsonAsnwer(array('task' => $task->getAttributes()), self::STATUS_ERROR, CHtml::errorSummary($task));
      }
    } else {
      if($task->execute()) {
        $this->jsonAsnwer(array('task' => $task->getAttributes(),'html' => $this->renderPartial('taskItem', array('task' => $task), true)), self::STATUS_OK, 'Проект успешно выполнен');
      } else {
        $this->jsonAsnwer(array('task' => $task->getAttributes()), self::STATUS_ERROR, CHtml::errorSummary($task));
      }
    }
    die();
  }

  public function actionCreate()
	{/**
   * @var $app CWebApplication
   */
    $app = Yii::app();
    if($app->user->isGuest) {
      $this->redirect($app->createUrl('login'));
    }
    $tpid = intval($_REQUEST['task_project_id']);
    $tpname = empty($_REQUEST['task_project_name']) ? $_REQUEST['name'] : $_REQUEST['task_project_name'];
    $uid = $app->user->id;
    $proj = null;
    $errors = array();
    $monthSc = $_REQUEST['month_schedule'];
    if (!empty($tpid)) {
      $proj = TaskProject::model()->findByPk($tpid);
    }
    if (!empty($tpname)) {
      if (empty($proj)) {
        $proj = TaskProject::model()->findByAttributes(array('name' => $tpname, 'user_id' => $uid));
      }
      if (empty($proj)) {
        $proj = new TaskProject();
        $proj->user_id = $uid;
        $proj->name = $tpname;
        $proj->status = TaskProject::STATUS_ACTIVE;
        if (!$proj->save()) {
          $this->jsonAsnwer(array('project' => $proj->getAttributes()), self::STATUS_ERROR, CHtml::errorSummary($proj));
          die();
        }
      }
    }
    $task = new TaskItem();
    $task->user_id = $uid;
    $task->status = TaskItem::STATUS_NEW;
    $task->status_text = TaskItem::STATUS_TEXT_NEW;
    $task->setAttributes($_REQUEST);
    $task->task_project_id = $proj->id;
    if(!$task->validate() || !$task->save()) {
      $this->jsonAsnwer(array('project' => $proj->getAttributes(), 'task' => $task->getAttributes()), self::STATUS_ERROR, CHtml::errorSummary($task) );
      die();
    }
    if(!empty($monthSc)) {
      foreach($monthSc as $day) {
        $mc = new TaskMonthSchedule();
        $mc->task_item_id = $task->id;
        $mc->day = intval($day);
        $mc->status = TaskMonthSchedule::STATUS_ACTIVE;
        if(!$mc->validate() || !$mc->save()) {
          $errors[0] = 'Не удалось сохранить некоторые дни повторения для таска';
          $errors[] = CHtml::errorSummary($mc);
        }
      }
    }
    if(empty($errors)) {
      $this->jsonAsnwer(array('task'=>$task->getAttributes(),
        'project' => $proj->getAttributes(),
        'html' => $this->renderPartial('taskItem', array('task' => $task), true)),
        self::STATUS_OK, 'Таск успешно открыт');
    } else {
      $this->jsonAsnwer(array('task'=>$task->getAttributes(),
        'project' => $proj->getAttributes(),
        'errors'=>$errors,
        'html' => $this->renderPartial('taskItem', array('task' => $task), true)),
        self::STATUS_OK, join('<br>', $errors));
    }
    die();
	}

  public function actionGetTaskProjectsArray()
  {
    /**
     * @var $app CWebApplication
     * @var $pr TaskProject
     */
    $app = Yii::app();
    if ($app->user->isGuest) {
      $this->redirect($app->createUrl('login'));
    }
    $projects = TaskProject::model()->findAllByAttributes(array('user_id' => $app->user->id ) );
    $projectsArray = array();
    if(!empty($projects)) {
      foreach($projects as $pr) {
        $projectsArray[] = $pr->name;
      }
    }
    echo json_encode($projectsArray);
    die();
  }

  public function actionUpdate()
  {/**
   * @var $app CWebApplication
   */
    $app = Yii::app();
    if($app->user->isGuest) {
      $this->redirect($app->createUrl('login'));
    }
    $tpid = intval($_REQUEST['task_project_id']);
    $tpname = empty($_REQUEST['task_project_name']) ? $_REQUEST['name'] : $_REQUEST['task_project_name'];
    $uid = $app->user->id;
    $proj = null;
    $errors = array();
    $monthSc = $_REQUEST['month_schedule'];
    if (!empty($tpid)) {
      $proj = TaskProject::model()->findByPk($tpid);
    }
    if (!empty($tpname)) {
      if (empty($proj)) {
        $proj = TaskProject::model()->findByAttributes(array('name' => $tpname, 'user_id' => $uid));
      }
      if (empty($proj)) {
        $proj = new TaskProject();
      }
//      $proj->user_id = $uid;
      $proj->name = $tpname;
//      $proj->status = TaskProject::STATUS_ACTIVE;
      if (!$proj->save()) {
        $this->jsonAsnwer(array('project' => $proj->getAttributes()), self::STATUS_ERROR, CHtml::errorSummary($proj));
        die();
      }
    }
    $tid = intval($_REQUEST['id']);
    /**
     * @var $task TaskItem
     */
    $task = TaskItem::model()->findByPk($tid);
    if(empty($task)) {
      $this->jsonAsnwer(null, self::STATUS_ERROR, 'Невозможно найти задачу');
      die();
    }

    $task->setAttributes($_REQUEST);
    if(!$task->validate() || !$task->save()) {
      $this->jsonAsnwer(array('project' => $proj->getAttributes(), 'task' => $task->getAttributes()), self::STATUS_ERROR, CHtml::errorSummary($task) );
      die();
    }
    foreach($task->monthSchedule as $day) {
      $monthScInd = array_search($day->day, $monthSc);
      if(!$monthScInd) {
        $day->delete();
      } else {
        unset($monthSc[$monthScInd]);
      }
    }
    if(!empty($monthSc)) {
      foreach($monthSc as $day) {
        $mc = new TaskMonthSchedule();
        $mc->task_item_id = $task->id;
        $mc->day = intval($day);
        $mc->status = TaskMonthSchedule::STATUS_ACTIVE;
        if(!$mc->validate() || !$mc->save()) {
          $errors[0] = 'Не удалось сохранить некоторые дни повторения для таска';
          $errors[] = CHtml::errorSummary($mc);
        }
      }
    }
    if(empty($errors)) {
      $this->jsonAsnwer(array(
          'task'=>$task->getAttributes(),
          'project' => $proj->getAttributes(),
          'html' => $this->renderPartial('taskItem', array('task' => $task), true)),
        self::STATUS_OK, 'Таск успешно обновлён');
    } else {
      $this->jsonAsnwer(array('task'=>$task, 'project' => $proj, 'errors'=>$errors), self::STATUS_ERROR, join('<br>', $errors));
    }
    die();
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
