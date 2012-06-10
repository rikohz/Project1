<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class SearchDareForm extends CFormModel
{
	public $idUser;
	public $idDare;
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
                    array('idUser,idDare,idCategory,level,order,limit,username','safe'),
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
            
            $criteria->select = " t.idDare, t.idCategory, t.idUser, t.dare, t.dateSubmit, t.voteUp, t.voteDown, t.validated, t.anonymous, 
                (SELECT count(ULC.idDare) AS nbFavourite
                  FROM userListContent ULC
                  INNER JOIN userList UL ON UL.idUserList = ULC.idUserList 
                  WHERE ULC.idDare = t.idDare " . $conditionUserFavourite . " 
                  GROUP BY ULC.idDare) AS nbFavourite,
                (SELECT count(idComment) AS nbComment
                  FROM comment
                  WHERE idDare = t.idDare) AS nbComment,
                (SELECT count(idChallenge) AS nbChallenge
                  FROM challenge
                  WHERE idDare = t.idDare AND status = 1) AS nbChallenge
                ";
                        
                              
            if(isset($this->idUser) && $this->idUser !== ''){
                $criteria->addCondition(' t.idUser = :idUser ');
                $criteria->params[":idUser"] = $this->idUser;
            }             
            if(isset($this->idDare) && $this->idDare !== ''){
                $criteria->addCondition(' t.idDare = :idDare ');
                $criteria->params[":idDare"] = $this->idDare;
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
                $criteria->addCondition(' categoryDare.level <= :levelMax ');
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
            //Dare validated
            $criteria->addCondition(' t.validated = 1 ');
   
            return $criteria;
        }
}