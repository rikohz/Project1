<?php

/**
 * This is the model class for table "verifIdentity".
 *
 * The followings are the available columns in table 'verifIdentity':
 * @property string $serialNumber
 * @property string $verifCode
 */
class VerifIdentity extends CActiveRecord
{
        
	/**
	 * Returns the static model of the specified AR class.
	 * @return VerifIdentity the static model class
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
		return 'verifIdentity';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('serialNumber, verifCode', 'required'),
			array('serialNumber, verifCode', 'length', 'max'=>12),
			array('serialNumber, verifCode', 'numerical'),
			array('serialNumber', 'exist','message' => "Sorry, this Serial Number does not exist",'className'=>'verifIdentity'),
			array('serialNumber', 'verifyUnicitySerialNumber','on'=>'addCoin'),
			array('verifCode', 'verifyCoinPassword'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('serialNumber, verifCode', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'serialNumber' => 'Serial Number',
			'verifCode' => 'Verif Code',
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

		$criteria->compare('serialNumber',$this->id,true);
		$criteria->compare('verifCode',$this->verifCode,true);

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
        
        
}