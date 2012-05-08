<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - Update Informations';
  $this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
        'My Settings'=>array('user/mySettings'),
	'Update Informations',
  );
?>

<?php if(Yii::app()->user->hasFlash('updateUserInformations')): ?>
    <div class="flash-success">
            <?php echo Yii::app()->user->getFlash('updateUserInformations'); ?>
    </div>
<?php else: ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-update-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true),
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'conf_email'); ?>
		<?php echo $form->textField($model,'conf_email'); ?>
		<?php echo $form->error($model,'conf_email'); ?>
	</div>
        
        <?php
            echo $form->dropDownList($model,'idProvince', $provinces,
                array(
                    'prompt'=>'Select Province',
                    'options'=>array($model->idProvince=>array("selected"=>"selected")),
                    'ajax' => array(
                        'type'=>'POST',
                        'url'=>CController::createUrl('User/updateCities'), 
                        'dataType'=>'json',
                        'data'=>array('idProvince'=>'js:this.value'),  
                        'success'=>'function(data) {
                            $("#User_idCity").html(data.dropDownCities);
                            $("#User_idDistrict").html(data.dropDownDistricts);
                        }',
                ))); 
            
            echo $form->dropDownList($model,'idCity', $cities,
                array(
                    'prompt'=>'Select City',
                    'options'=>array($model->idCity=>array("selected"=>"selected")),
                    'ajax' => array(
                        'type'=>'POST', 
                        'url'=>CController::createUrl('User/updateDistricts'), 
                        'update'=>'#User_idDistrict', 
                        'data'=>array('idCity'=>'js:this.value'),
                ))); 

                
            echo $form->dropDownList($model,'idDistrict', $districts, 
                    array(
                        'prompt'=>'Select District',
                        'options'=>array($model->idDistrict=>array("selected"=>"selected"))
                    ));
        ?>

        
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
