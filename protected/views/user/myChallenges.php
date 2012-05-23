<script type="text/javascript" src="/TruthOrDare/script/ajaxupload.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">
function selectCategory(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myChallenges&idCategory=" + value;
}
function selectGender(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myChallenges&idGender=" + value;
}
function selectTypeChallenge(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myChallenges&idTypeChallenge=" + value;
}
function selectStatusChallenge(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myChallenges&idStatusChallenge=" + value;
}
function selectMinDateChallenge(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myChallenges&minDateChallenge=" + value;
}
function selectPrivateStatus(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myChallenges&idPrivateStatus=" + value;
}
function selectUserFrom(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myChallenges&idUserFrom=" + value;
}
function getExtension(filename){        
    var parts = filename.split(".");        
    return (parts[(parts.length-1)]);    
}   

$(function() {
    
    //<!--***********************************-->
    //<!-- PictureBox for Challenge Pictures -->
    //<!--***********************************-->
    $("a.challengePicture").fancybox();
    
    
    //<!-- For Dialog boxes -->
    var parent = null;
    var idChallenge = null;
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    
    //<!--**************************************-->
    //<!-- Dialog box for Accept Challenge Dare -->
    //<!--**************************************-->
    $(".acceptChallengeDare").click(function() {
        idChallenge = $(this).attr("id");
        parent = $(this).parent();
        $( "#dialog-form-accept-challengeDare" ).dialog( "open" );
    }); 
    
    $( "#dialog-form-accept-challengeDare" ).dialog({
            autoOpen: false,
            height: 540,
            width: 360,
            modal: true,
            buttons: {
                    "Validate": function() {
                        if($('#validateImage').val() == 1)
                        {
                            var tempName = $('#tempName').val();
                            var extension = $('#extension').val();
                            $.ajax({ 
                              url: "index.php?r=user/acceptChallenge", 
                              type: "POST", 
                              data: {
                                  'type' : 'Dare',
                                  'answer' : $( "#ChallengeDare_answer" ).val(),
                                  'idChallenge' : idChallenge,
                                  'pictureName' : tempName,
                                  'pictureExtension' : extension
                              }, 
                              success: function(result){ 
                                  if(result == "SUCCESS")
                                    parent.html("<img src='userImages/challenge_mini/" + tempName + "_mini" + extension + "' width='48px' height='48px' />"); 
                                } 
                            });  
                            $( this ).dialog( "close" );
                            $('#tempName').val("");
                            $('#extension').val("");
                            $("#ChallengeDare_answer").val("");
                            $("#thumb").attr("src",""); 
                        } else{
                            document.getElementById('errorUpload').innerHTML = "You have to select a suitable Picture!";
                        }
                    },
                    Cancel: function() {
                            $( this ).dialog( "close" );
                    }
            }
    });

    //<!--***************************************-->
    //<!-- Dialog box for Accept Challenge Truth -->
    //<!--***************************************-->
    $(".acceptChallengeTruth").click(function() {
        idChallenge = $(this).attr("id");
        parent = $(this).parent();
        $( "#dialog-form-accept-challengeTruth" ).dialog( "open" );
    }); 
    
    $( "#dialog-form-accept-challengeTruth" ).dialog({
            autoOpen: false,
            height: 250,
            width: 400,
            modal: true,
            buttons: {
                    "Validate": function() {
                        var answer = $( "#ChallengeTruth_answer" ).val();
                        if(answer.length >= 1)
                        {
                            $.ajax({ 
                              url: "index.php?r=user/acceptChallenge", 
                              type: "POST", 
                              data: {
                                  'type' : 'Truth',
                                  'answer' : answer,
                                  'idChallenge' : idChallenge
                              }, 
                              success: function(result){ 
                                  if(result == "SUCCESS")
                                    parent.html(answer);  
                                } 
                            });  
                            $( "#ChallengeTruth_answer" ).val("");
                            $( this ).dialog( "close" );
                        }
                    },
                    Cancel: function() {
                            $( this ).dialog( "close" );
                    }
            }
    });

    //<!--********************************-->
    //<!-- Dialog box to see answer Truth -->
    //<!--********************************-->
    $(".answerTruth").click(function() {
        answerTruth = $(this).html();
        $( "#answerTruth" ).html(answerTruth);
        id = $(this).attr("id").substring(2, $(this).attr("id").length);
        type = $(this).attr("id").substring(0,2) == 'TR' ? 'Truth' : 'Dare';
        $("#dialog-form-see-answerTruth").dialog('option', 'title', 'Challenge ' + type + ' #'+id); 
        $( "#dialog-form-see-answerTruth" ).dialog( "open" );
    }); 
    
    $( "#dialog-form-see-answerTruth" ).dialog({
            autoOpen: false
    });

    //<!--*********************************-->
    //<!-- Dialog box to see Truth or Dare -->
    //<!--*********************************-->
    $(".truthOrDare").click(function() {
        truthOrDare = $(this).html();
        $( "#truthOrDare" ).html(truthOrDare);
        id = $(this).attr("id").substring(2, $(this).attr("id").length);
        type = $(this).attr("id").substring(0,2) == 'TR' ? 'Truth' : 'Dare';
        $("#dialog-form-see-truthOrDare").dialog('option', 'title', 'Challenge ' + type + ' #'+id); 
        $("#dialog-form-see-truthOrDare").dialog( "open" );
    }); 
    
    $( "#dialog-form-see-truthOrDare" ).dialog({
            autoOpen: false
    });

    //<!--********************************-->
    //<!-- Dialog box to delete Challenge -->
    //<!--********************************-->
    $(".deleteChallenge").click(function() {
        idChallenge = $(this).attr("id");
        parent = $(this).parent();
        $( "#dialog-confirm-delete" ).dialog( "open" );
    }); 
    
    $( "#dialog-confirm-delete" ).dialog({
            autoOpen: false,
            height: 180,
            width: 360,
            modal: true,
            buttons: {
                    "Delete": function() {
                        $.ajax({ 
                          url: "index.php?r=user/deleteChallenge", 
                          type: "POST", 
                          data: {
                              'idChallenge' : idChallenge
                          }, 
                          success: function(result){ 
                              if(result == "SUCCESS")
                                parent.parent().html('');  
                            } 
                        });  
                        $( this ).dialog( "close" );
                    },
                    Cancel: function() {
                            $( this ).dialog( "close" );
                    }
            }
    });
    
    
    //<!--********************************************-->
    //<!-- Preview image for Upload Challenge Picture -->
    //<!--********************************************-->
    var thumb = $('img#thumb');
    new AjaxUpload('Challenge_pictureUploader', {
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
    input { z-index: 10000 }
</style>

<?php
$this->pageTitle=Yii::app()->name . ' - My Challenges';
$this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
	'My Challenges',
);
?>

<!--******************-->
<!-- Order and Filter -->
<!--******************-->
<div style="margin-top:30px;">
    <span style="float:right; margin-left:18px;">Category: <?php echo CHtml::dropDownList('category',$idCategory,$categories, array('empty' => 'All','onChange'=>'selectCategory(this)')); ?></span>
    <span style="float:right; margin-left:18px;">Gender: <?php echo CHtml::dropDownList('gender',$idGender,$genders, array('empty' => 'All','onChange'=>'selectGender(this)')); ?></span>
    <span style="float:right; margin-left:18px;">Type: <?php echo CHtml::dropDownList('typeChallenge',$idTypeChallenge,$typeChallenges, array('empty' => 'All','onChange'=>'selectTypeChallenge(this)')); ?></span>
    <span style="float:right; margin-left:18px;">Status: <?php echo CHtml::dropDownList('statusChallenge',$idStatusChallenge,$statusChallenges, array('empty' => 'All','onChange'=>'selectStatusChallenge(this)')); ?></span>
    <span style="float:right; margin-left:18px;">Period: <?php echo CHtml::dropDownList('minDateChallenge',$minDateChallenge,$period, array('empty' => 'All','onChange'=>'selectMinDateChallenge(this)')); ?></span>
    <span style="float:right; margin-left:18px;">Private: <?php echo CHtml::dropDownList('privateStatus',$idPrivateStatus,$privateStatus, array('empty' => 'All','onChange'=>'selectPrivateStatus(this)')); ?></span>
    <span style="float:right; margin-left:18px;">From: <?php echo CHtml::dropDownList('userFrom',$idUserFrom,$userFrom, array('empty' => 'All','onChange'=>'selectUserFrom(this)')); ?></span>
</div>
<br />


<!--************-->
<!-- Challenges -->
<!--************-->
<table width="100%" style="border-spacing:5px;">
    <tr>
        <td width="8%">&nbsp;</td>
        <td width="3%">&nbsp;</td>
        <td width="3%">&nbsp;</td>
        <td width="3%">&nbsp;</td>
        <td width="5%">&nbsp;</td>
        <td width="10%">&nbsp;</td>
        <td width="30%">&nbsp;</td>
        <td width="35%">&nbsp;</td>
        <td width="3%">&nbsp;</td>
    </tr>
    <?php foreach($challenges as $row): ?>
        <tr style="background-color: <?php echo $row->status == 1 ? '#c1fb9e' : '#ffd4d5'; ?>">
            <td style="height:60px;"><?php echo Yii::app()->dateFormatter->format('yyyy-MM-dd',$row->createDate); ?></td>
            <td><?php echo $row->truth === null ? "D" : "T"; ?></td>
            <td><?php echo $row->truth === null ? $row->dare->category->category : $row->truth->category->category; ?></td>
            <td><?php if($row->private == 1){echo "P";} ?></td>
            <td><?php echo ($row->voteUp - $row->voteDown) >= 0 ? '+ ' . ($row->voteUp - $row->voteDown) : '- ' . ($row->voteUp - $row->voteDown); ?></td>
            <td><a href="index.php?r=user/userPage&idUser=<?php echo $row->idUserFrom; ?>"><?php echo $row->userFrom->username; ?></a></td>
            <td style="overflow:hidden;">
                <span id="<?php echo $row->truth === null ? 'DA' . $row->dare->idDare : 'TR' . $row->truth->idTruth; ?>" class='truthOrDare' style='cursor:pointer;display:block;height:50px;overflow:hidden;'>
                    <?php echo $row->truth === null ? $row->dare->dare : $row->truth->truth; ?>
                </span>
            </td>
            <td>
                <?php if($row->status == 0): ?>
                    <button type="button" id="<?php echo $row->idChallenge; ?>" class="acceptChallenge<?php echo $row->dare === null ? 'Truth' : 'Dare'; ?>">Accept!</button>
                <?php else: ?>
                    <?php if($row->dare === null): ?>
                        <span id="<?php echo $row->truth === null ? 'DA' . $row->dare->idDare : 'TR' . $row->truth->idTruth; ?>" class='answerTruth' style='cursor:pointer;display:block;height:50px;overflow:hidden;'><?php echo $row->answer; ?></span>
                    <?php else: ?>
                        <a id="<?php echo $row->idChallenge; ?>" class="challengePicture" title="<?php echo "Dare #" . $row->dare->idDare . " (" . $row->dare->category->category . "): " . $row->dare->dare ?>" href="userImages/challenge_original/<?php echo $row->pictureName . '_original' . $row->pictureExtension; ?>"><img src="userImages/challenge_mini/<?php echo $row->pictureName . '_mini' . $row->pictureExtension; ?>" width="48px" height="48px" /></a>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
            <td>
                <button type="button" id="<?php echo $row->idChallenge; ?>" class="deleteChallenge">X</button>
            </td>
        </tr>
    <?php endforeach; ?>
</table>


<!--**************************************-->
<!-- Dialog box to accept Challenges Dare -->
<!--**************************************-->
<div id="dialog-form-accept-challengeDare" style="font-size:0.8em;" title="Accept Challenge">
    <form enctype="multipart/form-data" id="acceptChallenge-formDare" method="post">
        <p>Picture:</p>
	<input id="Challenge_pictureUploader" type="file" />
        <br />
	<div class ="errorMessage" id="errorUpload" style="display: inline-block; margin-left: 5px;"></div> 
        <br /><br />
        <p>Comment:</p>
        <textarea rows="2" cols="60" id="ChallengeDare_answer"></textarea> 
        <input type="hidden" id="tempName" />
        <input type="hidden" id="extension" />
        <input type="hidden" id="validateImage" value="0" />
        <br /><br />
        <p align="center"><u>Preview</u></p>
        <div class="preview"><img id="thumb" width="150px" height="150px" src="" /></div>     
    </form>
</div>
           

<!--***************************************-->
<!-- Dialog box to accept Challenges Truth -->
<!--***************************************-->
<div id="dialog-form-accept-challengeTruth" style="font-size:0.8em;" title="Accept Challenge">
    <form enctype="multipart/form-data" id="acceptChallenge-formTruth" method="post">
        <p>Answer:</p>
        <textarea rows="4" cols="70" name="truthAnswer" id="ChallengeTruth_answer"></textarea>
    </form>
</div>


<!--*************************************-->
<!-- Dialog box to see the Truth or Dare -->
<!--*************************************-->
<div id="dialog-form-see-truthOrDare" title="See answer">
    <div id="truthOrDare">&nbsp;</div>
</div>


<!--******************************************-->
<!-- Dialog box to see answer Challenge Truth -->
<!--******************************************-->
<div id="dialog-form-see-answerTruth" title="See answer">
    <div id="answerTruth">&nbsp;</div>
</div>


<!--********************************-->
<!-- Dialog box to delete Challenge -->
<!--********************************-->
<div id="dialog-confirm-delete" title="Delete Challenge">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to delete this Challenge (you will lose all the points related ot it)?</p>
</div>