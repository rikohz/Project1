<?php

class TruthController extends MyController
{
    
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

	public function actionTruth()
	{                
            $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
      
            //Filter and order criterias
            if(isset($_GET['category']))
                Yii::app()->session['category'] = $_GET['category']; 
            $category = Yii::app()->session['category'];
            if(isset($_GET['order']))
                Yii::app()->session['order'] = $_GET['order']; 
            $order = Yii::app()->session['order'];
            
            $this->render('truth',array('categories'=>$categories,'order'=>$order,'category'=>$category));
	}
	       
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
				'actions'=>array('error','captcha'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('truth'),
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