<?php

class UserController extends MyController
{

	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
                                'testLimit'=>5,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	public function actionLogin()
	{
		$model=new LoginForm;

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
                    $model->attributes=$_POST['LoginForm'];
                    if($model->validate() && $model->login())
                            $this->redirect(Yii::app()->user->returnUrl);   
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	public function actionDeleteCoin()
	{
            if(isset($_POST['serialNumber']))
            {
                $verifIdentity = VerifIdentity::model()->findByPk($_POST['serialNumber']);
                //Verify that the person who deletes the coin is its owner
                if($verifIdentity->idUser == Yii::app()->user->getId())
                {
                    $verifIdentity->idUser = null;
                    if($verifIdentity->save())
                        return;
                }
            }
            
            return "ERROR";
	}

	public function actionMyPage()
	{
            $user = User::model()->with('province','city','district')->findByPk(Yii::app()->user->getId());
            $scoreTruth = $user->getScore('truth');
            $scoreDare = $user->getScore('dare');
            
            $this->render('myPage',array('user'=>$user,'scoreTruth'=>$scoreTruth,'scoreDare'=>$scoreDare));
	}
        
	public function actionRegister()
        {
            $model = new User('register');
            
            $provinces = Province::model()->findAll();
            $provinces = CHtml::listData($provinces,'idProvince','name');
            
            if(isset($_POST['User']))
            {
                $model->attributes = $_POST['User'];
                $model->registrationDate = date('Y-m-d');
                $model->validation = uniqid();
                $model->profilePicture = $_POST['tempName'];
                $model->profilePictureExtension = $_POST['extension'];
                
                if($model->save())
                {               
                    //On recupere l'Id du nouvel utilisateur et on vient la remplir dansla table verifIdentity
                    $idUser = Yii::app()->db->getLastInsertId();
                    $verifIdentity = VerifIdentity::model()->findByPk($model->serialNumber);
                    $verifIdentity->idUser = $idUser;
                    $verifIdentity->save();     
                    
                    //Upload of Profile Picture
                    if($model->profilePicture !== "default")
                        $result = $model->addPicture($_POST['tempName'], $_POST['extension']);

                    //On logue le nouvel utilisateur
                    $modelLoginForm = new LoginForm;
                    $modelLoginForm->username = $model->username;
                    $modelLoginForm->password = $model->conf_password; //because password has been md5
                    if($modelLoginForm->login())
                        $this->redirect(Yii::app()->user->returnUrl);   
                }
            }
            $this->render('register',array('model'=>$model,'provinces'=>$provinces));
        }
        
	public function actionUpdateProfilePicture()
        { 
            $model = User::model()->findByPk(Yii::app()->user->getId());
            
            if(isset($_POST['User']))
            {
                $model->attributes = $_POST['User'];
                $oldProfilePictureName = $model->profilePicture;
                $oldProfilePictureExtension = $model->profilePictureExtension;
                $model->profilePicture = $_POST['validateImage'] == 1? $_POST['tempName'] : $model->profilePicture;
                $model->profilePictureExtension = $_POST['validateImage'] == 1? $_POST['extension'] : $model->profilePictureExtension;
                 
                //On ajoute la photo
                if($_POST['validateImage'] == 1)
                {
                    $result = $model->addPicture($_POST['tempName'], $_POST['extension'],$oldProfilePictureName,$oldProfilePictureExtension);
                    $model->addError('profilePicture',$result);
                }
                if(isset($result) && $result == true)
                    if($model->save())
                            Yii::app()->user->setFlash('updateProfilePicture','Your Profile Picture has been updated.');
                        else     
                            $model->addError('profilePicture','No picture selected or problem during the transfer'); 
                    else   
                        $model->addError('profilePicture','No picture selected or problem during the transfer');
            }
            $this->render('updateProfilePicture',array('model'=>$model));
        }
        
	public function actionUpdateUser()
        {          
            $model = User::model()->findByPk(Yii::app()->user->getId());
            $model->setScenario('updateUser');
            
            //Province
            $provinces = Province::model()->findAll();
            $provinces = CHtml::listData($provinces,'idProvince','name');
            
            //City
            $cities = array();
            if(isset($model->idProvince))
            {
                $cities = City::model()->findAllByAttributes(array('idProvince'=>$model->idProvince));
                $cities = CHtml::listData($cities,'idCity','name');
            }
            
            //District
            $districts = array();
            if(isset($model->idCity))
            {
                $districts = District::model()->findAllByAttributes(array('idCity'=>$model->idCity));
                $districts = CHtml::listData($districts,'idDistrict','name');
            }
            
            if(isset($_POST['User']))
            {                
                $model->attributes = $_POST['User'];
                
                if($model->save())
                    Yii::app()->user->setFlash('updateUserInformations','Your informations have been updated.');        
            }
            else  
                $model->conf_email = $model->email;
            
            
            
            $this->render('updateUser',array('model'=>$model,'provinces'=>$provinces,'cities'=>$cities,'districts'=>$districts));
        }
        
	public function actionUpdateCoins()
        {         
            $model = new VerifIdentity('addCoin');
            
            if(isset($_POST['VerifIdentity']))
            {            
                $model = VerifIdentity::model()->findByPk($_POST['VerifIdentity']['serialNumber']);
                $model->idUser = Yii::app()->user->getId(); 
                $model->verifCode = $_POST['VerifIdentity']['verifCode']; 
                $model->setScenario('addCoin');
                    
                if($model->save())
                {
                    $model->serialNumber = null;
                    $model->verifCode = null;
                }
            }           
            
            $coins = VerifIdentity::model()->findAllByAttributes(array('idUser'=>Yii::app()->user->getId()));
            $this->render('updateCoins',array('model'=>$model,'coins'=>$coins));
        }
        
	public function actionMySettings()
        {         
            $this->render('mySettings');
        }
        
	public function actionFavourite()
        {        
            $model = UserList::model()->findAllByAttributes(array('idUser'=>Yii::app()->user->getId()));
            $this->render('favourite',array('model'=>$model));
        }
     
	public function actionChangePassword()
        {      
            $model = User::model()->findByPk(Yii::app()->user->getId());
            $model->setScenario('changePassword');
            
            $oldPassword = $model->password;
            $model->password = '';
            
            if(isset($_POST['User']))
            {                
                $model->attributes = $_POST['User'];
                
                if(md5($model->password) == $oldPassword)
                {
                    $model->password = $model->newPassword;
                    if($model->save())
                        Yii::app()->user->setFlash('changePassword','Your password has been changed.');        
                }
                else
                    $model->addError('password','Incorrect password');                  
            }          
            
            $this->render('changePassword',array('model'=>$model));
        }

	public function actionUpdateCities()
	{
            //Cities
            $data = City::model()->findAll('idProvince=:idProvince', array(':idProvince'=>(int) $_POST['idProvince']));
            $data = CHtml::listData($data,'idCity','name');
            $dropDownCities = "<option value=''>Select City</option>"; 
            foreach($data as $value=>$name)
                $dropDownCities .= CHtml::tag('option', array('value'=>$value),CHtml::encode($name),true);
            
            //District
            $dropDownDistricts = "<option value=''>Select District</option>";

            // return data (JSON formatted)
            echo CJSON::encode(array(
              'dropDownCities'=>$dropDownCities,
              'dropDownDistricts'=>$dropDownDistricts
            ));
	}

	public function actionUpdateDistricts()
	{
            $data = District::model()->findAll('idCity=:idCity', array(':idCity'=>(int) $_POST['idCity']));
            $data = CHtml::listData($data,'idDistrict','name');
            echo "<option value=''>Select District</option>";
            foreach($data as $value=>$name)
                echo CHtml::tag('option', array('value'=>$value),CHtml::encode($name),true);
	}

         
	/**
	 * @return array action filters
	 */
        
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('login','error','logout','register','captcha','updateDistricts','updateCities'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('myPage', 'updateUser','changePassword', 'updateCoins','deleteCoin','updateProfilePicture','favourite','mySettings'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	} 
    
    
        
}