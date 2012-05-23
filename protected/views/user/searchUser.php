<?php Yii::app()->getClientScript()->registerCssFile(CHtml::asset(Yii::getPathOfAlias('system.web.widgets.pagers.pager').'.css')); ?>
<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - Update Informations';
  $this->breadcrumbs=array(
        'Search Users',
  );
?>


<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-search-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true),
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<span>
            Username: 
            <?php echo $form->textField($model,'username'); ?>
	</span>

	<span>
                Gender:
		<?php echo $form->radioButtonList($model,'gender',array(''=>'Both','1'=>'Man','0'=>'Female'),array('labelOptions'=>array('style'=>'display:inline'),'separator'=>'')); ?>
	</span>

	<span>
		Age Min:
		<?php echo $form->textField($model,'ageMin'); ?>
	</span>

	<span>
		Age Max:
		<?php echo $form->textField($model,'ageMax'); ?>
	</span>
        
        <?php
            echo $form->dropDownList($model,'idProvince', $provinces,
                array(
                    'prompt'=>'Select Province',
                    'ajax' => array(
                        'type'=>'POST',
                        'url'=>CController::createUrl('User/updateCities'), 
                        'dataType'=>'json',
                        'data'=>array('idProvince'=>'js:this.value'),  
                        'success'=>'function(data) {
                            $("#SearchUserForm_idCity").html(data.dropDownCities);
                            $("#SearchUserForm_idDistrict").html(data.dropDownDistricts);
                        }',
                ))); 
            
            echo $form->dropDownList($model,'idCity', array(),
                array(
                    'prompt'=>'Select City',
                    'ajax' => array(
                        'type'=>'POST', 
                        'url'=>CController::createUrl('User/updateDistricts'), 
                        'update'=>'#SearchUserForm_idDistrict', 
                        'data'=>array('idCity'=>'js:this.value'),
                ))); 

                
            echo $form->dropDownList($model,'idDistrict', array(), array('prompt'=>'Select District'));
        ?>

	<span>
		Level:
		<?php echo $form->dropDownList($model,'level', array('1'=>'小','2'=>'中','3'=>'高'), 
                    array('prompt'=>'Level',)); ?>
	</span>

	<span>
		<?php echo CHtml::ajaxSubmitButton ("Submit",
                              CController::createUrl('user/searchUser'), 
                              array('update' => '#searchResult'));
                ?>
	</span>
    <?php $this->endWidget(); ?>
</div>


<div id="searchResult">
   <?php $this->renderPartial('_searchUserResult', array('searchResult'=>array())); ?>
</div>