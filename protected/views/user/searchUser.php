<?php Yii::app()->getClientScript()->registerCssFile(CHtml::asset(Yii::getPathOfAlias('system.web.widgets.pagers.pager').'.css')); ?>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - Search Users';
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
    
    <div style="border:1px black solid;display:table;width:100%;background-color:#EEE;border-radius: 20px;">
        <div style="width:30%;display:table-cell;padding: 20px 20px 20px 50px;">
            <div class="row">
                <?php echo $form->labelEx($model,'gender'); ?>
                <?php echo $form->radioButtonList($model,'gender',array(''=>'Both','1'=>'Man','0'=>'Woman'),array('labelOptions'=>array('style'=>'display:inline;font-weight:normal;'),'separator'=>'&nbsp;&nbsp;&nbsp;')); ?>
            </div>
            
            <div style="margin-top:10px;">
                <div style="width:60px;display:inline-block;">
                    <?php echo $form->labelEx($model,'ageMin'); ?>
                    <?php echo $form->textField($model,'ageMin',array('style'=>"width:50px;")); ?>
                </div>

                <div style=";width:60px;display:inline-block;">
                    <?php echo $form->labelEx($model,'ageMax'); ?>
                    <?php echo $form->textField($model,'ageMax',array('style'=>"width:50px;")); ?>
                </div>
            </div>
            
            <div class="row">
                <?php echo $form->labelEx($model,'level'); ?>
                <?php echo $form->dropDownList($model,'level', array('1'=>'小','2'=>'中','3'=>'高'), 
                            array('prompt'=>'Level',)); ?>
            </div>
        </div>
        <div style="width:40%;display:table-cell;padding:20px;">
            
            <div class="row">
                <?php echo $form->labelEx($model,'location'); ?>
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
            </div>
            
            <div class="row">
                <?php echo $form->labelEx($model,'username'); ?>
                <?php echo $form->textField($model,'username'); ?>
            </div>

            <div class="row buttons" style="position:relative;left:260px;">
                <?php echo CHtml::ajaxSubmitButton ("Submit",
                                    CController::createUrl('user/searchUser'), 
                                    array('update' => '#divSearchResult'));
                        ?>
            </div>
        </div>
        <div style="width:30%;display:table-cell;padding:20px;">
            Order by:
            <?php echo $form->hiddenField($model,'order'); ?>
            <div class="row" style="margin-left:50px;"><?php echo CHtml::ajaxSubmitButton("Last online","index.php?r=user/searchuser",array('update' => '#divSearchResult'),array('onClick'=>"$('#SearchUserForm_order').val('t.lastLoginDate DESC');")) ;?></div>
            <div class="row" style="margin-left:50px;"><?php echo CHtml::ajaxSubmitButton("Score","index.php?r=user/searchuser",array('update' => '#divSearchResult'),array('onClick'=>"$('#SearchUserForm_order').val('(scoreTruth.score + scoreDare.score) DESC');")) ;?></div>
            <div class="row" style="margin-left:50px;"><?php echo CHtml::ajaxSubmitButton("Username","index.php?r=user/searchuser",array('update' => '#divSearchResult'),array('onClick'=>"$('#SearchUserForm_order').val('t.username ASC');")) ;?></div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>

<br />
<br />

<div id="divSearchResult">
   <?php $this->renderPartial('_searchUserResult', array('searchResult'=>$searchResult,'pages'=>$pages)); ?>
</div>