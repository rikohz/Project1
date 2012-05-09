<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $idUser
 * @property integer $gender
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $birthDate
 * @property string $registrationDate 
 * @property string $serialNumber
 * @property string $validation 
 * @property string $profilePicture
 * @property string $profilePictureExtension
 * @property integer $idProvince
 * @property integer $idCity
 * @property integer $idDistrict

 *
 * The followings are the available model relations:
 * @property Dare[] $dares
 * @property Truth[] $truths
 * @property Verifidentity $idUser0
 * @property Verifidentity[] $verifidentities
 * @property Comment[] $comments
 * @property Votingdetail[] $votingdetails
 * @property District $idDistrict0
 * @property Province $idProvince0
 * @property City $idCity0

 */
class User extends CActiveRecord
{   
        public $conf_email; 
        public $conf_password;
        public $verifyCode;
        public $verifCode;
        public $newPassword;
        public $conf_newPassword;
        public $pictureUploader;
	
        /**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                    
                        //Common
			array('username, email, password, registrationDate, gender', 'required'),
			array('username, email, conf_email, password, conf_password', 'length', 'max'=>64),
			array('username', 'length', 'min'=>3),
			array('verifCode, serialNumber', 'length', 'max'=>20),
			array('email,conf_email', 'email'),
			array('serialNumber, verifCode', 'numerical'),
			array('email', 'unique','message' => "This email already has an account!",'className'=>'user'),
			array('username', 'unique','message' => "Sorry, this username has been used already!",'className'=>'user'),
			array('verifyCode', 'captcha', 'allowEmpty'=>!extension_loaded('gd')),
                        array('birthDate','safe'),
                    
                        //Register
                        array('serialNumber, conf_email, verifCode, validation, conf_password','required','on'=>'register'),
                        array('conf_email', 'compare', 'compareAttribute'=>'email','on'=>'register'),
			array('verifCode', 'verifyCoinPassword','on'=>'register'),
			array('serialNumber', 'verifyUnicitySerialNumber','on'=>'register'),
                        array('conf_password', 'compare', 'compareAttribute'=>'password','on'=>'register'),
                        array('pictureUploader', 'file', 'types'=>'jpg, jpeg, gif, png', 'maxSize'=> '2097152', 'tooLarge'=>'This file is too big...', 'wrongType'=>'Wrong format of file...', 'allowEmpty'=>true,'on'=>'register'),
                        array('idProvince, idCity, idDistrict, profilePictureExtension', 'safe','on'=>'register'),
			array('serialNumber', 'exist','message' => "Sorry, this Serial Number does not exist",'className'=>'verifIdentity','on'=>'register'),
                        array('profilePicture, profilePictureExtension','safe','on'=>'register'),
                    
                        //Update User
                        array('conf_email','required','on'=>'updateUser'),
                        array('conf_email', 'compare', 'compareAttribute'=>'email','on'=>'updateUser'), 
                        array('idProvince, idCity, idDistrict', 'safe','on'=>'updateUser'),
                    
                        //Change Password
                        array('newPassword, conf_newPassword','required','on'=>'changePassword'),
                        array('conf_newPassword', 'compare', 'compareAttribute'=>'newPassword','on'=>'changePassword'),
                    
                        //Update Coins
                        array('serialNumber, verifCode','required','on'=>'addCoin'),
			array('verifCode', 'verifyCoinPassword','on'=>'addCoin'),
			array('serialNumber', 'exist','message' => "Sorry, this Serial Number does not exist",'className'=>'verifIdentity','on'=>'addCoin'),
                    
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('profilePicture, idUser, username, email, password, registrationDate, serialNumber, gender, validation, idProvince, idCity, idDistrict', 'safe', 'on'=>'search'),
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
			'comments' => array(self::HAS_MANY, 'Comment', 'idUser'),
			'dares' => array(self::HAS_MANY, 'Dare', 'idUser'),
			'scoreDare' => array(self::HAS_ONE, 'Scoredare', 'idUser'),
			'scoreTruth' => array(self::HAS_ONE, 'Scoretruth', 'idUser'),
			'truths' => array(self::HAS_MANY, 'Truth', 'idUser'),
                        'verifidentities' => array(self::HAS_MANY, 'Verifidentity', 'idUser'),
			'votingdetails' => array(self::HAS_MANY, 'Votingdetail', 'idUser'),
                        'district' => array(self::BELONGS_TO, 'District', 'idDistrict'),
			'province' => array(self::BELONGS_TO, 'Province', 'idProvince'),
			'city' => array(self::BELONGS_TO, 'City', 'idCity'),

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idUser' => 'Id User',
			'username' => 'Username',
			'email' => 'Email',
			'password' => 'Password',
			'registrationDate' => 'Registration Date',
                        'serialNumber' => 'Serial Number',
			'verifyCode'=>'Verification Code',
			'conf_email' => 'Confirm Email',
			'conf_password' => 'Confirm Password',
			'newPassword' => 'New Password',
			'conf_newPassword' => 'Confirm new Password',
			'verifCode' => 'Password Coin',
			'gender' => 'Gender',
			'profilePicture' => 'Choose your picture',
			'idProvince' => 'Choose your Province',
			'idCity' => 'Choose your City',
			'idDistrict' => 'Choose your District',
			'birthDate' => 'Birth Date',
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

		$criteria->compare('idUser',$this->idUser,true);
                $criteria->compare('gender',$this->gender);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('birthDate',$this->birthDate,true);
		$criteria->compare('registrationDate',$this->registrationDate,true);
                $criteria->compare('serialNumber',$this->serialNumber,true);
		$criteria->compare('validation',$this->validation,true);
		$criteria->compare('profilePicture',$this->profilePicture,true);
		$criteria->compare('idProvince',$this->idProvince);
		$criteria->compare('idCity',$this->idCity);
		$criteria->compare('idDistrict',$this->idDistrict);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        /**
	 * Validation Rule
         * Add error if the SerialNumber and the Password don't match
	 */
        public function verifyCoinPassword($attribute,$params)
	{
                    if(!VerifIdentity::model()->exists('serialNumber = :serialNumber AND verifCode = :verifCode',array(':serialNumber'=>$this->serialNumber,':verifCode'=>$this->verifCode)))
                            $this->addError('verifCode','Incorrect validation Coin Password.');

	}
        
        /**
	 * Validation Rule
         * Add error if the SerialNumber is already linked with a person
	 */
        public function verifyUnicitySerialNumber($attribute,$params)
	{
                    $verifIdentity = VerifIdentity::model()->findByPk($this->serialNumber);
                    if($verifIdentity != null)
                    {
                        if(!($verifIdentity->idUser === null))
                            $this->addError('serialNumber','This serial Number is already used.');
                    }
                    
        }
        
        /**
	 * Validation Rule
         * Add error if the Username and the Password don't match
	 */
        public function verifyPassword($attribute,$params)
        {
            if(!$this->hasErrors())
            {
                if(User::model()->exists('username = :username AND password = :password',array(':username'=>$this->username,':password'=>$this->password)))
                    $this->addError('password','Incorrect password');
            }
        }
        
        /**
         * For registration or change password, we hash the password before to save it
	 */
        public function beforeSave() {
            if(($this->getScenario() === 'register') || ($this->getScenario() === 'changePassword'))    
                $this->password = md5($this->password);
            return true;
        }

        /**
	 * Returns the level of the User according to his highest registered coin
	 * @return Int as User level
	 */
        public function getLevel()
	{
            $criteria = new CDbCriteria;
            $criteria->condition = " idUser = $this->idUser ";
            $criteria->order = ' t.level DESC ';
            
            $userCoins = VerifIdentity::model()->find($criteria);
                    
            return $userCoins === null ? 1 : $userCoins['level'];                              
        }

        /**
	 * Add the profile Picture in the 3 different sizes folders
	 * @return True or the Error Message
	 */
        public function addPicture($pictureName, $extension, $oldProfilePictureName=null,$oldPictureProfileExtension=null)
        {
            
            $allowedExtensions = array(".gif",".jpg",".png",".jpeg");

            $fromFile =  $_SERVER['DOCUMENT_ROOT'] . "TruthOrDare/userImages/temp/" . $pictureName . $extension;
            $toFileProfile = $_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/profilePicture/" . $pictureName . '_profile' . $extension;
            $toFileOriginal = $_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/profilePicture_original/" . $pictureName . '_original' . $extension;
            $toFileMini = $_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/profilePicture_mini/" . $pictureName . '_mini' . $extension;

            if (!in_array(strtolower($extension),$allowedExtensions))
                $result = "Wrong format of file - Only JPG/PNG/GIF are allowed";
            else if (!(filesize($fromFile) <= (1024 * 1024 * 2)))
                $result = "File too heavy - 2MB maximum";
            else
            {       
                //Delete old Profile Picture
                if($oldProfilePictureName !== "default" )
                {
                    if(is_file($_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/profilePicture/" . $oldProfilePictureName . '_profile' . $oldPictureProfileExtension))
                        unlink($_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/profilePicture/" . $oldProfilePictureName . '_profile' . $oldPictureProfileExtension);                          
                    if(is_file($_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/profilePicture_mini/" . $oldProfilePictureName . '_mini' . $oldPictureProfileExtension))
                        unlink($_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/profilePicture_mini/" . $oldProfilePictureName . '_mini' . $oldPictureProfileExtension);                       
                    if(is_file($_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/profilePicture_original/" . $oldProfilePictureName . '_original' . $oldPictureProfileExtension))
                        unlink($_SERVER['DOCUMENT_ROOT']. "TruthOrDare/userImages/profilePicture_original/" . $oldProfilePictureName . '_original' . $oldPictureProfileExtension);    
                }        
                
                //We keep 3 pictures, _original _profile _mini
                //Original Picture
                if (rename($fromFile,$toFileOriginal))
                {
                    //Profile Picture (max width:150px)
                    $image = Yii::app()->image->load($toFileOriginal);
                    $image->resize(150, 150, Image::WIDTH);
                    $image->save($toFileProfile);
                    
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
         * Returns the Truth score of the User $idUser related to the votes of his/her submitted ideas
	 * @return array()
	 */
       public function getScoreVoteIdeas($type=null)
       {
           if($type == null)
               return getScoreVoteIdeas('truth') + getScoreVoteIdeas('dare');
           else
           {
               //Prepare Query
               $criteria = new CDbCriteria;
               $criteria->group = " $type.idUser ";
               $criteria->select = " SUM(CASE WHEN t.voteDate >= :minDateSubmitWeek THEN (CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) END) AS scoreWeek, ";
               $criteria->select .= " SUM(CASE WHEN t.voteDate >= :minDateSubmitMonth THEN (CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) END) AS scoreMonth, ";
               $criteria->select .= " SUM(CASE WHEN t.voteDate >= :minDateSubmitYear THEN (CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) END) AS scoreYear, ";
               $criteria->select .= " SUM(CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) AS score ";
               $criteria->with = array($type);
               $criteria->addCondition(" $type.idUser = :idUser ");

               //Bind Parameters
               $criteria->params = array(':idUser'=>$this->idUser);
               $criteria->params[':minDateSubmitWeek'] = MyFunctions::getFirstDayWeek();
               $criteria->params[':minDateSubmitMonth'] = MyFunctions::getFirstDayMonth();
               $criteria->params[':minDateSubmitYear'] = MyFunctions::getFirstDayYear();

               //Execute Query
               $result = VotingDetail::model()->find($criteria);

               //Fetch results
               $scoreTotal = $result['score'] === null? 0 : $result->score;
               $scoreWeek = $result['scoreWeek'] === null? 0 : $result->scoreWeek;
               $scoreMonth = $result['scoreMonth'] === null? 0 : $result->scoreMonth;
               $scoreYear = $result['scoreYear'] === null? 0 : $result->scoreYear;

               return array('total'=>$scoreTotal,'week'=>$scoreWeek,'month'=>$scoreMonth,'year'=>$scoreYear);
           }
       }
        
       /**
         * Returns the Truth score of the User $idUser related to the Challenges he/she successfuly realized
	 * @return array()
	 */
       public function getScoreChallenges($type)
       {          
           //Prepare Query
           $criteria = new CDbCriteria;
           $criteria->group = " t.idUserTo ";
           $criteria->select = " SUM(CASE WHEN t.finishDate >= :minDateSubmitWeek THEN (CASE WHEN t.idTruth IS NULL THEN 5 ELSE 2 END) END) AS scoreWeek, ";
           $criteria->select .= " SUM(CASE WHEN t.finishDate >= :minDateSubmitMonth THEN (CASE WHEN t.idTruth IS NULL THEN 5 ELSE 2 END) END) AS scoreMonth, ";
           $criteria->select .= " SUM(CASE WHEN t.finishDate >= :minDateSubmitYear THEN (CASE WHEN t.idTruth IS NULL THEN 5 ELSE 2 END) END) AS scoreYear, ";
           $criteria->select .= " SUM(CASE WHEN t.idTruth IS NULL THEN 5 ELSE 2 END) AS score ";
           $criteria->addCondition(' t.success = 1 ');
           $criteria->addCondition(' t.idUserTo = :idUser ');
           if($type !== null)
                $criteria->addCondition(" t.id$type IS NOT NULL ");

           //Bind Parameters
           $criteria->params = array(':idUser'=>$this->idUser);
           $criteria->params[':minDateSubmitWeek'] = MyFunctions::getFirstDayWeek();
           $criteria->params[':minDateSubmitMonth'] = MyFunctions::getFirstDayMonth();
           $criteria->params[':minDateSubmitYear'] = MyFunctions::getFirstDayYear();

           //Execute Query
           $result = Challenge::model()->find($criteria);

           //Fetch results
           $scoreTotal = $result['score'] === null? 0 : $result->score;
           $scoreWeek = $result['scoreWeek'] === null? 0 : $result->scoreWeek;
           $scoreMonth = $result['scoreMonth'] === null? 0 : $result->scoreMonth;
           $scoreYear = $result['scoreYear'] === null? 0 : $result->scoreYear;

           return array('total'=>$scoreTotal,'week'=>$scoreWeek,'month'=>$scoreMonth,'year'=>$scoreYear);
       }
        
       /**
         * Returns the Truth score of the User $idUser related to the vote of the Challenges he/she successfuly realized
	 * @return array()
	 */
       public function getScoreVoteChallenges($type)
       {         
           //Prepare Query
           $criteria = new CDbCriteria;
           $criteria->group = " t.idUserTo ";
           $criteria->select = " SUM(CASE WHEN t.finishDate >= :minDateSubmitWeek THEN t.voteUp - t.voteDown END) AS scoreWeek, ";
           $criteria->select .= " SUM(CASE WHEN t.finishDate >= :minDateSubmitMonth THEN t.voteUp - t.voteDown END) AS scoreMonth, ";
           $criteria->select .= " SUM(CASE WHEN t.finishDate >= :minDateSubmitYear THEN t.voteUp - t.voteDown END) AS scoreYear, ";
           $criteria->select .= " SUM(t.voteUp - t.voteDown) AS score ";
           $criteria->addCondition(' t.success = 1 ');
           $criteria->addCondition(' t.idUserTo = :idUser ');
           if($type !== null)
                $criteria->addCondition(" t.id$type IS NOT NULL ");

           //Bind Parameters
           $criteria->params = array(':idUser'=>$this->idUser);
           $criteria->params[':minDateSubmitWeek'] = MyFunctions::getFirstDayWeek();
           $criteria->params[':minDateSubmitMonth'] = MyFunctions::getFirstDayMonth();
           $criteria->params[':minDateSubmitYear'] = MyFunctions::getFirstDayYear();

           //Execute Query
           $result = Challenge::model()->find($criteria);

           //Fetch results
           $scoreTotal = $result['score'] === null? 0 : $result->score;
           $scoreWeek = $result['scoreWeek'] === null? 0 : $result->scoreWeek;
           $scoreMonth = $result['scoreMonth'] === null? 0 : $result->scoreMonth;
           $scoreYear = $result['scoreYear'] === null? 0 : $result->scoreYear;

           return array('total'=>$scoreTotal,'week'=>$scoreWeek,'month'=>$scoreMonth,'year'=>$scoreYear); 
       }

       /**
         * Create a new user
	 * @return 1 if success 0 else
	 */
        public function createUser()
        {
            $result = 1;
            
            $this->registrationDate = date('Y-m-d');
            $this->validation = uniqid();
      
            $transaction = $this->dbConnection->beginTransaction();
            
            try
            {            
                $this->save();
             
                //Get the idUser of the new User and specify it in the table verifIdentity
                $idUser = Yii::app()->db->getLastInsertId();
                $verifIdentity = VerifIdentity::model()->findByPk($this->serialNumber);
                $verifIdentity->idUser = $idUser;
                if(!$verifIdentity->save())
                    throw new CDbException(null);     

                //Upload of Profile Picture
                if($this->profilePicture !== "default")
                {
                    $result = $this->addPicture($this->profilePicture, $this->profilePictureExtension);
                    if($result !== 1)
                    {
                        $this->addError('pictureUploader',$result);
                        throw new CException(null);                 
                    }
                }
                
                $transaction->commit();
            }
            catch(Exception $e)
            {
                if($e !== null)
                    $this->addError('pictureUploader',"Problem during create process");
                $transaction->rollBack();
                $result = 0;
            }  

            return $result;
        }

       /**
         * Update the profile picture of the user
	 * @return 1 if success 0 else
	 */
        public function updatePicture($newPictureName, $newPictureExtension)
        {
            $result = 1; 
            $oldprofilePictureName = $this->profilePicture;
            $oldprofilePictureExtension = $this->profilePictureExtension;
            $transaction = $this->dbConnection->beginTransaction();
            
            try
            {                         
                $this->profilePicture = $newPictureName;
                $this->profilePictureExtension = $newPictureExtension;
                if(!$this->save())
                    throw new CDbException(null);  
                
                $result = $this->addPicture($newPictureName, $newPictureExtension,$oldprofilePictureName,$oldprofilePictureExtension);
                if($result !== 1)
                {
                    $this->addError('pictureUploader',$result);
                    throw new CException(null);                 
                }  
                
                $transaction->commit();
            }
            catch(Exception $e)
            {
                if($e !== null)
                    $this->addError('pictureUploader',"Problem during file transfer");
                $transaction->rollBack();
                $result = 0;
            } 

            return $result;
        }
        
        /**
         * This function has to be called when a user just gained points
         * The function will upgrade the user if necessary
	 * @return 1 if upgraded otherwise 0
	 */
        public static function userRankUpgrade($idUser, $nbPoints, $type)
        {
            $result = 0;
            $user = User::model()->with("score".$type)->findByPk($idUser);
            $score = $type === 'Truth'? $user->scoreTruth->score : $user->scoreDare->score;
            //$type = $type === 'Truth'? 0 : 1;
            $ranks = Rank::model()->findAllByAttributes(array('type'=>$type === 'Truth'? 0 : 1));
            foreach($ranks as $row)
            {
                if(($score - $row->points < 0) && ($score - $row->points + $nbPoints >= 0))
                {
                    $userRank = new Userrank;
                    $userRank->idUser = $idUser;
                    $userRank->createDate = date('Y-m-d, H:i:s');
                    $userRank->idRank = $row->idRank;
                    $userRank->save();
                    $result = 1;
                }
            }  
            return $result;
        }
        
        public function getIdFriends()
        {
            $friendsTo = Friend::model()->findAll(array('select' => 'idUserTo','condition'=>'idUserFrom=:idUser and accepted=1','params'=>array(':idUser'=>$this->idUser))); 
            $friendsFrom = Friend::model()->findAll(array('select' => 'idUserFrom','condition'=>'idUserTo=:idUser and accepted=1','params'=>array(':idUser'=>$this->idUser))); 
            $friends = array();
            foreach($friendsTo as $row)
                $friends[] = $row->idUserTo;
            foreach($friendsFrom as $row)
                $friends[] = $row->idUserFrom;
            return $friends;
        }
       
}