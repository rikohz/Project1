<?php

class UserController extends MyController
{

//      <!--****************************-->
//      <!-- Functions related to pages -->
//      <!--****************************-->

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
    
	public function actionMyPage()
	{
            //User Score
            $user = User::model()->with('province','city','district','scoreTruth','scoreDare')->findByPk(Yii::app()->user->getId());

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
    
	public function actionUserPage()
	{
            if(isset($_GET['idUser']))
            {
                //Get user infos
                $user = User::model()->with('province','city','district','scoreTruth','scoreDare')->findByPk($_GET['idUser']);
                
                //Get the scores of the User
                $score = array(
                    'scoreTruthVoteIdeas'=>$user->getScoreVoteIdeas('truth'),
                    'scoreTruthChallenges'=>$user->getScoreChallenges('truth'),
                    'scoreTruthVoteChallenges'=>$user->getScoreVoteChallenges('truth'),
                    'scoreDareVoteIdeas'=>$user->getScoreVoteIdeas('dare'),
                    'scoreDareChallenges'=>$user->getScoreChallenges('dare'),
                    'scoreDareVoteChallenges'=>$user->getScoreVoteChallenges('dare')
                );

                $this->render('userPage',array('user'=>$user,'score'=>$score));
            }
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
        
	public function actionMyProfilePicture()
        { 
            $model = User::model()->findByPk(Yii::app()->user->getId());
            $model->setScenario('updateProfilePicture');
            
            if(isset($_POST['User']) && $_POST['validateImage'] == 1)
            {
                $model->verifyCode = $_POST['User']['verifyCode'];
                if($model->updatePicture($_POST['User']['profilePicture'],$_POST['User']['profilePictureExtension']))
                    Yii::app()->user->setFlash('updateProfilePicture','Your Profile Picture has been updated.'); 
            }
            $this->render('myProfilePicture',array('model'=>$model));
        }
        
	public function actionMyInformations()
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
        
	public function actionMyCoins()
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
     
	public function actionMyPassword()
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

	public function actionMyTruths()
	{                
            $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
      
            //Filter and order criterias
            if(isset($_GET['idCategory']))
                Yii::app()->session['idCategory'] = $_GET['idCategory']; 
            $idCategory = Yii::app()->session['idCategory'];
            if(isset($_GET['order']))
                Yii::app()->session['order'] = $_GET['order']; 
            $order = Yii::app()->session['order'];
            
            $this->render('myTruths',array('categories'=>$categories,'order'=>$order,'idCategory'=>$idCategory));
	}

	public function actionUserTruths()
	{                
            if(isset($_GET['idUser']))
            {
                $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');

                //Filter and order criterias
                if(isset($_GET['idCategory']))
                    Yii::app()->session['idCategory'] = $_GET['idCategory']; 
                $idCategory = Yii::app()->session['idCategory'];
                if(isset($_GET['order']))
                    Yii::app()->session['order'] = $_GET['order']; 
                $order = Yii::app()->session['order'];

                $this->render('userTruths',array('categories'=>$categories,'order'=>$order,'idCategory'=>$idCategory,'idUser'=>$_GET['idUser']));	
            }
            else
                throw new CHttpException(404,'The page cannot be found.');
        }

	public function actionMyDares()
	{                
            $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
      
            //Filter and order criterias
            if(isset($_GET['idCategory']))
                Yii::app()->session['idCategory'] = $_GET['idCategory']; 
            $idCategory = Yii::app()->session['idCategory'];
            if(isset($_GET['order']))
                Yii::app()->session['order'] = $_GET['order']; 
            $order = Yii::app()->session['order'];
            
            $this->render('myDares',array('categories'=>$categories,'order'=>$order,'idCategory'=>$idCategory));
	}

	public function actionUserDares()
	{                
            if(isset($_GET['idUser']))
            {
                $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');

                //Filter and order criterias
                if(isset($_GET['idCategory']))
                    Yii::app()->session['idCategory'] = $_GET['idCategory']; 
                $idCategory = Yii::app()->session['idCategory'];
                if(isset($_GET['order']))
                    Yii::app()->session['order'] = $_GET['order']; 
                $order = Yii::app()->session['order'];

                $this->render('userDares',array('categories'=>$categories,'order'=>$order,'idCategory'=>$idCategory,'idUser'=>$_GET['idUser']));	
            }
            else
                throw new CHttpException(404,'The page cannot be found.');
        }

	public function actionUserFriends()
	{             
            if(isset($_GET['idUser']))
            {
                //Filter and order criterias
                if(isset($_GET['idCategory']))
                    Yii::app()->session['idCategoryFriends'] = $_GET['idCategory']; 
                if(isset($_GET['idGender']))
                    Yii::app()->session['idGenderFriends'] = $_GET['idGender']; 

                $friends = Friend::getFriends($_GET['idUser'],1,'username','ASC',Yii::app()->session['idGenderFriends'],Yii::app()->session['idCategoryFriends']);
                $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
                $genders = array('0'=>'Female','1'=>'Male');
                
                $this->render('userFriends',array('categories'=>$categories,'idCategory'=>Yii::app()->session['idCategoryFriends'],'genders'=>$genders,'idGender'=>Yii::app()->session['idGenderFriends'],'friends'=>$friends,'idUser'=>$_GET['idUser']));
            }
            else
                throw new CHttpException(404,'The page cannot be found.');
        }

	public function actionMyFriends()
	{                
            //Filter and order criterias
            if(isset($_GET['idCategory']))
                Yii::app()->session['idCategoryFriends'] = $_GET['idCategory']; 
            if(isset($_GET['idGender']))
                Yii::app()->session['idGenderFriends'] = $_GET['idGender']; 
            
            $friendsRequest = Friend::getFriendRequests(Yii::app()->user->getId());
            $friends = Friend::getFriends(Yii::app()->user->getId(),1,'username','ASC',Yii::app()->session['idGenderFriends'],Yii::app()->session['idCategoryFriends']);
            $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
            $genders = array('0'=>'Female','1'=>'Male');
            
            $this->render('myFriends',array('categories'=>$categories,'idCategory'=>Yii::app()->session['idCategoryFriends'],'genders'=>$genders,'idGender'=>Yii::app()->session['idGenderFriends'],'friends'=>$friends,'friendsRequest'=>$friendsRequest));
	}

	public function actionMyChallenges()
	{                
            //Filter and order criterias
            if(isset($_GET['idCategory']))
                Yii::app()->session['idCategoryChallenge'] = $_GET['idCategory']; 
            if(isset($_GET['idGender']))
                Yii::app()->session['idGenderChallenge'] = $_GET['idGender']; 
            if(isset($_GET['idTypeChallenge']))
                Yii::app()->session['idTypeChallenge'] = $_GET['idTypeChallenge']; 
            if(isset($_GET['idStatusChallenge']))
                Yii::app()->session['idStatusChallenge'] = $_GET['idStatusChallenge']; 
            if(isset($_GET['minDateChallenge']))
                Yii::app()->session['minDateChallenge'] = $_GET['minDateChallenge']; 
            if(isset($_GET['idPrivateStatus']))
                Yii::app()->session['idPrivateStatus'] = $_GET['idPrivateStatus']; 
            if(isset($_GET['idUserFrom']))
                Yii::app()->session['idUserFrom'] = $_GET['idUserFrom']; 
            
            $challenges = Challenge::getChallenges(
                    Yii::app()->user->getId(),Yii::app()->session['idCategoryChallenge'],
                    Yii::app()->session['idGenderChallenge'],
                    Yii::app()->session['idTypeChallenge'],
                    Yii::app()->session['idStatusChallenge'],     
                    Yii::app()->session['minDateChallenge'],
                    Yii::app()->session['idPrivateStatus'],
                    Yii::app()->session['idUserFrom']
            );
            
            $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
            $genders = array('0'=>'Female','1'=>'Male');
            $typeChallenges = array('Truth'=>'Truth','Dare'=>'Dare');
            $statusChallenges = array('0'=>'Waiting','1'=>'Success');
            $period = array(MyFunctions::getFirstDayWeek()=>'Week',MyFunctions::getFirstDayMonth()=>'Month',MyFunctions::getFirstDayYear()=>'Year');
            $privateStatus = array('0'=>'Public','1'=>'Private');
            $userFrom = CHtml::listData(Friend::getFriends(Yii::app()->user->getId()), 'idUser', 'username');
            
            $model = new Challenge;
            
            $this->render(
                    'myChallenges',array(
                        'model'=>$model,
                        'categories'=>$categories,
                        'idCategory'=>Yii::app()->session['idCategoryChallenge'],
                        'genders'=>$genders,
                        'idGender'=>Yii::app()->session['idGenderChallenge'],
                        'typeChallenges'=>$typeChallenges,
                        'idTypeChallenge'=>Yii::app()->session['idTypeChallenge'],
                        'statusChallenges'=>$statusChallenges,
                        'idStatusChallenge'=>Yii::app()->session['idStatusChallenge'],
                        'period'=>$period,
                        'minDateChallenge'=>Yii::app()->session['minDateChallenge'],
                        'privateStatus'=>$privateStatus,
                        'idPrivateStatus'=>Yii::app()->session['idPrivateStatus'],
                        'userFrom'=>$userFrom,
                        'idUserFrom'=>Yii::app()->session['idUserFrom'],
                        'challenges'=>$challenges)
            );
	}

	public function actionUserChallenges()
	{                             
            if(isset($_GET['idUser']))
            {    
                //Filter and order criterias
                if(isset($_GET['idCategory']))
                    Yii::app()->session['idCategoryChallenge'] = $_GET['idCategory']; 
                if(isset($_GET['idGender']))
                    Yii::app()->session['idGenderChallenge'] = $_GET['idGender']; 
                if(isset($_GET['idTypeChallenge']))
                    Yii::app()->session['idTypeChallenge'] = $_GET['idTypeChallenge']; 
                if(isset($_GET['idStatusChallenge']))
                    Yii::app()->session['idStatusChallenge'] = $_GET['idStatusChallenge']; 
                if(isset($_GET['minDateChallenge']))
                    Yii::app()->session['minDateChallenge'] = $_GET['minDateChallenge']; 
                if(isset($_GET['idUserFrom']))
                    Yii::app()->session['idUserFrom'] = $_GET['idUserFrom']; 

                $challenges = Challenge::getChallenges(
                        $_GET['idUser'],
                        Yii::app()->session['idCategoryChallenge'],
                        Yii::app()->session['idGenderChallenge'],
                        Yii::app()->session['idTypeChallenge'],
                        Yii::app()->session['idStatusChallenge'],     
                        Yii::app()->session['minDateChallenge'],
                        0,
                        Yii::app()->session['idUserFrom']
                );

                $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
                $genders = array('0'=>'Female','1'=>'Male');
                $typeChallenges = array('Truth'=>'Truth','Dare'=>'Dare');
                $statusChallenges = array('0'=>'Waiting','1'=>'Success');
                $period = array(MyFunctions::getFirstDayWeek()=>'Week',MyFunctions::getFirstDayMonth()=>'Month',MyFunctions::getFirstDayYear()=>'Year');
                $userFrom = CHtml::listData(Friend::getFriends($_GET['idUser']), 'idUser', 'username');

                $model = new Challenge;

                $this->render(
                        'userChallenges',array(
                            'model'=>$model,
                            'categories'=>$categories,
                            'idCategory'=>Yii::app()->session['idCategoryChallenge'],
                            'genders'=>$genders,
                            'idGender'=>Yii::app()->session['idGenderChallenge'],
                            'typeChallenges'=>$typeChallenges,
                            'idTypeChallenge'=>Yii::app()->session['idTypeChallenge'],
                            'statusChallenges'=>$statusChallenges,
                            'idStatusChallenge'=>Yii::app()->session['idStatusChallenge'],
                            'period'=>$period,
                            'minDateChallenge'=>Yii::app()->session['minDateChallenge'],
                            'userFrom'=>$userFrom,
                            'idUserFrom'=>Yii::app()->session['idUserFrom'],
                            'challenges'=>$challenges,
                            'idUser'=>$_GET['idUser'])
                );
            }
            else
                throw new CHttpException(404,'The page cannot be found.');
	}

	public function actionMyLists()
	{               
            $criteria = new CDbCriteria;
            $criteria->addCondition('t.idUser = :idUser');
            $criteria->params = array(':idUser'=>Yii::app()->user->getId());
            if(isset($_GET['public']))
                Yii::app()->session['myListsPublic'] = $_GET['public']; 
            if(isset(Yii::app()->session['myListsPublic']) && Yii::app()->session['myListsPublic'] !== "")
            {
                $criteria->addCondition('t.public = :public');
                $criteria->params[':public'] = Yii::app()->session['myListsPublic'];
            }
            
            $userLists = Userlist::model()->with('userListContents','userListContents.truth','userListContents.dare','userListContents.truth.category','userListContents.dare.category')->findAll($criteria);
            $friends = CHtml::listData(Friend::getFriends(Yii::app()->user->getId()),'idUser','username');
            
            $this->render('myLists',array('userlists'=>$userLists,'friends'=>$friends,'public'=>Yii::app()->session['myListsPublic']));
	}

	public function actionUserLists()
	{                   
            if(isset($_GET['idUser']))
            {       
                $criteria = new CDbCriteria;
                $criteria->addCondition('t.idUser = :idUser');
                $criteria->params = array(':idUser'=>$_GET['idUser']);
                $criteria->addCondition('t.public = :public');
                $criteria->params[':public'] = 1;

                $userLists = Userlist::model()->with('userListContents','userListContents.truth','userListContents.dare','userListContents.truth.category','userListContents.dare.category')->findAll($criteria);
                $friends = CHtml::listData(Friend::getFriends(Yii::app()->user->getId()),'idUser','username');

                $this->render('userLists',array('userlists'=>$userLists,'friends'=>$friends,'idUser'=>$_GET['idUser']));	
            }
            else
                throw new CHttpException(404,'The page cannot be found.');
	}

	public function actionSearchUser()
	{      
            $model = new SearchUserForm;
            if(Yii::app()->request->isAjaxRequest) 
            { 
                if(isset($_POST['SearchUserForm']))
                {
                    if($_POST['SearchUserForm']['username'] !== '')
                        $model->username = $_POST['SearchUserForm']['username'];
                    else
                        $model->attributes = $_POST['SearchUserForm'];
                    $criteria = $model->getCriteria();
                    
                    //Page manager
                    $count = User::model()->count($criteria); 
                    $pages = new CPagination($count);
                    $pages->pageSize = 10;
                    $pages->applyLimit($criteria);
                    
                    $searchResult = User::model()->findAll($criteria);
                    
                    $this->renderPartial('_searchUserResult',array('searchResult'=>$searchResult,'pages'=>$pages));
                }
            } 
            else 
            {  
                $provinces = Province::model()->findAll();
                $provinces = CHtml::listData($provinces,'idProvince','name');

                $this->render('searchUser',array('model'=>$model,'provinces'=>$provinces));
            }         
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

	public function actionAddUserList()
	{
            if(isset($_POST['name'],$_POST['public']))
            {  
                $userList = new Userlist;
                $userList->idUser = Yii::app()->user->getId();
                $userList->createDate = date('Y-m-d, H:i:s');
                $userList->public = $_POST['public'] == 'true' ? 1 : 0;
                $userList->name = $_POST['name'];
                if($userList->save())
                {
                    echo "SUCCESS";
                    return;
                }
            }
            
            echo "ERROR";
            return;
	}

	public function actionDeleteUserList()
	{
            if(isset($_POST['idUserList']))
            {  
                $userList = Userlist::model()->findByPk($_POST['idUserList']);
                //Verify that the person who deletes the content is its owner
                if($userList->idUser == Yii::app()->user->getId())
                {
                    if($userList->delete())
                    {
                        echo "SUCCESS";
                        return;
                    }
                }
            }
            
            echo "ERROR";
            return;
	}

	public function actionDeleteUserListContent()
	{
            if(isset($_POST['idUserListContent']))
            {  
                $userListContent = Userlistcontent::model()->with('userList')->findByPk($_POST['idUserListContent']);
                //Verify that the person who deletes the content is its owner
                if($userListContent->userList->idUser == Yii::app()->user->getId())
                {
                    if($userListContent->delete())
                    {
                        echo "SUCCESS";
                        return;
                    }
                }
            }
            
            echo "ERROR";
            return;
	}

	public function actionSendFriendRequest()
	{
            if(isset($_POST['idUser']))
            {  
                if(!Friend::areFriendsOrFriendRequest(Yii::app()->user->getId(),$_POST['idUser']))
                {                
                    $friendRequest = new Friend;
                    $friendRequest->idUserFrom = Yii::app()->user->getId();
                    $friendRequest->idUserTo = $_POST['idUser'];
                    $friendRequest->createDate = date('Y-m-d, H:i:s');
                    if($friendRequest->save())
                        echo "A friend request has been sent!";
                    return;
                }
                echo "You are already friends or have a Friend Request processing"  ;
                return;
            }
	}

	public function actionAcceptFriendRequest()
	{
            if(isset($_POST['idUser']))
            {  
                $friendRequest = Friend::model()->findByAttributes(array('idUserTo'=>Yii::app()->user->getId(),'idUserFrom'=>$_POST['idUser'],'accepted'=>0));
                if($friendRequest !== null)
                {                
                    $friendRequest->accepted = 1;
                    if($friendRequest->save())
                        echo "Accepted!";
                }
            }
	}

	public function actionDeclineFriendRequest()
	{
            if(isset($_POST['idUser']))
            {  
                $friendRequest = Friend::model()->findByAttributes(array('idUserTo'=>Yii::app()->user->getId(),'idUserFrom'=>$_POST['idUser'],'accepted'=>0));
                if($friendRequest !== null)
                {                
                    $friendRequest->accepted = 2;
                    if($friendRequest->save())
                        echo "Declined!";
                }
            }
	}

	public function actionSendChallenge()
	{
            if(isset($_POST['idUser'], $_POST['private'], $_POST['comment']) 
                    && (isset($_POST['idTruth']) || isset($_POST['idDare']))
                    && Friend::areFriendsOrFriendRequest($_POST['idUser'],Yii::app()->user->getId(),1) == 1
              )
            {  
                $type = isset($_POST['idTruth']) ? "idTruth" : "idDare";
                $id = isset($_POST['idTruth']) ? $_POST['idTruth'] : $_POST['idDare'];
                
                //If the challenge is public and it already exists
                if($_POST['private'] == 'false' && Challenge::model()->exists("idUserTo = :idUserTo AND $type = :id",array(':idUserTo'=>$_POST['idUser'],':id'=>$id)))
                {
                    echo 'ALREADY_EXISTS';
                    return;
                }

                $challenge = new Challenge;
                $challenge->idUserFrom = Yii::app()->user->getId();
                $challenge->idUserTo = $_POST['idUser'];
                $challenge->$type = $id;
                $challenge->createDate = date('Y-m-d, H:i:s');
                $challenge->private = $_POST['private'] == 'true' ? 1 : 0;
                $challenge->comment = $_POST['comment'];
                if($challenge->save())
                    echo "SUCCESS";
            }
	}

	public function actionAcceptChallenge()
	{
            if(isset($_POST['idChallenge'], $_POST['type']))
            {  
                $challenge = Challenge::model()->findByPk($_POST['idChallenge']);
                
                if($_POST['type'] == 'Truth' && isset($_POST['answer']) && $challenge->idUserTo == Yii::app()->user->getId())
                {
                    $challenge->answer = $_POST['answer'];
                    $challenge->finishDate = date('Y-m-d, H:i:s');
                    $challenge->status = 1;
                    if($challenge->save())
                        echo "SUCCESS";
                }
                if($_POST['type'] == 'Dare' && isset($_POST['pictureName'],$_POST['pictureExtension'],$_POST['answer']) && $challenge->idUserTo == Yii::app()->user->getId())
                {
                    $challenge->addPicture($_POST['pictureName'],$_POST['pictureExtension']);
                    $challenge->answer = $_POST['answer'];
                    $challenge->finishDate = date('Y-m-d, H:i:s');
                    $challenge->status = 1;
                    $challenge->pictureName = $_POST['pictureName'];
                    $challenge->pictureExtension = $_POST['pictureExtension'];
                    if($challenge->save())
                        echo "SUCCESS";
                }
            }
	}

	public function actionDeleteChallenge()
	{
            if(isset($_POST['idChallenge']))
            {  
                $challenge = Challenge::model()->findByPk($_POST['idChallenge']);
                if($challenge->idUserTo == Yii::app()->user->getId())
                {
                    //We change the status to decline
                    $challenge->status = 2;
                    $challenge->save();
                    //We delete the associated votes
                    $votingDetails = VotingDetail::model()->deleteAllByAttributes(array('idChallenge'=>$_POST['idChallenge']));
                    echo "SUCCESS";
                }
            }
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
				'actions'=>array('login','error','logout','register','captcha','updateDistricts','updateCities','searchUser', 'userPage','userLists','userTruths','userDares','userFriends','userChallenges'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('myPage', 'myInformations','myPassword', 'myCoins','deleteCoin','myProfilePicture','myLists','mySettings','myTruths','myDares','sendFriendRequest','myFriends','acceptFriendRequest','declineFriendRequest','myChallenges','acceptChallenge','deleteChallenge','sendChallenge','addUserList','deleteUserList','deleteUserListContent'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}   
        
}