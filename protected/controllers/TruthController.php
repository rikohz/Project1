<?php

class TruthController extends MyController
{
    
//      <!--****************************-->
//      <!-- Functions related to pages -->
//      <!--****************************-->

	public function actionTruth()
	{     
            $model = new SearchTruthForm;
            
            //If we selected some Search Criteria
            if(isset($_POST['SearchTruthForm'])) 
            { 
                if(isset($_POST['SearchTruthForm']['idTruth']) && $_POST['SearchTruthForm']['idTruth'] !== '')
                    $model->idTruth = $_POST['SearchTruthForm']['idTruth'];
                else
                    $model->attributes = $_POST['SearchTruthForm'];
            }                
            
            //If we are using AJAX Update Panel
            if(Yii::app()->request->isAjaxRequest)
                $this->renderPartial('_searchTruthResult',array('model'=>$model));
            else
            {
                $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
                $this->render('truth',array('model'=>$model,'categories'=>$categories));
            }
	}

//	public function actionTruth()
//	{                
//            $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
//      
//            //Filter and order criterias
//            if(isset($_GET['idCategory']))
//                Yii::app()->session['idCategory'] = $_GET['idCategory']; 
//            if(isset($_GET['order']))
//                Yii::app()->session['order'] = $_GET['order']; 
//            
//            $this->render('truth',array('categories'=>$categories,'order'=>Yii::app()->session['order'],'idCategory'=>Yii::app()->session['idCategory']));
//	}
         
        
//      <!--********************************-->
//      <!-- Functions not related to pages -->
//      <!--********************************-->
	       
	public function actionAcceptTruth()
	{
		if(isset($_POST['idTruth']))
                {
                    $model = Truth::model()->findByPk($_POST['idTruth']);
                    $model->validated = 1;
                    $model->save();

                    echo "APPROVED";
                }
	}
        
	public function actionRefuseTruth()
	{
		if(isset($_POST['idTruth']))
                {
                    $model = Truth::model()->findByPk($_POST['idTruth']);
                    $model->validated = 2;
                    $model->save();

                    echo "<span style='font-weight:bold; font-size: 2em';>DISAPPROVED</span>";
                }
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
				'actions'=>array('error','captcha','truth'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('acceptTruth', 'refuseTruth'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	} 
}