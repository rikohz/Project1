<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">
$(function() {

//<!--********************-->
//<!-- Vote Truth or Dare -->
//<!--********************-->

    $(".voteTruthOrDare").click(function() 
    { 
        if($("#isGuest").val() == 1)
            window.location = "index.php?r=user/login";
             
        var id = $(this).attr("id").substring(2, $(this).attr("id").length);
        var name = $(this).attr("name");
        var type = $(this).attr("id").substring(0, 2) === "VT" ? "Truth" : "Dare";
        var initial = $(this).attr("id").substring(0, 2) === "VT" ? "T" : "D";
        var dataString = 'id' + type + '='+ id + '&vote=' + name;    
        var parent = $(this);

        var tampon = $(this).html();
        var compteur = document.getElementById('nbVote' + initial + id);
        $(this).fadeIn(200).html('<img src="/TruthOrDare/images/loading.gif" />');

        $.ajax({
            type: "POST",
            url: "index.php?r=site/vote",
            data: dataString,
            cache: false,

            success: function(html)
            {
                compteur.innerHTML = html;
                parent.html(tampon);
            } 
        }); 
        return false;
    });
    
//<!--****************-->
//<!-- Vote Challenge -->
//<!--****************-->
    $(".voteChallenge").click(function() 
    { 
        if($("#isGuest").val() == 1)
            window.location = "index.php?r=user/login";
             
        var id = $(this).attr("id").substring(2, $(this).attr("id").length);
        var name = $(this).attr("name");
        var dataString = 'idChallenge='+ id + '&vote=' + name;    
        var parent = $(this);

        var tampon = $(this).html();
        var compteur = document.getElementById('nbVoteC' + id);
        $(this).fadeIn(200).html('<img src="/TruthOrDare/images/loading.gif" />');

        $.ajax({
            type: "POST",
            url: "index.php?r=site/voteChallenge",
            data: dataString,
            cache: false,

            success: function(html)
            {
                compteur.innerHTML = html;
                parent.html(tampon);
            } 
        }); 
        return false;
    });

//<!--**********************-->
//<!-- Favourite Management -->
//<!--**********************-->

    var idDare = null;
    var idTruth = null;
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#dialog-form-favourite" ).dialog({
            autoOpen: false,
            height: 210,
            width: 350,
            modal: true,
            buttons: {
                    "Validate": function() {
                            var idUserList = $( "#Favourite_idUserList" ).val();
                            var dataString = idTruth === null ? 'idDare=' + idDare : 'idTruth=' + idTruth;
                            dataString = dataString +  '&idUserList=' + idUserList
                            if ( idUserList !== '' ) {
                                    $.ajax({ 
                                      url: "index.php?r=site/addFavourite", 
                                      type: "POST", 
                                      data: dataString, 
                                      success: function(result){ 
                                          if(result == "SUCCESS")
                                          {
                                              if(idTruth === null)
                                                document.getElementById('FD' + idDare).style.backgroundImage = "url(/TruthOrDare/images/favouriteChosen.png)";                      
                                              else
                                                document.getElementById('FT' + idTruth).style.backgroundImage = "url(/TruthOrDare/images/favouriteChosen.png)"; 
                                          }
                                        } 
                                    });  
                                    $( this ).dialog( "close" );
                            }
                    },
                    Cancel: function() {
                            $( this ).dialog( "close" );
                    }
            }
    });

    $( ".addFavourite" ).click(function() {
            if($(this).attr("id").substring(0, 2) == "FT")
            {
                idTruth = $(this).attr("id").substring(2, $(this).attr("id").length);
                idDare = null;
                $( "#dialog-form-favourite" ).dialog( "open" );
            }
            if($(this).attr("id").substring(0, 2) == "FD")
            {
                idDare = $(this).attr("id").substring(2, $(this).attr("id").length);
                idTruth = null;
                $( "#dialog-form-favourite" ).dialog( "open" );
            } 
    });

    //Send Challenge
    $("#dialog-form-challenge-sent").dialog({autoOpen: false});
    $("#dialog-form-challenge-alreadyexists").dialog({autoOpen: false});
    $( "#dialog-form-challenge" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            context: $(this), 
            modal: true,
            buttons: {
                    "Validate": function() {    
                        if ( $( "#Challenge_idUser" ).val() !== '' ) {
                            var datastring = idTruth === null ? 'idDare='+idDare : 'idTruth='+idTruth;
                            datastring = datastring + "&idUser="+$( "#Challenge_idUser" ).val();
                            datastring = datastring + "&private="+document.getElementById('Challenge_private').checked;
                            datastring = datastring + "&comment="+$( "#Challenge_comment" ).val(),
                            $.ajax({ 
                              url: "index.php?r=user/sendChallenge", 
                              type: "POST", 
                              data: datastring, 
                              success: function(result){ 
                                  if(result == "SUCCESS"){
                                    $("#dialog-form-challenge" ).dialog( "close" );
                                    $("#Challenge_idUser").val('');
                                    $("#Challenge_private").checked = 0;
                                    $("#Challenge_comment").val('');
                                    $("#dialog-form-challenge-sent").dialog( "open" );
                                  }
                                  if(result == "ALREADY_EXISTS"){
                                    $("#dialog-form-challenge-alreadyexists").dialog( "open" );
                                  }
                                } 
                            });  
                        }
                    },
                    Cancel: function() {
                            $( this ).dialog( "close" );
                    }
            }
    });

    $( ".challenge" ).click(function() {
        if($(this).attr("id").substring(0, 2) == "CT")
        {
            idTruth = $(this).attr("id").substring(2, $(this).attr("id").length);
            idDare = null;
            $("#dialog-form-challenge").dialog('option', 'title', 'Challenge Truth #'+idTruth); 
            $("#dialog-form-challenge").dialog( "open" );  
        }
        if($(this).attr("id").substring(0, 2) == "CD")
        {
            idDare = $(this).attr("id").substring(2, $(this).attr("id").length);
            idTruth = null;
            $("#dialog-form-challenge").dialog('option', 'title', 'Challenge Dare #'+idDare); 
            $("#dialog-form-challenge").dialog( "open" );  
        }
    });
  
});

//<!--***********************************-->
//<!-- PictureBox for Challenge Pictures -->
//<!--***********************************-->
$(document).ready(function() {
            $("a.challengePicture").fancybox();
    });
</script>

<!--***************************-->
<!-- Form to add Wall Messages -->
<!--***************************-->
<?php if($this->withFormMessage == 1): ?>
    <div>
        <div class="form">
            <?php $form = $this->beginWidget('CActiveForm', array('id'=>'wall-form')); ?>
            <label style="padding: 0 10px 0 0; margin: 0; display: block;">
                <div class="row"><?php echo $form->textArea($model,'content',array('style'=>'width: 100%; border: 1px solid #333; padding: 4px;')); ?></div>
                </label>
                <div class="row" style="text-align:right; margin-right:2px;"><?php echo CHtml::submitButton('Submit'); ?></div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
<?php endif; ?>

<br />

<!--******-->
<!-- Wall -->
<!--******-->
<input type="hidden" value="<?php echo Yii::app()->user->isGuest; ?>" id="isGuest" />
<div>
    <?php foreach($wall as $row): ?>
        <div style="display: table; width:100%;">
            <span style="width:50%; display:table-cell; vertical-align: bottom;">
                <img style="margin: 0 5px 0 5px;" src="userImages/profilePicture_mini/<?php echo $row['userPicture']; ?>" width="32px" height="32px" />
                <?php echo "<a href='index.php?r=user/userPage&idUser=" . $row['idDisplayUser'] . "'>" . $row['displayUsername'] . "</a>"; ?>
                <?php echo "  (" . $row['rankTruth'] . " - " . $row['rankDare'] . ")" ; ?>
            </span>
            <span style="width:50%; display:table-cell; vertical-align: bottom; text-align: right;">

                <!--*****************-->
                <!-- Truths or Dares -->
                <!--*****************-->
                <?php if($row['type'] == "Truth" || $row['type'] == "Dare"): ?>
                    <?php $ref = substr($row['type'],0,1) . $row['id']; ?>
                    <div style="height:26px; line-height: 26px;">

                        <!-- Favourite -->
                        <?php if($this->withFavourites){?>
                            <div class='addFavourite' 
                                <?php if($row['nbFavourite'] > 0){echo " style='background-image: url(/TruthOrDare/images/favouriteChosen.png);' ";} ?> 
                                id='F<?php echo $ref; ?>'>&nbsp;
                            </div>
                        <?php } ?>
                        
                        <!-- Comments -->
                        <?php if($this->withComments){ ?>
                            <span style="float:right;font-size:0.8em;">
                                <a href="index.php?r=site/comment&id<?php echo $row['type'] . "=" . $row['id']; ?>" style="text-decoration:none;">
                                    <img src="/TruthOrDare/images/comment.png" width="24px" height="24px" title="Add/See comments" />
                                    <?php echo $row['nbComment']; ?>
                                </a>
                            </span>
                        <?php } ?>

                        <!-- Challenge -->
                        <?php if($this->withSendChallenge){ ?>
                            <span style="float:right; font-size:0.8em;">
                                <img  class='challenge' id='C<?php echo $ref; ?>' src="/TruthOrDare/images/challenge.png" width="24px" height="24px" style="cursor:pointer;" title="Challenge someone!" />
                            </span>
                        <?php } ?>
                            
                            
                        <!-- Like and Dislike -->
                        <?php if($this->withVotes && $row['idDisplayUser'] !== $this->idCurrentUser){ ?>
                        <span style="float:right;">
                            <span id="nbVote<?php echo $ref; ?>" style="padding:3px;font-weight: bold;border-radius:3px;background-color:#C9E0ED;color:<?php echo $row['vote'] >= 0 ? 'green' : 'red'; ?>"><?php echo $row['vote'] >= 0 ? '+' : '-'; ?><?php echo $row['vote']; ?></span>
                            <a href="" class="voteTruthOrDare" style="background-image: url(/TruthOrDare/images/iLike.png);text-decoration:none;" id="V<?php echo $ref; ?>" name="up">&nbsp;</a>
                            <a href="" class="voteTruthOrDare" style="background-image: url(/TruthOrDare/images/iDislike.png);text-decoration:none;" id="V<?php echo $ref; ?>" name="down">&nbsp;</a>
                        </span>
                        <?php } ?>
                    </div>
                <?php endif; ?>
                <!--************-->
                <!-- Challenges -->
                <!--************-->
                <?php if($row['type'] == "ChallengeTruth" || $row['type'] == "ChallengeDare"): ?>
                    <?php $ref = substr($row['type'],0,1) . $row['id']; ?>
                    <div style="height:26px; line-height: 26px;">

                        <!-- Comments -->
                        <?php if($this->withComments){ ?>
                            <span style="float:right;font-size:0.8em;">
                                <a href="index.php?r=site/challengeComment&idChallenge=<?php echo $row['id']; ?>" style="text-decoration:none;">
                                    <img src="/TruthOrDare/images/comment.png" width="24px" height="24px" title="Add/See comments" />
                                    <?php //echo $row->nbComment; ?>
                                </a>
                            </span>
                        <?php } ?>

                        <!-- Like and Dislike -->
                        <?php if($this->withVotes && $row['idDisplayUser'] !== $this->idCurrentUser){ ?>
                            <span style="float:right;">
                                <span id="nbVote<?php echo $ref; ?>" style="padding:3px;font-weight: bold;border-radius:3px;background-color:#C9E0ED;color:<?php echo $row['vote'] >= 0 ? 'green' : 'red'; ?>"><?php echo $row['vote'] >= 0 ? '+' : '-'; ?><?php echo $row['vote']; ?></span>
                                <a href="" class="voteChallenge" style="float: right; background-image: url(/TruthOrDare/images/iLike.png);text-decoration:none;" id="V<?php echo $ref; ?>" name="up">&nbsp;</a>
                                <a href="" class="voteChallenge" style="float: right; background-image: url(/TruthOrDare/images/iDislike.png);text-decoration:none;" id="V<?php echo $ref; ?>" name="down">&nbsp;</a>
                            </span>
                        <?php } ?>
                    </div>
                <?php endif; ?>
            </span>
        </div>
        <div style="border:2px black solid; margin-bottom: 20px; margin-left:15px; padding: 5px;">
            <table width="100px" style="margin:0;">
                <tr>
                    <?php if($row['type'] == "ChallengeDare"): ?>
                        <th rowspan="2" width="50px;"><a id="picture" class="challengePicture" title="<?php echo $row['challengeTruthOrCommentDare']; ?>" href="userImages/challenge_original/<?php echo $row['pictureChallengeDare']; ?>"><img src="userImages/challenge_mini/<?php echo $row['pictureChallengeDareMini']; ?>" width="48px" height="48px" /></a></th>
                    <?php endif; ?>
                    <?php if($row['type'] !== "WallMessage" && $row['type'] !== "RankUpgrade"): ?>
                        <td>
                            <b>
                                <?php 
                                    switch($row['type']){
                                        case 'ChallengeTruth' :
                                            echo "Truth #" . $row['id'] . " (" . $row['category'] . ") accomplished : " . $row['challengeTruthOrCommentDare'];
                                            break;
                                        case 'ChallengeDare' :
                                            echo "Dare #" . $row['id'] . " (" . $row['category'] . ") accomplished (+2 points):";
                                            break;
                                        case 'Truth' :
                                        case 'Dare' :
                                            echo "I just submitted the " . $row['type'] . " #" . $row['id'] . " (" . $row['category'] . ") :";
                                            break;
                                    }
                                ?>
                            </b>
                        </td>
                    <?php endif; ?>
                </tr>
                <tr><td><?php echo $row['content']; ?></td></tr>
            </table>
        </div>
    <?php endforeach; ?>


<!--**************************************************-->
<!-- Dialog box to choose which list to add favourite -->
<!--**************************************************-->
<?php if($this->withFavourites): ?>
    <div id="dialog-form-favourite" style="font-size:0.8em;" title="Choose your list">
        <?php 
            echo CHtml::dropDownList('Favourite_UserLists',null, $userLists, array('prompt'=>'Select List','style'=>'width:330px;','id'=>'Favourite_idUserList'));
            if($userLists == null)
                {echo "<br /><br /><p style='color:red;'>You haven't created any list yet, please click <a href='index.php?r=user/favourite'>here</a></p>";} 
        ?>
    </div>
<?php endif; ?>


<!--******************************-->
<!-- Dialog box to send Challenge -->
<!--******************************-->
<?php if($this->withSendChallenge){ ?>
    <div id="dialog-form-challenge" style="font-size:0.8em;" title="Send Challenge">
        <?php echo CHtml::dropDownList('FriendChallenge_username',null, $friends, array('prompt'=>'Select Friend','style'=>'width:330px;','id'=>'Challenge_idUser')); ?>
        <br />
        Comment:
        <textarea rows="4" cols="50" id="Challenge_comment"></textarea>
        <br />Private: 
        <?php echo CHtml::checkBox('ChallengeFriend_private',false, array('id'=>'Challenge_private')); ?>
        <?php if($friends == null): ?>
            <br /><br /><p style='color:red;'>You haven't any friend yet!</p> 
        <?php endif; ?>
    </div>

    <div id="dialog-form-challenge-sent">
        <p>Challenge sent!</p>
    </div>

    <div id="dialog-form-challenge-alreadyexists">
        <p>This user already played this challenge or has it in his/her waiting list!</p>
    </div>
<?php } ?>