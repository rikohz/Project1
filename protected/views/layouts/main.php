<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.css" />
        <!-- Liens jQuery -->
<!--        <script type="text/javascript" src="/TruthOrDare/script/jquery-1.7.1.min.js"></script>-->
<!--        NOUVELLE VERSION-->
<!--        <link type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.css" rel="stylesheet" />	
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>-->
        
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
                <div id="languages">
                    <?php $form=$this->beginWidget('LangBox'); ?>
                    <?php $this->endWidget(); ?>  
                </div>
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php 
                $isAdmin = !Yii::app()->user->isGuest ? Yii::app()->user->isAdmin() : false;
                $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>Yii::t('base','Home'), 'url'=>array('/site/index')),
				array('label'=>Yii::t('base','Test'), 'url'=>array('/site/test')),
				array('label'=>Yii::t('base','Dares'), 'url'=>array('/dare/dare')),
				array('label'=>Yii::t('base','Truths'), 'url'=>array('/truth/truth')),
				array('label'=>Yii::t('base','Submit Idea'), 'url'=>array('/site/submitIdea')),
				array('label'=>Yii::t('base','Purchase Online'), 'url'=>array('/site/purchaseOnline')),
				array('label'=>Yii::t('base','Events'), 'url'=>array('/site/events')),
				array('label'=>Yii::t('base','About'), 'url'=>array('/site/page', 'view'=>'about')),
				array('label'=>Yii::t('base','Contact'), 'url'=>array('/site/contact')),
				array('label'=>Yii::t('base','My Page'), 'url'=>array('/user/myPage'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>Yii::t('base','Admin'), 'url'=>array('/admin/admin'), 'visible'=>$isAdmin),
				array('label'=>Yii::t('base','Login'), 'url'=>array('/user/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>Yii::t('base','Logout').' ('.Yii::app()->user->name.')', 'url'=>array('/user/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by TruthOrDare<br/>
		All Rights Reserved.
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>