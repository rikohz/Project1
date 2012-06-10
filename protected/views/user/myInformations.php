<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - Update Informations';
  $this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
        'My Settings'=>array('user/mySettings'),
	'Update Informations',
  );
?>

<?php if(Yii::app()->user->hasFlash('myInformations')): ?>
    <div class="flash-success">
            <?php echo Yii::app()->user->getFlash('myInformations'); ?>
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

	<div class="row">
		<?php echo $form->labelEx($model,'birthDate'); ?>
        <?php 
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker',array(
                'model'=>$model,
                'attribute'=>'birthDate', 
                'mode'=>'date',
                'options'=>array(
                    'changeYear'=>true, 
                    'dateFormat'=>"yy-mm-dd",
                    'yearRange'=>"-100:+0"
                ),
            ));
        ?>
	</div>
        
		<?php echo $form->labelEx($model,'Location'); ?>
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

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php endif; ?>
