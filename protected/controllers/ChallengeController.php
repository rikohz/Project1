<?php

class ChallengeController extends MyController
{
    
//      <!--****************************-->
//      <!-- Functions related to pages -->
//      <!--****************************-->

	public function actionChallenge()
	{     
        if(isset($_GET['idTruth']) || isset($_GET['idDare'])) 
        {
            $idType = isset($_GET['idDare']) ? 'idDare' : 'idTruth';
            $modelTruthOrDare = isset($_GET['idDare']) ? new Dare : new Truth;
            $modelTruthOrDare->$idType = $_GET[$idType]; 
            $challenges = Challenge::model()->with('truth','dare','userTo',"userTo.scoreTruth","userTo.scoreDare")->findAllByAttributes(array($idType=>$_GET[$idType],'status'=>1));

            $this->render('challenge',array('modelTruthOrDare'=>$modelTruthOrDare,'challenges'=>$challenges,'idType'=>$idType));   	
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
				'actions'=>array('error','captcha','challenge'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	} 
}