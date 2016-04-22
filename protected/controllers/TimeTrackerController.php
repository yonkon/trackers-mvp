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
        $pos = TimeProject::getMaxPositionStatic($app->user->id);
        $timeProject = new TimeProject($app->user->id);
        $timeProject->position = $pos+1;
        $timeProject->setAttributes($_REQUEST['timeProject'], false);
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

  public function actionDelete() {
    $app = Yii::app();
    if(!empty($_REQUEST['id'])) {
      $id = intval($_REQUEST['id']);
      $proj = TimeProject::model()->findByPk($id);

      /** @var TimeProject $proj */
      if(empty($proj)) {
        $this->jsonAsnwer(array('id'=>$id), self::STATUS_ERROR, "Невозможно найти запись" );
      } else {
        if(!$app->user->isAdmin() && $proj->user_id != $app->user->id) {
          $this->jsonAsnwer(array('id'=>$id), self::STATUS_ERROR, "У Вас нет прав на удаление этой записи" );
        } else {
          if($proj->disable()) {
            $this->jsonAsnwer(array('id'=>$id), self::STATUS_OK, "Запись успешно удалена" );
          } else {
            $this->jsonAsnwer(array('id'=>$id), self::STATUS_ERROR, "Невозможно удалить запись" );
          }
        }
      }
    }
    die();
  }

  public function actionUpdate() {
    $pid = intval($_REQUEST['id']);
    /**
     * @var $project TimeProject
     */
    $project = TimeProject::model()->findByPk( $pid );
    if(empty($project)) {
      $this->jsonAsnwer(array('id' => $pid), self::STATUS_ERROR, "Не удалось найти проект");
    } else {
      $project->setAttributes($_REQUEST['attributes'], true);
      if($project->validate() && $project->save()) {
        $projectAttr = $project->getAttributes(null, true);
        $this->jsonAsnwer(array('timeProject' => $projectAttr));
      } else {
        $this->jsonAsnwer($project->getErrors(), self::STATUS_ERROR, CHtml::errorSummary($project));
      }
    }
    die();
  }

  public function actionUpdateToday() {
    $pid = intval($_REQUEST['id']);
    $hours = intval($_REQUEST['hours']);
    $minutes = intval($_REQUEST['minutes']);
    $from = intval(strtotime($_REQUEST['from']));
    $to = empty($_REQUEST['to']) ? Helpers::getTomorrow() : strtotime($_REQUEST['to'])+Helpers::SECONDS_IN_DAY;
    /**
     * @var TimeProject $proj
     */
    $proj = TimeProject::model()->findByPk($pid);
    $today = Helpers::getToday();
    $tomorrow = Helpers::getTomorrow();
    foreach($proj->getTimeItems() as $item) {
      if($item->start_int >= $today && $item->start_int < $tomorrow) {
        $item->discard();
      }
    }
    $ni = new TimeItem();
    $ni->start = $today;
    $ni->end = $today + $hours*Helpers::SECONDS_IN_HOUR + $minutes*Helpers::SECONDS_IN_MINUTE;
    $ni->status = TimeItem::STATUS_STOPPED;
    $ni->time_project_id = $proj->id;
    if($ni->save()){
      $proj->splitItemsByDays();
      $time = $proj->processTimeIntervals($from, $to);
      $this->jsonAsnwer(
        array(
          'id' => $proj->id,
          'today' => $proj->getToday(false),
          'todayFormatted' => $proj->getTodayFormatted(false),
          'week' => $proj->getWeek(false),
          'weekFormatted' => $proj->getWeekFormatted(false),
          'month' => $proj->getMonth(false),
          'monthFormatted' => $proj->getMonthFormatted(false),
          'custom' => $proj->getCustom(false),
          'customFormatted' => $proj->getCustomFormatted(false),
          'total' => $proj->getTotal(false),
          'totalFormatted' => $proj->getTotalFormatted(false),
        )
      );

    } else {
      $this->jsonAsnwer(null, self::STATUS_ERROR, CHtml::errorSummary($ni));
    }
    die();
  }

  public function actionUpdateCustom() {
    $pid = intval($_REQUEST['id']);
    $hours = intval($_REQUEST['hours']);
    $minutes = intval($_REQUEST['minutes']);
    $from = intval(strtotime($_REQUEST['from']));
    $to = empty($_REQUEST['to']) ? Helpers::getTomorrow() : strtotime($_REQUEST['to'])+Helpers::SECONDS_IN_DAY;
    /**
     * @var TimeProject $proj
     */
    $proj = TimeProject::model()->findByPk($pid);
    $today = Helpers::getToday();
    $tomorrow = Helpers::getTomorrow();
    $minimal_start = empty($from) ? Helpers::getToday() : $from;
    foreach($proj->getTimeItems() as $item) {
      if(empty($from) && $minimal_start > $item->start_int) {
        $minimal_start = $item->start_int;
      }
    }
    foreach($proj->getTimeItems() as $item) {
      if($item->start_int >= $minimal_start && $item->start_int < $to) {
        $item->discard();
      }
    }
    $minimal_start = Helpers::getMidnight($minimal_start);
    $nDays = Helpers::dayDifference($minimal_start, $to);
    $secondsTotal = $hours*Helpers::SECONDS_IN_HOUR + $minutes*Helpers::SECONDS_IN_MINUTE;
    $secondsPerDay = min(Helpers::SECONDS_IN_DAY-1, ceil($secondsTotal / $nDays));
    $start = $minimal_start;
    for($i = 0; $i < $nDays; $i++) {
      $ni = new TimeItem();
      $ni->start = $start;
      $ni->end = $start + $secondsPerDay;
      $ni->status = TimeItem::STATUS_STOPPED;
      $ni->time_project_id = $proj->id;
      if(!$ni->save()) {
        $this->jsonAsnwer(null, self::STATUS_ERROR, CHtml::errorSummary($ni));
        break;
      }
      $start += Helpers::SECONDS_IN_DAY;
      }
      $proj->splitItemsByDays();
      $time = $proj->processTimeIntervals($from, $to);
      $this->jsonAsnwer(
        array(
          'id' => $proj->id,
          'today' => $proj->getToday(false),
          'todayFormatted' => $proj->getTodayFormatted(false),
          'week' => $proj->getWeek(false),
          'weekFormatted' => $proj->getWeekFormatted(false),
          'month' => $proj->getMonth(false),
          'monthFormatted' => $proj->getMonthFormatted(false),
          'custom' => $proj->getCustom(false),
          'customFormatted' => $proj->getCustomFormatted(false),
          'total' => $proj->getTotal(false),
          'totalFormatted' => $proj->getTotalFormatted(false),
        )
      );
    die();
  }

  public function actionStart(){
    $item = TimeItem::model()->findByAttributes(array(
      'time_project_id' => intval($_REQUEST['timeProject']['id']),
      'start' => Helpers::time2mysql_ts($_REQUEST['timeItem']['start'])
    ));
    if(empty($item)) {
      $item = new TimeItem();
    }
    $item->setAttributes($_REQUEST['timeItem'], false);
    $item->setAttribute('time_project_id', $_REQUEST['timeProject']['id']);
    if($item->start()) {
      $itemAttr = $item->getAttributes(null, true);
      $this->jsonAsnwer(array('timeItem' => $itemAttr));
    } else {
      $this->jsonAsnwer($item->getErrors(), self::STATUS_ERROR, CHtml::errorSummary($item));
    }
    die();
  }

  public function actionStop()  {
    $req_iid = intval($_REQUEST['timeItem']['id']);
    $item = array();
    if($req_iid) {
      $item = TimeItem::model()->findByPk($req_iid);
    } elseif(!empty($_REQUEST['timeProject']['id']) && !empty($_REQUEST['timeItem']['start']) ) {
      $item = TimeItem::model()->findByAttributes(array(
        'time_project_id' => intval($_REQUEST['timeProject']['id']),
        'start' => Helpers::time2mysql_ts($_REQUEST['timeItem']['start'])
      ));
    }
    /**
     * @var TimeItem $item
     **/
    if(!empty($item)) {
      $item->end = intval($_REQUEST['timeItem']['end']);
      if($item->stop()) {
        $itemAttr = $item->getAttributes(null, true);
        $this->jsonAsnwer(array('timeItem' => $itemAttr));
      } else {
        self::jsonAsnwer(null, self::STATUS_ERROR, CHtml::errorSummary($item));
      }
    } else {
      self::jsonAsnwer(null, self::STATUS_ERROR, 'Не удалось найти указанную запись');
    }
    die();
  }

  public function actionGetCustomTime()  {
    $app = Yii::app();
    $from = empty($_REQUEST['from']) ? 0 : strtotime($_REQUEST['from']);
    $to = empty($_REQUEST['to']) ? time() : strtotime($_REQUEST['to']);
    if(!empty($to)) {
      $to += Helpers::SECONDS_IN_DAY;
    }
    $uid = $app->user->id;
    $result = array();

    $timeProjects = TimeProject::model()->with('timeItems')->findAllByAttributes(
      array('user_id' => $uid),
      array(
        'condition'=>'t.status!=:status',
        'params'=>array('status'=>TimeProject::STATUS_DELETED)
      )
    );
    if(!empty($timeProjects)) {
      foreach($timeProjects as $tp) {
        /**
         * @var $tp TimeProject
         */
        $tp->splitItemsByDays();
        $tp->processTimeIntervals($from, $to);
        $result[$tp->id] = array(
          'id' => $tp->id,
          'seconds' => $tp->custom,
          'custom' => $tp->customFormatted,
        );
      }

        $this->jsonAsnwer(array('timeProjects' => $result));
    } else {
      self::jsonAsnwer(null, self::STATUS_ERROR, 'Не удалось найти активные проекты');
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
