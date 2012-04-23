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
                        array('profilePicture', 'file', 'types'=>'jpg, jpeg, gif, png', 'maxSize'=> '2097152', 'tooLarge'=>'This file is too big...', 'wrongType'=>'Wrong format of file...', 'allowEmpty'=>true,'on'=>'register'),
                        array('idProvince, idCity, idDistrict, profilePictureExtension', 'safe','on'=>'register'),
			array('serialNumber', 'exist','message' => "Sorry, this Serial Number does not exist",'className'=>'verifIdentity','on'=>'register'),
                    
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
			'scoreDare' => array(self::HAS_ONE, 'Scoredare', 'idUser','select'=>'CASE WHEN scoreDare.score IS NULL THEN 0 ELSE scoreDare.score END AS score'),
			'scoreTruth' => array(self::HAS_ONE, 'Scoretruth', 'idUser','select'=>'CASE WHEN scoreTruth.score IS NULL THEN 0 ELSE scoreTruth.score END AS score'),
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
        
        //Verify that the Coin Password is the right one according to the Coin Serial Number
        public function verifyCoinPassword($attribute,$params)
	{
                    if(!VerifIdentity::model()->exists('serialNumber = :serialNumber AND verifCode = :verifCode',array(':serialNumber'=>$this->serialNumber,':verifCode'=>$this->verifCode)))
                            $this->addError('verifCode','Incorrect validation Coin Password.');

	}
        
        //Verify that the Serial Number is not linked with any other person yet
        public function verifyUnicitySerialNumber($attribute,$params)
	{
                    $verifIdentity = VerifIdentity::model()->findByPk($this->serialNumber);
                    if($verifIdentity != null)
                    {
                        if(!($verifIdentity->idUser === null))
                            $this->addError('serialNumber','This serial Number is already used.');
                    }
                    
        }
        
        public function verifyPassword($attribute,$params)
        {
            if(!$this->hasErrors())
            {
                if(User::model()->exists('username = :username AND password = :password',array(':username'=>$this->username,':password'=>$this->password)))
                    $this->addError('password','Incorrect password');
            }
        }
        
        public function beforeSave() {
            if(($this->getScenario() === 'register') || ($this->getScenario() === 'changePassword'))    
                $this->password = md5($this->password);
            return true;
        }

        //Get the highest coin level of the user
        public function getLevel()
	{
            $criteria = new CDbCriteria;
            $criteria->select = 't.level';
            $criteria->condition = ' idUser = ' . $this->idUser;
            $criteria->order = ' t.level DESC ';
            $criteria->limit = 1;
            
            $userCoins = VerifIdentity::model()->findAll($criteria);
                    
            if($userCoins != null)
                return $userCoins[0]['level'];
            
            return 1;                              
        }
        
        //Get the Truth or Dare score of the user
       public function getScore($type=null)
       {
           $scoreWeek = 0;
           $scoreMonth = 0;
           $scoreYear = 0;
           $scoreTotal = 0;
           
           //Initialize dates
           $dayOfWeek = CTimestamp::getDayofWeek(date('Y'),date('n'),date('d'));
           $minDateSubmitWeek = date('Y-m-d',strtotime(date("Y-m-d") . " -" . ($dayOfWeek -1) . "day")); 
           $minDateSubmitMonth = date('Y-m-d',strtotime(date("Y-m-d") . " -" . (date('d') -1) . "day")); 
           $cDateFormatter = new CDateFormatter(Yii::app()->language);
           $dayOfYear = $cDateFormatter->format("D",date('Y-m-d'));
           $minDateSubmitYear = date('Y-m-d',strtotime(date("Y-m-d") . " -" . ($dayOfYear) . "day"));
           
           //Initialize general criterias
           $criteria = new CDbCriteria;
           $criteria->params = array(':idUser'=>$this->idUser);
           
           //Truth Related Score
           if($type == null || $type == 'truth')
           {
               $criteria->group = " truth.idUser ";
               $criteria->select = " SUM(CASE WHEN t.voteDate >= '$minDateSubmitWeek' THEN (CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) END) AS scoreTruthWeek, ";
               $criteria->select .= " SUM(CASE WHEN t.voteDate >= '$minDateSubmitMonth' THEN (CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) END) AS scoreTruthMonth, ";
               $criteria->select .= " SUM(CASE WHEN t.voteDate >= '$minDateSubmitYear' THEN (CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) END) AS scoreTruthYear, ";
               $criteria->select .= " SUM(CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) AS scoreTruth ";
               $scoreTruth = VotingDetail::model()->with('truth')->find($criteria);
               $scoreTotal += $scoreTruth['scoreTruth'] === null? 0 : $scoreTruth->scoreTruth;
               $scoreWeek += $scoreTruth['scoreTruthWeek'] === null? 0 : $scoreTruth->scoreTruthWeek;
               $scoreMonth += $scoreTruth['scoreTruthMonth'] === null? 0 : $scoreTruth->scoreTruthMonth;
               $scoreYear += $scoreTruth['scoreTruthYear'] === null? 0 : $scoreTruth->scoreTruthYear;
           }
           
           //Dare Related Score
           if($type == null || $type == 'dare')
           {
               $criteria->group = " dare.idUser ";
               $criteria->select = " SUM(CASE WHEN t.voteDate >= '$minDateSubmitWeek' THEN (CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) END) AS scoreDareWeek, ";
               $criteria->select .= " SUM(CASE WHEN t.voteDate >= '$minDateSubmitMonth' THEN (CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) END) AS scoreDareMonth, ";
               $criteria->select .= " SUM(CASE WHEN t.voteDate >= '$minDateSubmitYear' THEN (CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) END) AS scoreDareYear, ";
               $criteria->select .= " SUM(CASE t.voteType WHEN 1 THEN 1 ELSE -1 END) AS scoreDare ";
               $scoreDare = VotingDetail::model()->with('dare')->find($criteria);
               $scoreTotal += $scoreDare['scoreDare'] === null? 0 : $scoreDare->scoreDare;
               $scoreWeek += $scoreDare['scoreDareWeek'] === null? 0 : $scoreDare->scoreDareWeek;
               $scoreMonth += $scoreDare['scoreDareMonth'] === null? 0 : $scoreDare->scoreDareMonth;
               $scoreYear += $scoreDare['scoreDareYear'] === null? 0 : $scoreDare->scoreDareYear;
           }
           
           return array('scoreTotal'=>$scoreTotal,'scoreWeek'=>$scoreWeek,'scoreMonth'=>$scoreMonth,'scoreYear'=>$scoreYear);
       }
        
        //Get the Truth or Dare score of the user
       public static function getRanking($type=null,$period=null,$idCategory=null,$gender=null)
       {
           switch($period)
           {
               case null:
                    $minDateSubmit = "2012-01-01";
                    break;
               case 'week':
                    $dayOfWeek = CTimestamp::getDayofWeek(date('Y'),date('n'),date('d'));
                    $minDateSubmit = date('Y-m-d',strtotime(date("Y-m-d") . " -" . ($dayOfWeek -1) . "day")); 
                    break;
               case 'month':
                    $minDateSubmit = date('Y-m-d',strtotime(date("Y-m-d") . " -" . (date('d') -1) . "day")); 
                    break;
               case 'year':
                    $cDateFormatter = new CDateFormatter(Yii::app()->language);
                    $dayOfYear = $cDateFormatter->format("D",date('Y-m-d'));
                    $minDateSubmit = date('Y-m-d',strtotime(date("Y-m-d") . " -" . ($dayOfYear) . "day"));
                    break;
           }   
           
           $query = " SELECT US.username, IFNULL(TR.ScoreTruth,0) AS ScoreTruth, IFNULL(DA.ScoreDare,0) AS ScoreDare, (IFNULL(TR.ScoreTruth,0) + IFNULL(DA.ScoreDare,0)) AS Score ";
           $query .= " FROM user US ";
           $query .= " LEFT JOIN ";
           $query .= " (SELECT idUser, SUM(voteUp - voteDown) AS ScoreTruth ";
           $query .= " FROM truth ";
           $query .= " WHERE dateSubmit >= '$minDateSubmit' ";
           $query .= $idCategory == NULL ? "" : " AND idCategory = $idCategory ";
           $query .= " GROUP BY idUser) TR ON TR.idUser = US.idUser ";
           $query .= " LEFT JOIN ";
           $query .= " (SELECT idUser, SUM(voteUp - voteDown) AS ScoreDare ";
           $query .= " FROM dare ";
           $query .= " WHERE dateSubmit >= '$minDateSubmit' ";
           $query .= $idCategory == NULL ? "" : " AND idCategory = $idCategory ";
           $query .= " GROUP BY idUser) DA ON DA.idUser = US.idUser ";
           $query .= " WHERE 1 ";
           $query .= $gender == NULL ? "" : " AND US.gender = $gender ";
           $query .= $type == 'truth' ? " ORDER BY IFNULL(TR.ScoreTruth,0) DESC " : "";
           $query .= $type == 'dare' ? " ORDER BY IFNULL(DA.ScoreDare,0) DESC " : "";
           $query .= $type == NULL ? " ORDER BY (IFNULL(TR.ScoreTruth,0) + IFNULL(DA.ScoreDare,0)) DESC " : "";
           $query .= " LIMIT 0,10 ";
           
           $result = Yii::app()->db->createCommand($query)->queryAll();                 
           return $result;
       }

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
                    
                    $result = true;               
                }
                else
                    $result = "Problem during file transfer";     
            }
            return $result;
        }
       
}