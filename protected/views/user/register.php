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
            <input type="hidden" name="infoPreview" id="infoPreview" value="<?php echo isset($_POST['infoPreview']) ? $_POST['infoPreview'] : ''; ?>" />
            <p align="center"><u>Preview</u></p>
            <div class="previewProfilePicture" style="background: url(userImages/<?php echo $model->profilePicture == "default" ? "profilePicture/" . $model->profilePicture . "_profile" . $model->profilePictureExtension : "temp/" . $model->profilePicture . $model->profilePictureExtension; ?>);">&nbsp;</div>    
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