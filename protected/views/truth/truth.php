<?php
$this->pageTitle=Yii::app()->name . ' - Truths';
$this->breadcrumbs=array(
	'Truths',
);
?>


<!--******************-->
<!-- Order and Filter -->
<!--******************-->
<div style="border:1px black solid;width:100%;background-color:#EEE;border-radius: 20px;">
    <div class="form" style="margin:10px;">

        <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'truth-search-form',
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
                #idTruth: 
                <?php echo $form->textField($model,'idTruth',array('style'=>'width:50px;')); ?>
            </span>
            <span>
                <?php echo CHtml::ajaxSubmitButton("Submit",CController::createUrl('truth/truth'),array('update' => '#divSearchResult')) ;?>
            </span>
            <span style="float:right;">
                Order by:
                <?php echo CHtml::ajaxSubmitButton("SubmitDate","index.php?r=truth/truth",array('update' => '#divSearchResult'),array('onClick'=>"$('#SearchTruthForm_order').val('t.dateSubmit');")) ;?>
                <?php echo CHtml::ajaxSubmitButton("Popularity","index.php?r=truth/truth",array('update' => '#divSearchResult'),array('onClick'=>"$('#SearchTruthForm_order').val('t.voteUp-t.voteDown');")) ;?>
                <?php echo CHtml::ajaxSubmitButton("Nb of Comments","index.php?r=truth/truth",array('update' => '#divSearchResult'),array('onClick'=>"$('#SearchTruthForm_order').val('nbComment');")) ;?>
            </span>
        <?php $this->endWidget(); ?>
    </div>
</div>
<br />
<br />

<div id="divSearchResult">
    <?php $this->renderPartial('_searchTruthResult', array('model'=>$model)); ?>
</div>


