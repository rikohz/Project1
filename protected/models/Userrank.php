<?php

/**
 * This is the model class for table "userrank".
 *
 * The followings are the available columns in table 'userrank':
 * @property integer $idUserRank
 * @property integer $idUser
 * @property integer $idRank
 * @property string $createDate
 *
 * The followings are the available model relations:
 * @property Rank $idRank0
 * @property User $idUser0
 */
class Userrank extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Userrank the static model class
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
		return 'userrank';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idUser, idRank, createDate', 'required'),
			array('idUser, idRank', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idUserRank, idUser, idRank, createDate', 'safe', 'on'=>'search'),
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
			'rank' => array(self::BELONGS_TO, 'Rank', 'idRank'),
			'user' => array(self::BELONGS_TO, 'User', 'idUser'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idUserRank' => 'Id User Rank',
			'idUser' => 'Id User',
			'idRank' => 'Id Rank',
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

		$criteria->compare('idUserRank',$this->idUserRank);
		$criteria->compare('idUser',$this->idUser);
		$criteria->compare('idRank',$this->idRank);
		$criteria->compare('createDate',$this->createDate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}