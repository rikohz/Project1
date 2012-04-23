<?php

/**
 * This is the model class for table "challenge".
 *
 * The followings are the available columns in table 'challenge':
 * @property integer $idChallenge
 * @property integer $idUserFrom
 * @property integer $idUserTo
 * @property integer $idTruth
 * @property integer $idDare
 * @property integer $success
 * @property integer $private
 * @property string $createDate
 *
 * The followings are the available model relations:
 * @property Dare $idDare0
 * @property User $idUserFrom0
 * @property User $idUserTo0
 * @property Truth $idTruth0
 */
class Challenge extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Challenge the static model class
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
		return 'challenge';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idUserFrom, idUserTo, private, createDate', 'required'),
			array('idUserFrom, idUserTo, idTruth, idDare, success, private', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idChallenge, idUserFrom, idUserTo, idTruth, idDare, success, private, createDate', 'safe', 'on'=>'search'),
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
			'idDare0' => array(self::BELONGS_TO, 'Dare', 'idDare'),
			'idUserFrom0' => array(self::BELONGS_TO, 'User', 'idUserFrom'),
			'idUserTo0' => array(self::BELONGS_TO, 'User', 'idUserTo'),
			'idTruth0' => array(self::BELONGS_TO, 'Truth', 'idTruth'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idChallenge' => 'Id Challenge',
			'idUserFrom' => 'Id User From',
			'idUserTo' => 'Id User To',
			'idTruth' => 'Id Truth',
			'idDare' => 'Id Dare',
			'success' => 'Success',
			'private' => 'Private',
			'createDate' => 'Create Date',
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

		$criteria->compare('idChallenge',$this->idChallenge);
		$criteria->compare('idUserFrom',$this->idUserFrom);
		$criteria->compare('idUserTo',$this->idUserTo);
		$criteria->compare('idTruth',$this->idTruth);
		$criteria->compare('idDare',$this->idDare);
		$criteria->compare('success',$this->success);
		$criteria->compare('private',$this->private);
		$criteria->compare('createDate',$this->createDate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}