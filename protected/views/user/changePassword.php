<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - Change Password';
  $this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
        'My Settings'=>array('user/mySettings'),
	'Change Password',
  );
?>

<?php if(Yii::app()->user->hasFlash('changePassword')): ?>
    <div class="flash-success">
            <?php echo Yii::app()->user->getFlash('changePassword'); ?>
    </div>
<?php else: ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'change-password-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true),
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'newPassword'); ?>
		<?php echo $form->passwordField($model,'newPassword'); ?>
		<?php echo $form->error($model,'newPassword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'conf_newPassword'); ?>
		<?php echo $form->passwordField($model,'conf_newPassword'); ?>
		<?php echo $form->error($model,'conf_newPassword'); ?>
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
