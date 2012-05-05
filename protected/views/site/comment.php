<?php
$this->pageTitle = Yii::app()->name . ' - Comments';
$this->breadcrumbs = $type == 'truth'? array('Truth'=>array('truth/truth'),'Comment') : array('Dares'=>array('dare/dare'),'Comment');
?>


<!--***************-->
<!-- Truth or Dare -->
<!--***************-->  

<?php 
    $this->widget($type.'List',
            array(
                'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
                'filterLevel'=>Yii::app()->user->isGuest ? 1 : Yii::app()->user->getLevel(),
                'withVotes'=>1,
                'withFavourites'=>!Yii::app()->user->isGuest,
                'withComments'=>0,
                'id'.$type=>$idTruthOrDare
                )); 
?>

<br />
<br />


    
<!--**********-->
<!-- Comments -->
<!--**********-->  

<?php foreach ($comments as $row) { ?>
    <?php echo $row->user->username; ?> - 
    <?php echo Yii::app()->user->getTruthRankName($row->user->scoreTruth->score) . ' - ' . Yii::app()->user->getDareRankName($row->user->scoreDare->score); ?> - 
    <?php echo Yii::app()->dateFormatter->format('yyyy-MM-dd',$row->submitDate); ?>
    <div style="background-color: #B7D6E7;">
        <?php echo $row->comment; ?>
    </div>
    <br />
<?php } ?>  

<!--****************-->
<!-- Submit Comment -->
<!--****************-->  
<?php if(Yii::app()->user->hasFlash('comment')): ?>
    <div class="flash-success"><?php echo Yii::app()->user->getFlash('comment'); ?></div>
<?php else: ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'comment-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
	'validateOnSubmit'=>true),
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'comment'); ?>
		<?php echo $form->textArea($model,'comment'); ?>
		<?php echo $form->error($model,'comment'); ?>
	</div>

        <?php if(CCaptcha::checkRequirements()): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'verifyCode'); ?>
		<div>
		<?php $this->widget('CCaptcha'); ?>
		<?php echo $form->textField($model,'verifyCode'); ?>
		</div>
		<div class="hint">Please enter the letters as they are shown in the image above.
		<br/>Letters are not case-sensitive.</div>
		<?php echo $form->error($model,'verifyCode'); ?>
	</div>
	<?php endif; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php endif; ?>
