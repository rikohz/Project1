<?php

/**
 * This is the model class for table "rank".
 *
 * The followings are the available columns in table 'rank':
 * @property integer $idRank
 * @property integer $type
 * @property integer $points
 * @property string $name
 *
 * The followings are the available model relations:
 * @property Userrank[] $userranks
 */
class Rank extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Rank the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'rank';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, points, name', 'required'),
			array('type, points', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idRank, type, points, name', 'safe', 'on'=>'search'),
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
			'userranks' => array(self::HAS_MANY, 'Userrank', 'idRank'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idRank' => 'Id Rank',
			'type' => 'Type',
			'points' => 'Points',
			'name' => 'Name',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('idRank',$this->idRank);
		$criteria->compare('type',$this->type);
		$criteria->compare('points',$this->points);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}