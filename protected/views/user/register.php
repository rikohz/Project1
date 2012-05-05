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
		action: '/TruthOrDare/script/upload.php',
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
                            thumb.attr('src', ''); 
                            $('#validateImage').val(0);      
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
<style type="text/css">  
    div.preview {margin-left: auto; margin-right: auto; height: 150px; width: 150px;  border: 2px dotted #CCCCCC;}
    div.preview.loading {background: url(/TruthOrDare/images/loading.gif) no-repeat 63px 63px;}
    div.preview.loading img {display: none;}
</style>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-register-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true),
	'enableAjaxValidation'=>false,
        'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

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

	<div class="row">
		<?php echo $form->labelEx($model,'gender'); ?>
            <ul>
		<?php echo $form->radioButtonList($model,'gender',array(1=>'Man',0=>'Female')); ?>
            </ul>
                <?php echo $form->error($model,'gender'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

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
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'conf_password'); ?>
		<?php echo $form->passwordField($model,'conf_password'); ?>
		<?php echo $form->error($model,'conf_password'); ?>
	</div>

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
                <img id="thumb" width="150px" height="150px" src="userImages/<?php echo $model->profilePicture == "default" ? $model->profilePicture . $model->profilePictureExtension : "temp/" . $model->profilePicture . $model->profilePictureExtension; ?>" />
            </div>     
        </div>
        
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
                            $("#User_idCity").html(data.dropDownCities);
                            $("#User_idDistrict").html(data.dropDownDistricts);
                        }',
                ))); 
            
            echo $form->dropDownList($model,'idCity', array(),
                array(
                    'prompt'=>'Select City',
                    'ajax' => array(
                        'type'=>'POST', 
                        'url'=>CController::createUrl('User/updateDistricts'), 
                        'update'=>'#User_idDistrict', 
                        'data'=>array('idCity'=>'js:this.value'),
                ))); 

                
            echo $form->dropDownList($model,'idDistrict', array(), array('prompt'=>'Select District'));
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