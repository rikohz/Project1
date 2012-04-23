<script type="text/javascript">
$(function() {
$(".deleteCoin").click(function() 
{
    var serialNumber = $(this).attr("id");
    var dataString = 'serialNumber='+ serialNumber ;
    var parent = $(this).parent();
    
    $.ajax({
        type: "POST",
        url: "index.php?r=user/deleteCoin",
        data: dataString,
        cache: false,

        success: function(html)
        {
            parent.html(html);
        } 
    });

    return false;
});
});
</script>
<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - Update Coins';
  $this->breadcrumbs=array(
	'Update Coins',
  );
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'coin-update-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true),
	'enableAjaxValidation'=>false,
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'serialNumber'); ?>
		<?php echo $form->textField($model,'serialNumber'); ?>
		<?php echo $form->error($model,'serialNumber'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'verifCode'); ?>
		<?php echo $form->passwordField($model,'verifCode'); ?>
		<?php echo $form->error($model,'verifCode'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<?php foreach ($coins as $row) { ?>   
    <span style="display:inline-block; margin-right:20px; text-align:center;" id="coin<?php echo $row['serialNumber']; ?>">
        <div style="width:80px; height:39px; background: url(<?php echo Yii::app()->request->baseUrl; ?>/images/coin.png); padding-top:40px;"><?php echo $row['serialNumber']; ?></div>
        <br />
        <a href="" class="deleteCoin" id="<?php echo $row['serialNumber']; ?>" name="deleteCoin">Delete</a>
    </span>
<?php } ?>
