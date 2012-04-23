<?php

class SiteController extends MyController
{
	/**
	 * Declares class-based actions.
	 */
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

	public function actionIndex()
	{              
                $generalRanking = User::getRanking(NULL,'year');
                $truthRanking = User::getRanking('truth','year');
                $dareRanking = User::getRanking('dare','year');    
                
                $this->render('index',array('generalRanking'=>$generalRanking,'truthRanking'=>$truthRanking,'dareRanking'=>$dareRanking));
	}


	public function actionRanking()
	{       
                //A COMPLETER
            
                $generalRanking = User::getRanking(NULL,'year');
                $truthRanking = User::getRanking('truth','year');
                $dareRanking = User::getRanking('dare','year');
                
                $this->render('ranking',array('generalRanking'=>$generalRanking,'truthRanking'=>$truthRanking,'dareRanking'=>$dareRanking));
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

	public function actionVote()
	{
            if(isset($_POST['idTruth']) && isset($_POST['vote']))
                if(!VotingDetail::model()->exists('idUser = :idUser AND idTruth = :idTruth',array(':idUser'=>Yii::app()->user->getId(),':idTruth'=>$_POST['idTruth'])))
                {
                    //We add one vote
                    $modelTruth = Truth::model()->findByPk($_POST['idTruth']);             
                    echo $modelTruth->addVote(Yii::app()->user->getId(), $_POST['vote']); 
                    
                    return;
                }
                else
                {
                    echo "no";
                    return;
                }
            
            if(isset($_POST['idDare']) && isset($_POST['vote']))
            {
                if(!VotingDetail::model()->exists('idUser = :idUser AND idDare = :idDare',array(':idUser'=>Yii::app()->user->getId(),':idDare'=>$_POST['idDare'])))
                {
                    //We add one vote
                    $modelDare = Dare::model()->findByPk($_POST['idDare']);
                    echo $modelDare->addVote(Yii::app()->user->getId(), $_POST['vote']);  
                    return;
                }
                else
                {
                    echo "no";
                    return;
                }
            }
            
            return "ERROR";
	}
              
	public function actionSubmitIdea()
	{            
            $model = new SubmitIdeaForm;          
            $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
            $truthOrDare = array('1'=>'Truth','2'=>'Dare');
            $model->username = Yii::app()->user->name;
                        
            if(isset($_POST['SubmitIdeaForm']))
            {
                $model->attributes = $_POST['SubmitIdeaForm'];

                if($model->validate())
                {
                    $modelTruthOrDare = $model->truthOrDare == '1' ? new Truth : new Dare;
                    $modelTruthOrDare->idCategory = $model->idCategory;
                    $modelTruthOrDare->idUser = Yii::app()->user->getId();
                    $modelTruthOrDare->dateSubmit = date('Y-m-d, H:i:s');
                    $modelTruthOrDare->anonymous = $model->anonymous;
                    if($model->truthOrDare == '1')
                        $modelTruthOrDare->truth = $model->idea;
                    else
                        $modelTruthOrDare->dare = $model->idea;
                    
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
            $model = new Comment;
            $model->idUser = Yii::app()->user->getId();
            $model->submitDate = date('Y-m-d, H:i:s');
            $model->idTruth = isset($_GET['idTruth'])? $_GET['idTruth'] : null;
            $model->idDare = isset($_GET['idDare'])? $_GET['idDare'] : null;
            $type = null;
            
            if(isset($_POST['Comment']))
            {
                $model->attributes = $_POST['Comment'];
                
                if($model->save())
                    Yii::app()->user->setFlash('comment','Your comment has been submited!');                 
                else
                    Yii::app()->user->setFlash('comment','Sorry, there was a problem during the saving process..! Please try again...');                                      
            }

            if(isset($_GET['idTruth']) and !isset($_GET['idDare'])) 
            { 
                $comments = Comment::model()->with('user','user.scoreTruth','user.scoreDare')->findAllByAttributes(array('idTruth'=>$_GET['idTruth']));
                $type = 'Truth';
                $idTruthOrDare = $_GET['idTruth'];
            }
            
            if(isset($_GET['idDare']) and !isset($_GET['idTruth'])) 
            { 
                $comments = Comment::model()->with('user','user.scoreTruth','user.scoreDare')->findAllByAttributes(array('idDare'=>$_GET['idDare']));
                $type = 'Dare';
                $idTruthOrDare = $_GET['idDare'];
            }          
            
            $this->render('comment',array('model'=>$model,'comments'=>$comments,'idTruthOrDare'=>$idTruthOrDare,'type'=>$type));   
	}
        
	public function actionAdmin()
	{
                $modelTruth = new Truth;
                $modelTruth->validated = 0;
                $dataTruth = $modelTruth->findAll($modelTruth->getCriteria());
                
                $modelDare = new Dare;
                $modelDare->validated = 0;
                $dataDare = $modelDare->findAll($modelDare->getCriteria());
                      
		$this->render('admin',array('dataTruth'=>$dataTruth,'dataDare'=>$dataDare));
	}
        
	public function actionTest()
	{  
                $image = Yii::app()->image->load('userImages/default.png');
                $image->resize(64, 64)->quality(75);
                $image->save('userImages/default_mini.png');
            
                $this->render('test');
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
        
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionEvents()
	{
                $this->render('events');
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
				'actions'=>array('index','contact','error','captcha','events','about','page','ranking','test'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('submitIdea','comment','addFavourite','vote'),
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