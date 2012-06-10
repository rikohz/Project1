<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class SearchUserForm extends CFormModel
{
	public $username;
	public $ageMin;
	public $ageMax;
	public $idProvince;
	public $idCity;
	public $idDistrict;
    public $gender;
    public $level;
    public $order;

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
                    array('username,ageMin,ageMax,idProvince,idCity,idDistrict,gender,level,order','safe'),
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
			'gender'=>'Gender',
			'username'=>'Username',
			'ageMin'=>'Age Min.',
			'ageMax'=>'Age Max.',
			'level'=>'Level',
			'location'=>'Location',
		);
	}
        
        /**
         * Returns CDbCriteria initiated using the Model instance params
	 * @return CDbCriteria 
	 */
        public function getCriteria()
        {
            $criteria = new CDbCriteria;
            $criteria->with = array('scoreTruth','scoreDare','level','province','city','district');
            $criteria->params = array();
                    
            if(isset($this->order) && $this->order !== ''){
                $criteria->order = $this->order;
            }   
            else
                $criteria->order = "(scoreTruth.score + scoreDare.score) DESC";
                
            if(isset($this->username) && $this->username !== ''){
                $criteria->addCondition(' t.username = :username ');
                $criteria->params[":username"] = $this->username;
            }               
            if(isset($this->gender) && $this->gender !== ''){
                $criteria->addCondition(' t.gender = :gender ');
                $criteria->params[":gender"] = $this->gender;
            }          
            if(isset($this->idProvince) && $this->idProvince !== ''){
                $criteria->addCondition(' t.idProvince = :idProvince ');
                $criteria->params[":idProvince"] = $this->idProvince;
            }     
            if(isset($this->idCity) && $this->idCity !== ''){
                $criteria->addCondition(' t.idCity = :idCity ');
                $criteria->params[":idCity"] = $this->idCity;
            }  
            if(isset($this->idDistrict) && $this->idDistrict !== ''){
                $criteria->addCondition(' t.idDistrict = :idDistrict ');
                $criteria->params[":idDistrict"] = $this->idDistrict;
            }  
            if(isset($this->ageMin) && $this->ageMin !== ''){
                $criteria->addCondition(' t.birthDate <= :maxBirthDate ');
                $criteria->params[":maxBirthDate"] = date('Y-m-d',strtotime(date("Y-m-d") . " -$this->ageMin year"));
            }  
            if(isset($this->ageMax) && $this->ageMax !== ''){
                $ageMax = $this->ageMax + 1;
                $criteria->addCondition(' t.birthDate >= :minBirthDate ');
                $criteria->params[":minBirthDate"] = date('Y-m-d',strtotime(date("Y-m-d") . " -$ageMax year"));
            }  
            if(isset($this->level) && $this->level !== ''){
                $criteria->addCondition(' level.level = :level ');
                $criteria->params[":level"] = $this->level;
            }  
   
            return $criteria;
        }
}