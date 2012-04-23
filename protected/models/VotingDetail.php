<?php

/**
 * This is the model class for table "votingDetail".
 *
 * The followings are the available columns in table 'votingDetail':
 * @property string $idVotingDetail
 * @property string $idUser
 * @property integer $idTruth
 * @property integer $idDare
 * @property integer $voteType
 * @property string $voteDate
 * 
 * The followings are the available model relations:
 * @property User $idUser0
 * @property Truth $idTruth0
 * @property Dare $idDare0
*/

class VotingDetail extends CActiveRecord
{
    public $scoreTotal;
    public $scoreTruthWeek;
    public $scoreDareWeek;
    public $scoreTruthMonth;
    public $scoreDareMonth;
    public $scoreTruthYear;
    public $scoreDareYear;
    public $scoreTruth;
    public $scoreDare;
	/**
	 * Returns the static model of the specified AR class.
	 * @return VotingDetail the static model class
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
		return 'votingDetail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idUser, voteType, voteDate', 'required'),
			array('idTruth, idDare, voteType', 'numerical', 'integerOnly'=>true),
			array('idUser', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idVotingDetail, idUser, idTruth, idDare, voteType, voteDate', 'safe', 'on'=>'search'),
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
			'truth' => array(self::BELONGS_TO, 'Truth', 'idTruth','joinType'=>'INNER JOIN','on'=>"truth.idUser = :idUser",'select'=>false),
			'dare' => array(self::BELONGS_TO, 'Dare', 'idDare','joinType'=>'INNER JOIN','on'=>"dare.idUser = :idUser",'select'=>false),
			'user' => array(self::BELONGS_TO, 'User', 'idUser','join'=>'INNER JOIN'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idVotingDetail' => 'Id Voting Detail',
			'idUser' => 'Id User',
			'idTruth' => 'Id Truth',
			'idDare' => 'Id Dare',
			'voteType' => 'Vote Type',
			'voteDate' => 'Vote Date'
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

		$criteria->compare('idVotingDetail',$this->idVotingDetail,true);
		$criteria->compare('idUser',$this->idUser,true);
		$criteria->compare('idTruth',$this->idTruth);
		$criteria->compare('idDare',$this->idDare);
		$criteria->compare('voteType',$this->voteType);
		$criteria->compare('voteDate',$this->voteDate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}