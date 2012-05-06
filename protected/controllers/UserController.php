<?php

class UserController extends MyController
{

//      <!--****************************-->
//      <!-- Functions related to pages -->
//      <!--****************************-->
    
	public function actionMyPage()
	{
            //User Score
            $user = User::model()->with('province','city','district')->findByPk(Yii::app()->user->getId());

            //Get the scores of the User
            $score = array(
                'scoreTruthVoteIdeas'=>$user->getScoreVoteIdeas('truth'),
                'scoreTruthChallenges'=>$user->getScoreChallenges('truth'),
                'scoreTruthVoteChallenges'=>$user->getScoreVoteChallenges('truth'),
                'scoreDareVoteIdeas'=>$user->getScoreVoteIdeas('dare'),
                'scoreDareChallenges'=>$user->getScoreChallenges('dare'),
                'scoreDareVoteChallenges'=>$user->getScoreVoteChallenges('dare')
            );
             
            $this->render('myPage',array('user'=>$user,'score'=>$score));
	}
        
	public function actionRegister()
        {
            $model = new User('register');
            
            $provinces = Province::model()->findAll();
            $provinces = CHtml::listData($provinces,'idProvince','name');
            
            if(isset($_POST['User']))
            {
                $model->attributes = $_POST['User'];
                
                if($model->createUser())
                {               
                    //Log in the new user
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
            $model->setScenario('updateProfilePicture');
            
            if(isset($_POST['User']) && $_POST['validateImage'] == 1)
            {
                $model->verifyCode = $_POST['User']['verifyCode'];
                if($model->updatePicture($_POST['User']['profilePicture'],$_POST['User']['profilePictureExtension']))
                    Yii::app()->user->setFlash('updateProfilePicture','Your Profile Picture has been updated.'); 
            }
            $this->render('updateProfilePicture',array('model'=>$model));
        }
        
	public function actionUpdateUser()
        {          
            $model = User::model()->findByPk(Yii::app()->user->getId());
            $model->conf_email = $model->email;
            $model->setScenario('updateUser');
            
            if(isset($_POST['User']))
            {                
                $model->attributes = $_POST['User'];
                
                if($model->save())
                    Yii::app()->user->setFlash('updateUserInformations','Your informations have been updated.');        
            }
            
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
        
	public function actionMyLists()
        {        
            $model = UserList::model()->findAllByAttributes(array('idUser'=>Yii::app()->user->getId()));
            $this->render('myLists',array('model'=>$model));
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

        
        
//      <!--********************************-->
//      <!-- Functions not related to pages -->
//      <!--********************************-->

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
                    //User can not delete coin if only one registered
                    if(VerifIdentity::model()->count('idUser = :idUser',array(':idUser'=>Yii::app()->user->getId())) > 1)
                    {   
                        $verifIdentity->idUser = null;
                        if($verifIdentity->save())
                            return;
                    }
                    echo "You need at least one coin!";
                    return;
                }
            }
            
            echo "ERROR";
            return;
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

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('login','error','logout','register','captcha','updateDistricts','updateCities'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('myPage', 'updateUser','changePassword', 'updateCoins','deleteCoin','updateProfilePicture','myLists','mySettings'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}   
        
}