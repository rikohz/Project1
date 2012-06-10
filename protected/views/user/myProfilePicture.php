<script type="text/javascript" src="/TruthOrDare/script/ajaxupload.js"></script>
<script type="text/javascript" src="/TruthOrDare/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript">
 
function getExtension(filename){        
    var parts = filename.split(".");        
    return (parts[(parts.length-1)]);    
}   

function updateThumb(name,path,width,height){    
    height = width > 150 ? height / (width / 150) : height;
    width = width > 150 ? width / (width / 150) : width;
    $("div.previewProfilePicture").css("background", "url(" + path + ") no-repeat");
    $('div.previewProfilePicture').css("width",width);
    $('div.previewProfilePicture').css("height",height); 
    $('div.previewProfilePicture').css("background-size","100% 100%"); 
    $('#validateImage').val(1); 
    $("#User_profilePicture").val(name);
    $("#User_profilePictureExtension").val('.' + getExtension(path));
    $('#errorUpload').html("&nbsp;");
}   
//Fonction pour le chargement du module de preview Image
$(document).ready(function(){   
    if($("#infoPreview").val() !== "")
    {
        var pictureArray = $("#infoPreview").val().split('|');
        updateThumb(pictureArray[0],pictureArray[1],pictureArray[2],pictureArray[3]);
    }
    
	new AjaxUpload('User_pictureUploader', {
		action: 'index.php?r=site/uploadPicture',
		name: 'userfile',
		onSubmit: function(file, extension) {  
			$("div.previewProfilePicture").css("background", "url(/TruthOrDare/images/loading.gif) center no-repeat");
		},
		onComplete: function(file, response) {   
                        var pictureArray = response.split('|');
                        if(pictureArray[0] == 0){
                            updateThumb(pictureArray[1],pictureArray[2],pictureArray[3],pictureArray[4]);
                            $("#infoPreview").val(pictureArray[1]+'|'+pictureArray[2]+'|'+pictureArray[3]+'|'+pictureArray[4]);
                        } else {
                            $("div.preview").css("background", "none");
                            $('#validateImage').val(0);  
                            $('#errorUpload').html(pictureArray[1]);
                        }
		}
	});
});   
</script>
<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - Update Profile Picture';
  $this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
        'My Settings'=>array('user/mySettings'),
	'Update Profile Picture',
  );
?>
<style type="text/css">  
    div.preview {margin-left: auto; margin-right: auto; height: 150px; width: 150px;  border: 2px dotted #CCCCCC;}
    div.preview.loading {background: url(/TruthOrDare/images/loading.gif) no-repeat 63px 63px;}
    div.preview.loading img {display: none;}
</style>

<?php if(Yii::app()->user->hasFlash('updateProfilePicture')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('updateProfilePicture'); ?>  
    </div>
<?php else: ?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-updateProfilePicture-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
	'validateOnSubmit'=>true),
        'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'profilePicture'); ?>
        <?php echo $form->fileField($model,'pictureUploader'); ?>
		<?php echo $form->error($model,'pictureUploader'); ?>
		<div class ="errorMessage" id="errorUpload" style="display: inline-block; margin-left: 5px;"></div>
	</div>
             
        <div class="row">
            <?php echo $form->hiddenField($model,'profilePicture'); ?>
            <?php echo $form->hiddenField($model,'profilePictureExtension'); ?>
            <input type="hidden" name="validateImage" id="validateImage" value="0" />
            <input type="hidden" name="infoPreview" id="infoPreview" value="<?php echo $infoPreview; ?>" />
            <p align="center"><u>Preview</u></p>
            <div class="previewProfilePicture" style="background: url(userImages/profilePicture/<?php echo $model->profilePicture . '_profile' . $model->profilePictureExtension; ?>);">&nbsp;</div>    
        </div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php endif; ?>