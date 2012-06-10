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
 * @property integer $status
 * @property integer $voteUp
 * @property integer $voteDown
 * @property integer $private
 * @property string $pictureName
 * @property string $pictureExtension
 * @property string $createDate
 * @property string $finishDate
 * @property string $answer
 * @property string $comment
 *
 * The followings are the available model relations:
 * @property User $idUserFrom0
 * @property User $idUserTo0
 * @property Truth $idTruth0
 * @property Dare $idDare0
 * @property Votingdetail[] $votingdetails

 */
class Challenge extends CActiveRecord
{
        public $score;
        public $scoreWeek;
        public $scoreMonth;
        public $scoreYear;
        public $pictureUploader;
    
        //Need to fetch results
        public $nbFavourite;
        public $nbComment;
        public $nbChallenge;
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
			array('idUserFrom, idUserTo, idTruth, idDare, status, voteUp, voteDown, private', 'numerical', 'integerOnly'=>true),
			array('pictureName', 'length', 'max'=>255),
			array('pictureExtension', 'length', 'max'=>5),
			array('finishDate, answer', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idChallenge, idUserFrom, idUserTo, idTruth, idDare, status, voteUp, voteDown, private, pictureName, pictureExtension, createDate, finishDate', 'safe', 'on'=>'search'),
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
			'userFrom' => array(self::BELONGS_TO, 'User', 'idUserFrom'),
			'userTo' => array(self::BELONGS_TO, 'User', 'idUserTo'),
			'truth' => array(self::BELONGS_TO, 'Truth', 'idTruth'),
			'dare' => array(self::BELONGS_TO, 'Dare', 'idDare'),
                        'votingdetails' => array(self::HAS_MANY, 'Votingdetail', 'idChallenge'),
			'levelUserFrom' => array(self::HAS_ONE, 'Verifidentity', 'idUser','on'=>'levelUserFrom.serialNumber = (SELECT serialNumber FROM verifidentity WHERE idUser=t.idUserFrom ORDER BY level DESC LIMIT 1)'),
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
			'status' => 'Status',
			'voteUp' => 'Vote Up',
			'voteDown' => 'Vote Down',
			'private' => 'Private',
			'pictureName' => 'Picture Name',
			'pictureExtension' => 'Picture Extension',
			'createDate' => 'Create Date',
			'finishDate' => 'Finish Date',
                        'answer' => 'Answer',
                        'comment' => 'Comment',
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
		$criteria->compare('status',$this->status);
		$criteria->compare('voteUp',$this->voteUp);
		$criteria->compare('voteDown',$this->voteDown);
		$criteria->compare('private',$this->private);
		$criteria->compare('pictureName',$this->pictureName,true);
		$criteria->compare('pictureExtension',$this->pictureExtension,true);
		$criteria->compare('createDate',$this->createDate,true);
		$criteria->compare('finishDate',$this->finishDate,true);		
                $criteria->compare('answer',$this->answer,true);	
                $criteria->compare('comment',$this->comment,true);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        /**
         * Returns the new score of the Challenge
	 * @return Int
	 */
        public function addVote($idUser,$typeVote)
        {         
            //Upgrade owner of the Truth if necessary
            $type = $this->idTruth === null ? 'Dare' : 'Truth';
            User::userRankUpgrade($this->idUserTo,1,$type);
            
            //We add the vote up or down
            if($typeVote == 'up')
                $this->voteUp += 1;
            else
                $this->voteDown += 1;  
            
            //We save
            $this->save();
                       
            //We add the vote to the table VotingDetail
            $votingDetail = new VotingDetail;
            $votingDetail->idUser = $idUser;
            $votingDetail->idChallenge = $this->idChallenge;
            $votingDetail->voteDate = date('Y-m-d, H:i:s');
            $votingDetail->voteType = $typeVote == 'up'? 1 : 0;
            $votingDetail->save(); 
            
            return $this->voteUp - $this->voteDown;;
        }

        /**
	 * Add the Dare Challenge picture in the 2 different sizes folders
	 * @return True or the Error Message
	 */
        public function addPicture($pictureName, $extension)
        {
            
            $allowedExtensions = array(".gif",".jpg",".png",".jpeg");

            $fromFile =  $_SERVER['DOCUMENT_ROOT'] . "TruthOrDare/userImages/temp/" . $pictureName . $extension;
            $toFileOriginal = $_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/challenge_original/" . $pictureName . '_original' . $extension;
            $toFileMini = $_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/challenge_mini/" . $pictureName . '_mini' . $extension;

            if (!in_array(strtolower($extension),$allowedExtensions))
                $result = "Wrong format of file - Only JPG/PNG/GIF are allowed";
            else if (!(filesize($fromFile) <= (1024 * 1024 * 2)))
                $result = "File too heavy - 2MB maximum";
            else
            {       
                //We keep 2 pictures, _original _mini
                //Original Picture
                if (rename($fromFile,$toFileOriginal))
                {
                    //Profile Picture (max width:150px)
                    $image = Yii::app()->image->load($toFileOriginal);
                    
                    //Thumbnail
                    if($image->__get('width') > $image->__get('height'))
                        $image->resize(50, 50, Image::HEIGHT);
                    else
                        $image->resize(50, 50, Image::WIDTH);
                    $image->crop(50, 50);
                    $image->save($toFileMini);
                    
                    $result = 1;               
                }
                else
                    $result = "Problem during file transfer";     
            }
            return $result;
        }

        
	/**
	 * Return CActiveRecord with Challenges 
	 * @return CActiveRecord
	 */
        public static function getChallenges($idUser, $idCategory=null, $idGender=null, $idType=null, $idStatus=null, $minDateChallenge=null, $idPrivateStatus=null, $idUserFrom=null)
        {
            $criteria = new CDbCriteria;           
            
            //Get users the current user added as Friends
            $criteria->condition = "t.idUserTo=:idUser AND t.status <> 2";
            $criteria->params = array(':idUser'=>$idUser);
            
            if($idCategory !== '' && $idCategory !== null){
                $criteria->addCondition("categoryTruth.idCategory = :idCategory OR categoryDare.idCategory = :idCategory");
                $criteria->params[':idCategory'] = $idCategory;
            }
            if($idGender !== '' && $idGender !== null){
                $criteria->addCondition("userFrom.gender = :gender");
                $criteria->params[':gender'] = $idGender;
            }
            if($idType !== '' && $idType !== null){
                $criteria->addCondition("t.id$idType IS NOT NULL");
            }
            if($idStatus !== '' && $idStatus !== null){
                $criteria->addCondition("t.status = :idStatus");
                $criteria->params[':idStatus'] = $idStatus;
            }
            if($minDateChallenge !== '' && $minDateChallenge !== null){
                $criteria->addCondition("IFNULL(t.finishDate,t.createDate) >= :minDateChallenge");
                $criteria->params[':minDateChallenge'] = $minDateChallenge;
            }
            if($idPrivateStatus !== '' && $idPrivateStatus !== null){
                $criteria->addCondition("t.private = :idPrivateStatus");
                $criteria->params[':idPrivateStatus'] = $idPrivateStatus;
            }
            if($idUserFrom !== '' && $idUserFrom !== null){
                $criteria->addCondition("t.idUserFrom = :idUserFrom");
                $criteria->params[':idUserFrom'] = $idUserFrom;
            }
            
            $challenges = Challenge::model()->with('truth','dare','truth.category','dare.category','userFrom','levelUserFrom')->findAll($criteria); 
            
            return $challenges;
        }
}