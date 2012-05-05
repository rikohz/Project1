<?php

/**
 * This is the model class for table "comment".
 *
 * The followings are the available columns in table 'comment':
 * @property string $idComment
 * @property string $idUser
 * @property integer $idTruth
 * @property integer $idDare
 * @property string $comment
 * @property string $submitDate
 *
 * The followings are the available model relations:
 * @property Dare $idDare0
 * @property User $idUser0
 * @property Truth $idTruth0
 */
class Comment extends CActiveRecord
{
    
	public $verifyCode;
        public $nbComments;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comment the static model class
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
		return 'comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idUser, comment, submitDate', 'required'),
			array('idTruth, idDare', 'numerical', 'integerOnly'=>true),
			array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idComment, idUser, idTruth, idDare, comment, submitDate', 'safe', 'on'=>'search'),
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
			'dare' => array(self::BELONGS_TO, 'Dare', 'idDare'),
			'user' => array(self::BELONGS_TO, 'User', 'idUser','select'=>'username','order'=>'t.submitDate DESC'),
			'truth' => array(self::BELONGS_TO, 'Truth', 'idTruth'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idComment' => 'Id Comment',
			'idUser' => 'Id User',
			'idTruth' => 'Id Truth',
			'idDare' => 'Id Dare',
			'comment' => 'Comment',
			'submitDate' => 'Submit Date',
			'verifyCode'=>'Verification Code',
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

		$criteria->compare('idComment',$this->idComment,true);
		$criteria->compare('idUser',$this->idUser,true);
		$criteria->compare('idTruth',$this->idTruth);
		$criteria->compare('idDare',$this->idDare);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('submitDate',$this->submitDate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}