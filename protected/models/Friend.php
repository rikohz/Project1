<?php

/**
 * This is the model class for table "friend".
 *
 * The followings are the available columns in table 'friend':
 * @property integer $idFriend
 * @property integer $idUserFrom
 * @property integer $idUserTo
 * @property integer $accepted
 * @property string $createDate
 *
 * The followings are the available model relations:
 * @property User $idUserTo0
 * @property User $idUserFrom0
 */
class Friend extends CActiveRecord
{
    //test
    public $username;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Friend the static model class
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
		return 'friend';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('idUserFrom, idUserTo, createDate', 'required'),
			array('idUserFrom, idUserTo, accepted', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idFriend, idUserFrom, idUserTo, accepted, createDate', 'safe', 'on'=>'search'),
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
			'levelUserFrom' => array(self::HAS_ONE, 'Verifidentity', 'idUser','on'=>'levelUserFrom.serialNumber = (SELECT serialNumber FROM verifidentity WHERE idUser=t.idUserFrom ORDER BY level DESC LIMIT 1)'),
			'levelUserTo' => array(self::HAS_ONE, 'Verifidentity', 'idUser','on'=>'levelUserTo.serialNumber = (SELECT serialNumber FROM verifidentity WHERE idUser=t.idUserTo ORDER BY level DESC LIMIT 1)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idFriend' => 'Id Friend',
			'idUserFrom' => 'Id User From',
			'idUserTo' => 'Id User To',
			'accepted' => 'Accepted',
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

		$criteria->compare('idFriend',$this->idFriend);
		$criteria->compare('idUserFrom',$this->idUserFrom);
		$criteria->compare('idUserTo',$this->idUserTo);
		$criteria->compare('accepted',$this->accepted);
		$criteria->compare('createDate',$this->createDate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Function that checks if 2 users are friends or have already friend requests processing
	 * @return 1 if friends 0 otherwise
	 */
        public static function areFriendsOrFriendRequest($idUser1, $idUser2, $friendStatus=null)
        {
            $criteria = new CDbCriteria;
            $criteria->addCondition('((idUserFrom = :idUserFrom AND idUserTo = :idUserTo) OR (idUserFrom = :idUserTo AND idUserTo = :idUserFrom))');
            if($friendStatus === null)
                $criteria->addInCondition('accepted', array("0","1"));
            else
                $criteria->addCondition("accepted = $friendStatus"); 
            $criteria->params[":idUserFrom"] = $idUser1;
            $criteria->params[":idUserTo"] = $idUser2;
            return Friend::model()->exists($criteria) || ($idUser1 === $idUser2);
        }

        
	/**
	 * Return array with user friends 
	 * @return array
	 */
        public static function getFriends($idUser,$friendStatus=1,$orderField='username',$orderDirection='ASC',$gender=null,$level=null)
        {
            $criteria = new CDbCriteria;           
            
            //Get users the current user added as Friends
            $criteria->condition = "idUserFrom=:idUser and accepted=:friendStatus";
            $criteria->params = array(':idUser'=>$idUser,':friendStatus'=>$friendStatus);
            if($gender !== '' && $gender !== null){
                $criteria->addCondition("userTo.gender = :gender");
                $criteria->params[':gender'] = $gender;
            }
            if($level !== '' && $level !== null){
                $criteria->addCondition("levelUserTo.level = :level");
                $criteria->params[':level'] = $level;
            }
            $friendsFrom = Friend::model()->with('userTo','userTo.levelUserTo')->findAll($criteria); 
            
            //Get users that added the current user as Friends
            $criteria->condition = "idUserTo=:idUser and accepted=:friendStatus";
            $criteria->params = array(':idUser'=>$idUser,':friendStatus'=>$friendStatus);
            if($gender !== '' && $gender !== null){
                $criteria->addCondition("userFrom.gender = :gender");
                $criteria->params[':gender'] = $gender;
            }
            if($level !== '' && $level !== null){
                $criteria->addCondition("levelUserFrom.level = :level");
                $criteria->params[':level'] = $level;
            }
            $friendsTo = Friend::model()->with('userFrom','userFrom.levelUserFrom')->findAll($criteria); 
            
            //Gather them together
            $friends = array(); $i=0;
            foreach($friendsFrom as $row)
            {
                $friends[$i]['idUser'] = $row->userTo->idUser;
                $friends[$i]['username'] = $row->userTo->username;
                $friends[$i]['profilePicture'] = $row->userTo->profilePicture;
                $friends[$i]['profilePictureExtension'] = $row->userTo->profilePictureExtension;
                $friends[$i]['level'] = $row->userTo->levelUserTo->level;
                $i++;      
            }
            foreach($friendsTo as $row)
            {
                $friends[$i]['idUser'] = $row->userFrom->idUser;
                $friends[$i]['username'] = $row->userFrom->username;
                $friends[$i]['profilePicture'] = $row->userFrom->profilePicture;
                $friends[$i]['profilePictureExtension'] = $row->userFrom->profilePictureExtension;
                $friends[$i]['level'] = $row->userFrom->levelUserFrom->level;
                $i++;  
            }
            $friends = MyFunctions::arraySort($friends,$orderField,$orderDirection);
            return $friends;
        }

        
	/**
	 * Return array of CActiveRecord
	 * @return array(CActiveRecord)
	 */
        public static function getFriendRequests($idUser)
        {
            $criteria = new CDbCriteria;
            $criteria->condition = 't.idUserTo=:idUser AND t.accepted=:friendStatus';
            $criteria->params = array(':idUser'=>$idUser,':friendStatus'=>0);
            $criteria->order = 'userFrom.username';
            $friendRequests = Friend::model()->with('userFrom','levelUserFrom')->findAll($criteria);
            return $friendRequests;
        }
}