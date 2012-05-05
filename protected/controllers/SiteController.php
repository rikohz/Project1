<?php

class SiteController extends MyController
{
    
//      <!--****************************-->
//      <!-- Functions related to pages -->
//      <!--****************************-->
      
	public function actionIndex()
	{              
                $generalRanking = MyFunctions::getRanking(NULL,'year');
                $truthRanking = MyFunctions::getRanking('truth','year');
                $dareRanking = MyFunctions::getRanking('dare','year');    
                
                $this->render('index',array('generalRanking'=>$generalRanking,'truthRanking'=>$truthRanking,'dareRanking'=>$dareRanking));
	}

	public function actionRanking()
	{        
                $this->render('ranking');
	}
              
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}
                
	public function actionSubmitIdea()
	{            
            $model = new SubmitIdeaForm;          
            $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
            $truthOrDare = array('Truth'=>'Truth','Dare'=>'Dare');
            $model->username = Yii::app()->user->name;
                        
            if(isset($_POST['SubmitIdeaForm']))
            {
                $model->attributes = $_POST['SubmitIdeaForm'];

                if($model->validate())
                {
                    $type = strtolower($model->truthOrDare);
                    $modelTruthOrDare = new $type;
                    $modelTruthOrDare->idCategory = $model->idCategory;
                    $modelTruthOrDare->idUser = Yii::app()->user->getId();
                    $modelTruthOrDare->dateSubmit = date('Y-m-d, H:i:s');
                    $modelTruthOrDare->anonymous = $model->anonymous;
                    $modelTruthOrDare->$type = $model->idea;
                    
                    if($modelTruthOrDare->save())
                        Yii::app()->user->setFlash('submitIdea','Your idea has been submited and will be displayed on the website after validation of the Truth Or Dare Team!');                 
                    else
                        Yii::app()->user->setFlash('submitIdea','Sorry, there was a problem during the saving process..! Please try again...');                           
                }
            } 
              $this->render('submitIdea',array('model'=>$model,'truthOrDare'=>$truthOrDare,'categories'=>$categories));   
	}
          
        public function actionComment()
	{     
            if(isset($_GET['idTruth']) || isset($_GET['idDare'])) 
            {
                $model = new Comment;
                $model->idTruth = isset($_GET['idTruth'])? $_GET['idTruth'] : null;
                $model->idDare = isset($_GET['idDare'])? $_GET['idDare'] : null;

                if(isset($_POST['Comment']))
                {
                    $model->attributes = $_POST['Comment'];
                    $model->idUser = Yii::app()->user->getId();
                    $model->submitDate = date('Y-m-d, H:i:s');

                    if($model->save())
                        Yii::app()->user->setFlash('comment','Your comment has been submited!');                 
                    else
                        Yii::app()->user->setFlash('comment','Sorry, there was a problem during the saving process..! Please try again...');                                      
                }      

                $type = isset($_GET['idDare']) ? 'Dare' : 'Truth';
                $comments = Comment::model()->with('user',"user.scoreTruth","user.scoreDare")->findAllByAttributes(array("id$type"=>$_GET["id$type"]));
                $idTruthOrDare = $_GET["id$type"];   

                $this->render('comment',array('model'=>$model,'comments'=>$comments,'idTruthOrDare'=>$idTruthOrDare,'type'=>$type));   	
            }
        }
             
	public function actionTest()
	{  
                $this->render('test');
	}
        
        
        
//      <!--********************************-->
//      <!-- Functions not related to pages -->
//      <!--********************************-->
        
        
	public function actionVote()
	{
            if(isset($_POST['idTruth']))
                $type = 'Truth';
            if(isset($_POST['idDare']))
                $type = 'Dare';
                    
            if(isset($type) && isset($_POST['vote']))
            {
                if(!VotingDetail::model()->exists("idUser = :idUser AND id$type = :id$type",array(':idUser'=>Yii::app()->user->getId(),":id$type"=>$_POST["id$type"])))
                {
                    $model = $type::model()->findByPk($_POST["id$type"]);             
                    echo $model->addVote(Yii::app()->user->getId(), $_POST['vote']);               
                }
                else
                    echo "Already Voted!";
                return;
            }
            
            return "ERROR";
	}
        
	public function actionVoteChallenge()
	{      
            if(isset($_POST['idChallenge']) && isset($_POST['vote']))
            {
                if(!VotingDetail::model()->exists("idUser = :idUser AND idChallenge = :idChallenge",array(':idUser'=>Yii::app()->user->getId(),":idChallenge"=>$_POST["idChallenge"])))
                {
                    $model = Challenge::model()->findByPk($_POST["idChallenge"]);             
                    echo $model->addVote(Yii::app()->user->getId(), $_POST['vote']);               
                }
                else
                    echo "Already Voted!";
                return;
            }
            
            return "ERROR";
	}
        
        public function actionAddFavourite()
        {
            if(isset($_POST['idUserList']) && (isset($_POST['idTruth']) || isset($_POST['idDare'])))
            {
                //Verify that the person that add to favourite is the owner of the list
                $model = UserList::model()->findByPk($_POST['idUserList']);
                if($model->idUser == Yii::app()->user->getId())
                {
                    $model = new Userlistcontent;
                    $model->idUserList = $_POST['idUserList'];
                    if(isset($_POST['idTruth']))
                        $model->idTruth = $_POST['idTruth'];
                    if(isset($_POST['idDare']))
                        $model->idDare = $_POST['idDare'];
                    if($model->save())
                        echo "SUCCESS";
                }
            }  
        }
        
	public function actionEvents()
	{
                $this->render('events');
	}
    
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
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
				'actions'=>array('index','contact','error','captcha','events','about','page','ranking','test'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('submitIdea','comment','addFavourite','vote','voteChallenge'),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('admin', 'acceptTruth', 'acceptDare', 'refuseTruth', 'refuseDare'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	} 

}