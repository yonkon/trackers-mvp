<?php

/**
 * This is the model class for table "time_item".
 *
 * The followings are the available columns in table 'time_item':
 * @property integer $id
 * @property integer $time_project_id
 * @property integer $status
 * @property string $start
 * @property integer $start_int
 * @property integer $end_int
 * @property integer $seconds
 * @property TimeProject $timeProject
 */
class TimeItem extends CActiveRecord
{

  const STATUS_STARTED = 1;
  const STATUS_STOPPED = 0;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'time_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('time_project_id', 'required'),
			array('time_project_id, status, seconds', 'numerical', 'integerOnly'=>true),
			array('end', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, time_project_id, status, start, end, seconds', 'safe', 'on'=>'search'),
		);
	}

  public function getAttributes($names = null, $intTimestamps = false) {
    $proj = parent::getAttributes($names);
    if(!empty($proj) && $intTimestamps) {
      $proj['end'] = strtotime($proj['end']);
      $proj['start'] = strtotime($proj['start']);
    }
    return $proj;
  }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
      'timeProject'=>array(self::BELONGS_TO, 'TimeProject', 'time_project_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'time_project_id' => 'Time Project',
			'status' => 'Status',
			'start' => 'Start',
			'end' => 'End',
			'seconds' => 'Seconds',
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
		$criteria->compare('time_project_id',$this->time_project_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('start',$this->start,true);
		$criteria->compare('end',$this->end,true);
		$criteria->compare('seconds',$this->seconds);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


  public function save($runValidation=true,$attributes=null) {
    $start = is_numeric($this->start) ? intval($this->start) : strtotime($this->start);
    $end = is_numeric($this->end) ? intval($this->end) : strtotime($this->end);
    if(!empty($end)) {
      $seconds = $end - $start;
      $this->seconds = $seconds;
    }

    if(is_numeric($this->start)) {
      $this->start  = Helpers::time2mysql_ts($this->start);
    }
    if(is_numeric($this->end)) {
      $this->end  = Helpers::time2mysql_ts($this->end);
    }
    return parent::save($runValidation,$attributes);
  }

  public function afterSave2() {
    if($this->status == self::STATUS_STOPPED) {
      $this->timeProject->stop($this->end);
    } elseif ($this->status == self::STATUS_STARTED) {
      $this->timeProject->start($this->start);
    }
  }


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TimeItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

  public function start()
  {
    $this->status = self::STATUS_STARTED;
    if($this->save()) {
      return $this->timeProject->start($this->start);
    }
    return false;
  }

  public function stop()
  {
    if(empty($this->end)) {
      $this->end = time();
    }
    $this->status = self::STATUS_STOPPED;
    if($this->save()) {
      return $this->timeProject->stop();
    }
    return false;
  }

  /**
   * @return int
   */
  public function getStart_int() {
    return is_numeric($this->start)? intval($this->start) : strtotime($this->start);
  }

  /**
   * @return int
   */
  public function getEnd_int() {
    return is_numeric($this->end)? intval($this->end) : strtotime($this->end);
  }

  public function isStopped()
  {
    return ($this->status == self::STATUS_STOPPED);
  }

  /**
   * @return int
   */
  public function getSeconds()
  {
    return $this->isStopped() ? $this->seconds : (time() - $this->start_int);
  }
}
