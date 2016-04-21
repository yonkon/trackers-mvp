<?php

/**
 * This is the model class for table "time_project".
 *
 * The followings are the available columns in table 'time_project':
 * @property integer $id
 * @property string $name
 * @property integer $status
 * @property string $created
 * @property string $updated
 * @property string $position
 * @property integer $user_id
 * @property integer $cost
 * @property integer $cost_type
 * @property integer $today
 * @property string $todayFormatted
 * @property integer $week
 * @property string $weekFormatted
 * @property integer $month
 * @property string $monthFormatted
 * @property TimeItem[] $timeItems
 * @property TimeItem lastItem
 */
class TimeProject extends CActiveRecord
{

  const STATUS_STOPPED = 0;
  const STATUS_STARTED = 1;
  const STATUS_DELETED = -1;
  private $total;
  private $today;
  private $week;
  private $month;
  private $custom;

  private $totalFormatted;
  private $todayFormatted;
  private $weekFormatted;
  private $monthFormatted;
  private $customFormatted;

  public function __construct($user_id, $scenario = 'insert')
  {
    parent::__construct($scenario);
    $this->user_id = $user_id;
    $this->status = self::STATUS_STOPPED;
  }

  public static function getMaxPositionStatic($uid)
  {
    $cmd = Yii::app()->db->createCommand('SELECT MAX(`position`) as `position` FROM ' . self::tableNameStatic() .
      ' WHERE `user_id` = ' . (int)$uid );
    $res = $cmd->queryScalar();
    return intval($res);

  }

  public function getMaxPosition()
  {
    $cmd = Yii::app()->db->createCommand('SELECT MAX(`position`) FROM ' . $this->tableName() .
      ' WHERE `user_id` = ' . $this->user_id);
    $res = $cmd->queryScalar();
    return intval($res);
  }

  /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'time_project';
	}

	public  static function tableNameStatic()
	{
		return 'time_project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, user_id', 'required'),
			array('status, user_id, cost, cost_type', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('position', 'length', 'max'=>10),
			array('updated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, status, created, updated, position, user_id, cost, cost_type', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
      'timeItems' => array(self::HAS_MANY, 'TimeItem', 'time_project_id', 'condition' => 'timeItems.status != '.TimeItem::STATUS_DISCARDED)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'status' => 'Status',
			'created' => 'Created',
			'updated' => 'Updated',
			'position' => 'Position',
			'user_id' => 'User',
			'cost' => 'Cost',
			'cost_type' => 'Cost Type',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('position',$this->position,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('cost',$this->cost);
		$criteria->compare('cost_type',$this->cost_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TimeProject the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

  public function processTimeIntervals($custom_from = null, $custom_to = null)
  {
    $today = Helpers::getToday();
    $tomorrow = Helpers::getTomorrow();
    $last_week = $tomorrow - Helpers::SECONDS_IN_WEEK;
    $last_month = $tomorrow - Helpers::SECONDS_IN_MONTH;
    $total  = 0;
    $this->today = 0;
    $this->week = 0;
    $this->month = 0;
    $this->custom = 0;

    foreach($this->timeItems as $item) {
      if($item->start_int < $tomorrow) {
        $seconds = $item->getSeconds();

        if($item->start_int >= $today) {
          $this->today += $seconds;
        }

        if($item->start_int >= $last_week) {
          $this->week += $seconds;
        }

        if($item->start_int >= $last_month) {
          $this->month += $seconds;
        }

        if((empty($custom_from) || $item->start_int >= $custom_from && $item->end_int >= $custom_from) &&
          (empty($custom_to) || $item->end_int <= $custom_to && $item->start_int <= $custom_to)
        ) {
          $this->custom += $seconds;
        }
      }
      $total += $seconds;
    }
    return array(
      'today' => $this->today,
      'week' => $this->week,
      'month' => $this->month,
      'custom' => $this->custom,
      'total' => $total,
    );
  }

  /**
   * @return mixed
   */
  public function getToday($refresh = true)
  {
    if($refresh || empty($this->today)) {
      $this->processTimeIntervals();
    }
    return $this->today;
  }

  /**
   * @return mixed
   */
  public function getWeek($refresh = true)
  {
    if($refresh || empty($this->week)) {
      $this->processTimeIntervals();
    }
    return $this->week;
  }

  /**
   * @return mixed
   */
  public function getMonth($refresh = true)
  {
    if($refresh || empty($this->month)) {
      $this->processTimeIntervals();
    }
    return $this->month;
  }

  public function getCustom($from = null, $to = null, $refresh = false)
  {
    if($refresh || !isset($this->custom) || !empty($from) || !empty($to)) {
      $this->processTimeIntervals($from, $to);
    }
    return $this->custom;
  }

  public function getTotal($refresh = true)
  {
    if($refresh || empty($this->month)) {
      $this->processTimeIntervals();
    }
    return $this->total;
  }

  public function stop($upd_time = null)
  {
    $this->setIsNewRecord(false);
    $this->status=self::STATUS_STOPPED;
    $this->updated = Helpers::time2mysql_ts($upd_time);
    return $this->save();
  }

  /**
   * Returns all column attribute values.
   * Note, related objects are not returned.
   * @param mixed $names names of attributes whose value needs to be returned.
   * If this is true (default), then all attribute values will be returned, including
   * those that are not loaded from DB (null will be returned for those attributes).
   * If this is null, all attributes except those that are not loaded from DB will be returned.
   * @return array attribute values indexed by attribute names.
   */
  public function getAttributes($names = true, $intTimestapms = false)
  {
    $proj =  parent::getAttributes($names);
    if(!empty($proj) && $intTimestapms) {
      $proj['created'] = strtotime($proj['created']);
      $proj['updated'] = strtotime($proj['updated']);
    }
    return $proj;
  }

  public function start($time = null)
  {
    $this->setIsNewRecord(false);
    $this->status = self::STATUS_STARTED;
    $this->updated = Helpers::time2mysql_ts($time);
    return $this->save();
  }

  /**
   * @return string
   */
  public function getTotalFormatted()
  {
    $this->totalFormatted = Helpers::formatTime($this->getTotal());
    return $this->totalFormatted;
  }
  public function getTodayFormatted()
  {
    $this->todayFormatted = Helpers::formatTime($this->getToday());
    return $this->todayFormatted;
  }
  public function getWeekFormatted()
  {
    $this->weekFormatted = Helpers::formatTime($this->getWeek());
    return $this->weekFormatted;
  }
  public function getMonthFormatted()
  {
    $this->monthFormatted = Helpers::formatTime($this->getMonth());
    return $this->monthFormatted;
  }
  public function getCustomFormatted()
  {
    $this->customFormatted = Helpers::formatTime($this->getCustom());
    return $this->customFormatted;
  }

  /**
   * @return TimeItem
   */
  public function getLastItem()
  {
    $last_start = array('index' => -1, 'start' => 0);
    foreach($this->timeItems as $i => $item) {
      if($last_start['start'] < $item->start) {
        $last_start = array('index' => $i, 'start' => $item->start);
      }
      if(!$item->isStopped()) {
        return $item;
      }
    }
    if($last_start['index'] != -1 ) {
      return $this->timeItems[$last_start['index']];
    }
    return null;
  }

  public function disable()
  {
    $this->status = self::STATUS_DELETED;
    foreach ($this->timeItems as $item ) {
      $item->disable();
    }
    return $this->save();
  }

  public function getIsNewRecord() {
    return !isset($this->primaryKey);
  }

  public function splitItemsByDays()
  {
    foreach($this->timeItems as $item) {
      $item->splitByDays();
    }
    $this->timeItems = $this->getRelated('timeItems');
  }

  /**
   * @return TimeItem[]
   */
  public function getTimeItems2()
  {
    $allTimeItems = $this->getRelated('TimeItems');
    return $this->timeItems;
  }



}
