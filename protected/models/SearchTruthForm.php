<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class SearchTruthForm extends CFormModel
{
	public $idUser;
	public $idTruth;
	public $idCategory;
    public $level;
    public $username;
    public $order;
    public $limit;
    public $anonymous;
    public $nbChallenge;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
                    array('idUser,idTruth,idCategory,level,order,limit,username','safe'),
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
                  WHERE idTruth = t.idTruth) AS nbComment,
                (SELECT count(idChallenge) AS nbChallenge
                  FROM challenge
                  WHERE idTruth = t.idTruth AND status = 1) AS nbChallenge
                ";
                              
            if(isset($this->idUser) && $this->idUser !== ''){
                $criteria->addCondition(' t.idUser = :idUser ');
                $criteria->params[":idUser"] = $this->idUser;
            }            
            if(isset($this->idTruth) && $this->idTruth !== ''){
                $criteria->addCondition(' t.idTruth = :idTruth ');
                $criteria->params[":idTruth"] = $this->idTruth;
            }            
            if(isset($this->username) && $this->username !== ''){
                $criteria->addCondition(' user.username = :username ');
                $criteria->params[":username"] = $this->username;
            }
            if(isset($this->idCategory) && $this->idCategory !== ''){
                $criteria->addCondition(' t.idCategory = :idCategory ');
                $criteria->params[":idCategory"] = $this->idCategory;
            }
            if(isset($this->anonymous) && $this->anonymous !== ''){
                $criteria->addCondition(' t.anonymous = :anonymous ');
                $criteria->params[":anonymous"] = $this->anonymous;
            }
            if(isset($this->levelMax) && $this->levelMax !== ''){
                $criteria->addCondition(' categoryTruth.level <= :levelMax ');
                $criteria->params[":levelMax"] = $this->levelMax;
            }
            if(isset($this->minDateSubmit) && $this->minDateSubmit !== ''){
                $criteria->addCondition(' t.dateSubmit >= :minDateSubmit ');
                $criteria->params[":minDateSubmit"] = $this->minDateSubmit;
            }
            if(isset($this->maxDateSubmit) && $this->maxDateSubmit !== ''){
                $criteria->addCondition(' t.dateSubmit < :maxDateSubmit ');
                $criteria->params[":maxDateSubmit"] = $this->maxDateSubmit;
            }
            //Truth validated
            $criteria->addCondition(' t.validated = 1 ');
   
            return $criteria;
        }
}