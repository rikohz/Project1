<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class SearchChallengeTruthForm extends CFormModel
{
	public $idUserTo;
	public $idTruth;
	public $idCategory;
    public $level;
    public $username;
    public $order;
    public $limit;
    public $private;
    public $nbChallenge;
    public $minFinishDate;
    public $maxFinishDate;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
                    array('idUserTo,idTruth,idCategory,level,order,limit,username,private,nbChallenge,minFinishDate,maxFinishDate','safe'),
		);
	}

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
		);
	}
        
        /**
         * Returns CDbCriteria initiated using the Model instance params
	 * @return CDbCriteria 
	 */
        public function getCriteria($userFavourite=1)
        {
            $criteria = new CDbCriteria;
            $criteria->with = array('userTo','truth','truth.category','userTo.scoreTruth','userTo.scoreDare');
            $criteria->params = array();
            
            //If we want to get the user favourites Truths
            $conditionUserFavourite = $userFavourite !== 1 || Yii::app()->user->isGuest ? "" :  " AND UL.idUser = " . Yii::app()->user->getId();
            
            $criteria->select = " t.idChallenge, t.idUserTo, t.idTruth, t.status, t.voteUp, t.voteDown, t.private, t.pictureName, t.pictureExtension, t.answer, 
                (SELECT count(ULC.idTruth) AS nbFavourite
                  FROM userListContent ULC
                  INNER JOIN userList UL ON UL.idUserList = ULC.idUserList 
                  WHERE ULC.idTruth = t.idTruth " . $conditionUserFavourite . " 
                  GROUP BY ULC.idTruth) AS nbFavourite,
                (SELECT count(idComment) AS nbComment
                  FROM comment
                  WHERE idTruth = t.idTruth) AS nbComment,
                (SELECT count(idChallenge) AS nbChallenge
                  FROM challenge
                  WHERE idTruth = t.idTruth AND status = 1) AS nbChallenge
                ";
                     
            $criteria->addCondition(' categoryTruth.level <= :levelMax ');
            $criteria->params[":levelMax"] = Yii::app()->user->getLevel();   
                
            if(isset($this->idUserTo) && $this->idUserTo !== ''){
                $criteria->addCondition(' t.idUserTo = :idUserTo ');
                $criteria->params[":idUserTo"] = $this->idUserTo;
            }            
            if(isset($this->idTruth) && $this->idTruth !== ''){
                $criteria->addCondition(' t.idTruth = :idTruth ');
                $criteria->params[":idTruth"] = $this->idTruth;
            }            
            if(isset($this->username) && $this->username !== ''){
                $criteria->addCondition(' userTo.username = :username ');
                $criteria->params[":username"] = $this->username;
            }
            if(isset($this->idCategory) && $this->idCategory !== ''){
                $criteria->addCondition(' truth.idCategory = :idCategory ');
                $criteria->params[":idCategory"] = $this->idCategory;
            }
            if(isset($this->private) && $this->private !== ''){
                $criteria->addCondition(' t.private = :private ');
                $criteria->params[":private"] = $this->private;
            }
            if(isset($this->minFinishDate) && $this->minFinishDate !== ''){
                $criteria->addCondition(' t.finishDate >= :minFinishDate ');
                $criteria->params[":minFinishDate"] = $this->minfinishDate;
            }
            if(isset($this->maxFinishDate) && $this->maxFinishDate !== ''){
                $criteria->addCondition(' t.finishDate < :maxFinishDate ');
                $criteria->params[":maxFinishDate"] = $this->maxFinishDate;
            }
   
            return $criteria;
        }
}