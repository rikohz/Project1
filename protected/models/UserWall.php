<?php

/**
 * This is the model class for table "userwall".
 *
 * The followings are the available columns in table 'userwall':
 * @property integer $idUserWall
 * @property integer $idUserFrom
 * @property integer $idUserTo
 * @property string $content
 * @property string $createDate
 *
 * The followings are the available model relations:
 * @property User $idUserTo0
 * @property User $idUserFrom0
 */
class Userwall extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Userwall the static model class
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
		return 'userwall';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idUserFrom, idUserTo, content, createDate', 'required'),
			array('idUserFrom, idUserTo', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idUserWall, idUserFrom, idUserTo, content, createDate', 'safe', 'on'=>'search'),
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
			'userTo' => array(self::BELONGS_TO, 'User', 'idUserTo'),
			'userFrom' => array(self::BELONGS_TO, 'User', 'idUserFrom'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idUserWall' => 'Id User Wall',
			'idUserFrom' => 'Id User From',
			'idUserTo' => 'Id User To',
			'content' => 'Content',
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

		$criteria->compare('idUserWall',$this->idUserWall);
		$criteria->compare('idUserFrom',$this->idUserFrom);
		$criteria->compare('idUserTo',$this->idUserTo);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('createDate',$this->createDate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}