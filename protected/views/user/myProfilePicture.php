<script type="text/javascript" src="/TruthOrDare/script/ajaxupload.js"></script>
<script type="text/javascript">
 
function getExtension(filename){        
    var parts = filename.split(".");        
    return (parts[(parts.length-1)]);    
}   

//Fonction pour le chargement du module de preview Image
$(document).ready(function(){

	var thumb = $('img#thumb');
        
	new AjaxUpload('User_pictureUploader', {
		action: '/TruthOrDare/script/uploadPicture.php',
		name: 'userfile',
		onSubmit: function(file, extension) {
			$('div.preview').addClass('loading');
		},
		onComplete: function(file, response) {   
                        if(jQuery.inArray(response, ["1","2","3","4"]) !== -1){
                            $('div.preview').removeClass('loading');
                            thumb.unbind();
                            switch (response) 
                            { 
                            case "1": 
                                document.getElementById('errorUpload').innerHTML = "Wrong format of file - Only JPG/PNG/GIF are allowed";
                                break; 
                            case "2": 
                                document.getElementById('errorUpload').innerHTML = "File too heavy - 2MB maximum";
                                break; 
                            defaut: 
                                document.getElementById('errorUpload').innerHTML = "Problem during file transfer";
                                break; 
                            }
//                            thumb.attr('src', ''); 
//                            $('#validateImage').val(0);      
                        } else {
                            thumb.load(function(){
				$('div.preview').removeClass('loading');
				thumb.unbind();
                            });
                            document.getElementById('errorUpload').innerHTML = "&nbsp;";
                            thumb.attr('src', response.substring(13,response.length));
                            $('#validateImage').val(1); 
                            $('#tempName').val(response.substring(0,13)); 
                            $('#extension').val('.' + getExtension(response)); 
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
            <input type="hidden" name="User[profilePicture]" value="<?php echo $model->profilePicture; ?>" id="tempName" />
            <input type="hidden" name="User[profilePictureExtension]" value="<?php echo $model->profilePictureExtension; ?>" id="extension" />
            <input type="hidden" name="validateImage" id="validateImage" value="0" />
            <p align="center"><u>Preview</u></p>
            <div class="preview">
                <img id="thumb" width="150px" height="150px" src="userImages/profilePicture/<?php echo $model->profilePicture . '_profile' . $model->profilePictureExtension; ?>" />
            </div>     
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