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
 * @property integer $repeat_every
 * @property integer $week_schedule
 * @property string $status_text
 * @property TaskProject taskProject
 * @property integer close_date_int
 */
class TaskItem extends CActiveRecord
{
  const STATUS_NEW = 1;
  const STATUS_TEXT_NEW = 'Новый';


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
			array('close_date, description, status_text', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, task_project_id, name, description, status, created, user_id, close_date, repeat_every, week_schedule, status_text', 'safe', 'on'=>'search'),
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
    return parent::save($runValidation,$attributes);
  }

  public function getMonthScheduleDays() {
    $days = array();
    foreach ($this->monthSchedule as $record) {
      $days[] = $record->day;
    }
    return $days;
  }
  public function getMonthScheduleDaysString()
  {
    $days = $this->getMonthScheduleDays();
    return join(', ', $days);
  }
}
