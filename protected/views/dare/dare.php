<?php
$this->pageTitle=Yii::app()->name . ' - Dares';
$this->breadcrumbs=array(
	'Dares',
);
?>


<!--******************-->
<!-- Order and Filter -->
<!--******************-->
<div style="border:1px black solid;width:100%;background-color:#EEE;border-radius: 20px;">
    <div class="form" style="margin:10px;">

        <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'dare-search-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array('validateOnSubmit'=>true),
        )); ?>

            <?php echo $form->hiddenField($model,'order'); ?>
            <span>
                Category:
                <?php echo $form->dropDownList($model,'idCategory', $categories,array('prompt'=>'All',)); ?>
            </span>
            <span style="margin-left:10px">
                Username: 
                <?php echo $form->textField($model,'username',array('style'=>'width:80px;')); ?>
            </span>
            <span style="margin-left:10px">
                #idDare: 
                <?php echo $form->textField($model,'idDare',array('style'=>'width:50px;')); ?>
            </span>
            <span>
                <?php echo CHtml::ajaxSubmitButton("Submit",CController::createUrl('dare/dare'),array('update' => '#divSearchResult')) ;?>
            </span>
            <span style="float:right;">
                Order by:
                <?php echo CHtml::ajaxSubmitButton("SubmitDate","index.php?r=dare/dare",array('update' => '#divSearchResult'),array('onClick'=>"$('#SearchDareForm_order').val('t.dateSubmit');")) ;?>
                <?php echo CHtml::ajaxSubmitButton("Popularity","index.php?r=dare/dare",array('update' => '#divSearchResult'),array('onClick'=>"$('#SearchDareForm_order').val('t.voteUp-t.voteDown');")) ;?>
                <?php echo CHtml::ajaxSubmitButton("Nb of Comments","index.php?r=dare/dare",array('update' => '#divSearchResult'),array('onClick'=>"$('#SearchDareForm_order').val('nbComment');")) ;?>
            </span>
        <?php $this->endWidget(); ?>
    </div>
</div>

<br />
<br />

<div id="divSearchResult">
    <?php $this->renderPartial('_searchDareResult', array('model'=>$model)); ?>
</div>