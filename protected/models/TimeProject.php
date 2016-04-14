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
 * @property TimeItem[] $timeItems
 */
class TimeProject extends CActiveRecord
{

  const STATUS_STOPPED = 0;
  const STATUS_STARTED = 1;
  const STATUS_DELETED = -1;
  public $today;
  public $week;
  public $month;

  public function __construct($user_id, $scenario = 'insert')
  {
    parent::__construct($scenario);
    $this->user_id = $user_id;
    $this->status = self::STATUS_STOPPED;
  }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
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
      'timeItems' => array(self::HAS_MANY, 'TimeItem', 'time_project_id')
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

  /**
   * @return mixed
   */
  public function getToday()
  {
    return $this->today;
  }

  /**
   * @return mixed
   */
  public function getWeek()
  {
    return $this->week;
  }

  /**
   * @return mixed
   */
  public function getMonth()
  {
    return $this->month;
  }

  public function stop($upd_time = null)
  {
    $this->status=self::STATUS_STOPPED;
    $this->updated = Helpers::time2mysql_ts($upd_time);
    $this->save();
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

}
