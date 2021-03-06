<?php

class DareController extends MyController
{
    
//      <!--****************************-->
//      <!-- Functions related to pages -->
//      <!--****************************-->

	public function actionDare()
	{       
            $model = new SearchDareForm;
            
            //If we selected some Search Criteria
            if(isset($_POST['SearchDareForm'])) 
            { 
                if(isset($_POST['SearchDareForm']['idDare']) && $_POST['SearchDareForm']['idDare'] !== '')
                    $model->idDare = $_POST['SearchDareForm']['idDare'];
                else
                    $model->attributes = $_POST['SearchDareForm'];
            }                
            
            //If we are using AJAX Update Panel
            if(Yii::app()->request->isAjaxRequest)
                $this->renderPartial('_searchDareResult',array('model'=>$model));
            else
            {
                $categories = CHtml::listData(Category::model()->findAll(), 'idCategory', 'category');
                $this->render('dare',array('model'=>$model,'categories'=>$categories));
            }
	}
         
        
//      <!--********************************-->
//      <!-- Functions not related to pages -->
//      <!--********************************-->
       
	public function actionAcceptDare()
	{
            if(isset($_POST['idDare']))
                {
                    $model = Dare::model()->findByPk($_POST['idDare']);
                    $model->validated = 1;
                    $model->save();

                    echo "APPROVED";
                }
	}
        
	public function actionRefuseDare()
	{
            if(isset($_POST['idDare']))
                {
                    $model = Dare::model()->findByPk($_POST['idDare']);
                    $model->validated = 2;
                    $model->save();

                    echo "<span style='font-weight:bold; font-size: 2em';>DISAPPROVED</span>";
                }
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
				'actions'=>array('error','captcha','dare'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array(),
				'users'=>array('@'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('acceptDare', 'refuseDare'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	} 

    
}