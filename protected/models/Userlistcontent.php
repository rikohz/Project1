<?php

/**
 * This is the model class for table "userlistcontent".
 *
 * The followings are the available columns in table 'userlistcontent':
 * @property integer $idUserListContent
 * @property integer $idUserList
 * @property integer $idTruth
 * @property integer $idDare
 *
 * The followings are the available model relations:
 * @property Userlist $idUserList0
 * @property Truth $idTruth0
 * @property Dare $idDare0
 */
class Userlistcontent extends CActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Userlistcontent the static model class
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
		return 'userlistcontent';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idUserList', 'required'),
			array('idUserList, idTruth, idDare', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idUserListContent, idUserList, idTruth, idDare', 'safe', 'on'=>'search'),
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
			'userList' => array(self::BELONGS_TO, 'Userlist', 'idUserList'),
			'truth' => array(self::BELONGS_TO, 'Truth', 'idTruth'),
			'dare' => array(self::BELONGS_TO, 'Dare', 'idDare'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idUserListContent' => 'Id User List Content',
			'idUserList' => 'Id User List',
			'idTruth' => 'Id Truth',
			'idDare' => 'Id Dare',
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

		$criteria->compare('idUserListContent',$this->idUserListContent);
		$criteria->compare('idUserList',$this->idUserList);
		$criteria->compare('idTruth',$this->idTruth);
		$criteria->compare('idDare',$this->idDare);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}