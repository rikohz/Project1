<script type="text/javascript">    
    function clearUsername(){
        if(document.getElementById('SubmitIdeaForm_anonymous').checked == true)
            document.getElementById('SubmitIdeaForm_username').value = 'Anonymous';
        else
            document.getElementById('SubmitIdeaForm_username').value = '<?php echo Yii::app()->user->name; ?>';         
    }
    function selectTruth(){
        document.getElementById('truth').src = 'images/truth.png';
        document.getElementById('dare').src ='images/dare2.png';
        document.getElementById('SubmitIdeaForm_truthOrDare').selectedIndex = 1;
    }
    function selectDare(){
        document.getElementById('truth').src = 'images/truth2.png';
        document.getElementById('dare').src = 'images/dare.png';
        document.getElementById('SubmitIdeaForm_truthOrDare').selectedIndex = 2;
    }
</script>

<?php if(Yii::app()->user->hasFlash('submitIdea')): ?>
    <div class="flash-success">
            <?php echo Yii::app()->user->getFlash('submitIdea'); ?>
    </div>
<?php else: ?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'submitIdea-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true),
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('readOnly'=>true)); ?>
		<?php echo $form->error($model,'username'); ?>
		<?php echo $form->checkBox($model,'anonymous',array('onClick'=>'clearUsername()','value'=>'1')); ?> Anonymous
	</div>
        
        <div class="row">
            <?php echo $form->labelEx($model,'truthOrDare'); ?>
            <span style="display: none;"><?php echo $form->dropDownList($model,'truthOrDare',$truthOrDare,array('prompt'=>'Select')); ?></span>
            <img id="truth" src="images/truth2.png" onClick="selectTruth()" />
            <img id="dare" src="images/dare2.png" onClick="selectDare()" />
            <?php echo $form->error($model,'truthOrDare'); ?>
        </div>

	<div class="row">
		<?php echo $form->labelEx($model,'idCategory'); ?>
		<?php echo $form->dropDownList($model,'idCategory',$categories,array('prompt'=>'Select a category')); ?>
		<?php echo $form->error($model,'idCategory'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'idea'); ?>
		<?php echo $form->textArea($model,'idea'); ?>
		<?php echo $form->error($model,'idea'); ?>
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

        