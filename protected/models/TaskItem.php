<?php

/**
 * This is the model class for table "task_item".
 *
 * The followings are the available columns in table 'task_item':
 * @property integer $id
 * @property integer $task_project_id
 * @property string $name
 * @property string $description
 * @property integer $status
 * @property string $created
 * @property integer $user_id
 * @property string $close_date
 * @property string $last_close
 * @property integer $repeat_every
 * @property integer $week_schedule
 * @property string $status_text
 * @property TaskProject taskProject
 * @property integer close_date_int
 * @property TaskMonthSchedule[] monthSchedule
 * @property integer repeated Flag
 */
class TaskItem extends CActiveRecord
{
  const STATUS_NEW = 1;
  const STATUS_TEXT_NEW = 'Новый';
  const STATUS_CLOSED = 0;
  const STATUS_EXECUTED = 2;
  const STATUS_TEXT_EXECUTED = 'Выполнен';
  const STATUS_TEXT_CLOSED = "Закрыт";
  const STATUS_REPEATED = 4;
  const STATUS_TEXT_REPEATED = "Ждет повторного выполнения";


  /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'task_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('task_project_id, name, user_id', 'required'),
			array('task_project_id, status, user_id, repeat_every, week_schedule', 'numerical', 'integerOnly'=>true),
			array('close_date, description, status_text, repeated, last_close', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, task_project_id, name, description, status, created, user_id, close_date, repeat_every, week_schedule, status_text, repeated, last_close', 'safe', 'on'=>'search'),
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
      'taskProject' => array( self::BELONGS_TO,'TaskProject', 'task_project_id'),
      'monthSchedule' => array( self::HAS_MANY,'TaskMonthSchedule', 'task_item_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'task_project_id' => 'Task Project',
			'name' => 'Name',
			'description' => 'Description',
			'status' => 'Status',
			'created' => 'Created',
			'user_id' => 'User',
			'close_date' => 'Close Date',
			'repeat_every' => 'Repeat Every',
			'week_schedule' => 'Week Schedule',
      'status_text' => 'Status',
      'repeated' => 'Repeated',
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
		$criteria->compare('task_project_id',$this->task_project_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('close_date',$this->close_date,true);
		$criteria->compare('repeat_every',$this->repeat_every);
		$criteria->compare('week_schedule',$this->week_schedule);
    $criteria->compare('status_text',$this->status_text,true);
    $criteria->compare('repeated',$this->repeated);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TaskItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

  /**
   * @return int
   */
  public function getClose_date_int()
  {
    $res = is_numeric($this->close_date)? intval($this->close_date) : strtotime($this->close_date);
    return  $res;
  }

  public function save($runValidation=true,$attributes=null) {
    $this->close_date = Helpers::time2mysql_ts($this->close_date_int);
    if($this->status == self::STATUS_CLOSED && empty($this->last_close)) {
      $this->last_close = Helpers::time2mysql_ts();
    }
    return parent::save($runValidation,$attributes);
  }

  public function getMonthScheduleDays() {
    $days = array();
    foreach ($this->monthSchedule as $record) {
      $days[] = $record->day;
    }
    sort($days);
    return $days;
  }
  public function getMonthScheduleDaysString()
  {
    $days = $this->getMonthScheduleDays();
    return join(' ', $days);
  }

  public function beforeValidate(){
    $this->repeated = empty($this->repeated) ? 0 : 1;
    return parent::beforeValidate();
  }

  public function execute()
  {
    $this->last_close = Helpers::time2mysql_ts();
    if(empty($this->repeated)) {
      $this->status = self::STATUS_EXECUTED;
      $this->status_text = self::STATUS_TEXT_EXECUTED;
      return $this->save();
    }
    $this->status = self::STATUS_REPEATED;
    $this->status_text = self::STATUS_TEXT_REPEATED;
    if(!empty($this->monthSchedule) ) {
      $this->close_date = $this->getNextCloseMonth();
    } else {
      if(!empty($this->week_schedule) ) {
        $this->close_date = $this->getNextCloseWeek();
      } else {
        if(!empty($this->repeat_every) ) {
          $this->close_date = $this->getNextCloseEvery();
        }
      }
    }

    return $this->save();

  }

  private function getNextCloseEvery()
  {
    if(empty($this->close_date_int)){
      return false;
    }
    return $this->close_date_int + $this->repeat_every*Helpers::SECONDS_IN_DAY;
  }

  private function getNextCloseWeek()
  {
    if(empty($this->close_date_int)){
      return false;
    }
    $lastCloseDay = intval(date('N', $this->close_date_int));
    $lcdBit = intval($lastCloseDay)-1;
    $d = false;
    for($days = 0; $days < 7; $days++ ) {
      $nextCloseDay = ($lcdBit+$days) % 7 ;
      if($this->week_schedule & pow(2,$nextCloseDay) ) {
        $d = $days+1;
        break;
      }
    }
    if(empty($d)) {
      return false;
    }
    return $this->close_date_int + Helpers::SECONDS_IN_DAY*$d;
  }

  private function getNextCloseMonth()
  {
    if(empty($this->close_date_int)){
      return false;
    }
    $lastCloseDay = intval(date('j', $this->close_date_int));
    $lastCloseMonth = intval(date('n', $this->close_date_int));
    $nextCloseMonth = ($lastCloseMonth)%12+1;
    $lastCloseYear = intval(date('Y', $this->close_date_int));
    $nextCloseYear = $lastCloseYear;
    if($nextCloseMonth==1){
      $nextCloseYear = $lastCloseYear+1;
    }
    $mcd = $this->getMonthScheduleDays();
    $d = strtotime("{$lastCloseDay}/{$nextCloseMonth}/$nextCloseYear");
    foreach($mcd as $day) {
      $maxDay = Helpers::getDaysInMonth($lastCloseMonth, $lastCloseYear);
      if($maxDay < $day) {
        $day = $maxDay;
      }
      if($lastCloseDay < $day) {
        return $this->close_date_int+($day-$lastCloseDay)*Helpers::SECONDS_IN_DAY;
        //        return strtotime("{$day}/{$lastCloseMonth}/$lastCloseYear");
      }
    }
    if(!empty($mcd[0])) {
      /*if(Helpers::getDaysInMonth($nextCloseMonth, $nextCloseYear) < $mcd[0]) {
        $nextCloseMonth++;
      }*/
      return strtotime("{$nextCloseMonth}/{$mcd[0]}/{$nextCloseYear}");
    }
    return false;
  }

  public function unexecute()
  {
    $this->status = self::STATUS_REPEATED;
    return $this->save();
  }

}
