<?php

/**
 * This is the model class for table "truth".
 *
 * The followings are the available columns in table 'truth':
 * @property integer $idTruth
 * @property integer $idCategory
 * @property string $idUser
 * @property string $truth
 * @property string $dateSubmit
 * @property integer $voteUp
 * @property integer $voteDown
 * @property integer $validated
 * @property integer $anonymous
 *
 * The followings are the available model relations:
 * @property Category $idCategory0
 * @property User $idUser0
 * @property VotingIp[] $votingIps
 */
class Truth extends CActiveRecord
{
        //Need for CdbCriterias
        public $maxDateSubmit;
        public $minDateSubmit;
        public $levelMax;
        
        //Need to fetch results
        public $nbFavourite;
        public $nbComment;
        
	/**
	 * Returns the static model of the specified AR class.
	 * @return Truth the static model class
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
		return 'truth';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('truth, dateSubmit', 'required'),
			array('idCategory, idUser, voteUp, validated', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('idTruth, idCategory, idUser, truth, dateSubmit, voteUp, voteDown, validated, anonymous', 'safe', 'on'=>'search'),
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
			'category' => array(self::BELONGS_TO, 'Category', 'idCategory','alias'=>'categoryTruth'),
			'user' => array(self::BELONGS_TO, 'User', 'idUser'),
			'votingDetail' => array(self::HAS_MANY, 'VotingDetail', 'idTruth'),
                        'comments'=> array(self::HAS_MANY,'Comment','idTruth'),
			'userListContents' => array(self::HAS_MANY, 'Userlistcontent', 'idTruth'),
                        'nbComment'=> array(self::HAS_ONE,'Comment','idTruth','select'=>'count(idComment) AS nbComments','group'=>'t.idTruth')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'idTruth' => 'Id Truth',
			'idCategory' => 'Id Category',
			'idUser' => 'Id User',
			'truth' => 'Truth',
			'dateSubmit' => 'Date Submit',
			'voteUp' => 'Vote Up',
			'validated' => 'Validated',
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

		$criteria->compare('idTruth',$this->idTruth);
		$criteria->compare('idCategory',$this->idCategory);
		$criteria->compare('idUser',$this->idUser,true);
		$criteria->compare('truth',$this->truth,true);
		$criteria->compare('dateSubmit',$this->dateSubmit,true);
		$criteria->compare('voteUp',$this->voteUp);
		$criteria->compare('voteDown',$this->voteDown);
		$criteria->compare('validated',$this->validated);
		$criteria->compare('anonymous',$this->anonymous);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        
        /**
         * Returns CDbCriteria initiated using the Model instance params
	 * @return CDbCriteria 
	 */
        public function getCriteria($userFavourite=1)
        {
            $criteria = new CDbCriteria;
            $criteria->with = array('user','category','user.scoreTruth','user.scoreDare');
            $criteria->params = array();
            
            //If we want to get the user favourites Truth and Dares
            $conditionUserFavourite = $userFavourite !== 1 || Yii::app()->user->isGuest ? "" :  " AND UL.idUser = " . Yii::app()->user->getId();
            
            $criteria->select = " t.idTruth, t.idCategory, t.idUser, t.truth, t.dateSubmit, t.voteUp, t.voteDown, t.validated, t.anonymous, 
                (SELECT count(ULC.idTruth) AS nbFavourite
                  FROM userListContent ULC
                  INNER JOIN userList UL ON UL.idUserList = ULC.idUserList 
                  WHERE ULC.idTruth = t.idTruth " . $conditionUserFavourite . " 
                  GROUP BY ULC.idTruth) AS nbFavourite,
                (SELECT count(idComment) AS nbComment
                  FROM comment
                  WHERE idTruth = t.idTruth) AS nbComment
                ";
                        
            if(isset($this->idUser)){
                $criteria->addCondition(' t.idUser = :idUser ');
                $criteria->params[":idUser"] = $this->idUser;
            }         
            if(isset($this->idTruth)){
                $criteria->addCondition(' t.idTruth = :idTruth ');
                $criteria->params[":idTruth"] = $this->idTruth;
            }
            if(isset($this->idCategory)){
                $criteria->addCondition(' t.idCategory = :idCategory ');
                $criteria->params[":idCategory"] = $this->idCategory;
            }
            if(isset($this->levelMax)){
                $criteria->addCondition(' categoryTruth.level <= :levelMax ');
                $criteria->params[":levelMax"] = $this->levelMax;
            }
            if(isset($this->minDateSubmit)){
                $criteria->addCondition(' t.dateSubmit >= :minDateSubmit ');
                $criteria->params[":minDateSubmit"] = $this->minDateSubmit;
            }
            if(isset($this->maxDateSubmit)){
                $criteria->addCondition(' t.dateSubmit < :maxDateSubmit ');
                $criteria->params[":maxDateSubmit"] = $this->maxDateSubmit;
            }
   
            return $criteria;
        }
        
        /**
         * Returns the new score of the Truth
	 * @return Int
	 */
        public function addVote($idUser,$typeVote)
        {         
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
            $votingDetail->idTruth = $this->idTruth;
            $votingDetail->voteDate = date('Y-m-d, H:i:s');
            //VoteType = 1 -> + 1 / VoteType = 0 -> -1
            $votingDetail->voteType = $typeVote == 'up'? 1 : 0;
            $votingDetail->save();                                                    
                    
            return $this->voteUp - $this->voteDown;;
        }
        
       /**
         * Dynamic Scope for Level Filter
	 */
        public function levelFilter($level=1)
        {
            $this->getDbCriteria()->mergeWith(array(
                'condition'=>"categoryTruth.level <= $level",
            ));
            return $this;
        }
        
        /**
         * Dynamic Scope for Category Filter
	 */
        public function category($category)
        {
            if(!($category === null))
                $this->getDbCriteria()->mergeWith(array(
                    'condition'=>"t.idCategory=$category",
                ));
            return $this;
        }
        
        /**
         * Dynamic Scope for selecting only one Truth
	 */
        public function selectTruth($idTruth)
        {
            $this->getDbCriteria()->mergeWith(array(
                'condition'=>"t.idTruth=$idTruth",
            ));
            return $this;
        }
        
        /**
         * Scopes
	 */
        public function scopes()
        {
            return array(
                'validated'=>array(
                    'condition'=>'t.validated=1',
                ),
                'unvalidated'=>array(
                    'condition'=>'t.validated=0',
                )
            );
        }
 }