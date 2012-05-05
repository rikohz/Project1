<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript">
$(function() {

    //Vote Truth or Dare
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
    
    //Vote Challenge
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

    //Favourite Management
    var idDare = null;
    var idTruth = null;
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#dialog-form" ).dialog({
            autoOpen: false,
            height: 210,
            width: 350,
            modal: true,
            buttons: {
                    "Validate": function() {
                            var idUserList = $( "#UserList_name" ).val();
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
        if($(this).attr("tag") !== 'Chosen')
        {
            if($(this).attr("id").substring(0, 2) == "FT")
            {
                idTruth = $(this).attr("id").substring(2, $(this).attr("id").length);
                idDare = null;
                $( "#dialog-form" ).dialog( "open" );
            }
            if($(this).attr("id").substring(0, 2) == "FD")
            {
                idDare = $(this).attr("id").substring(2, $(this).attr("id").length);
                idTruth = null;
                $( "#dialog-form" ).dialog( "open" );
            }
        }    
    });
  
});

</script>

<!--***************************-->
<!-- Form to add Wall Messages -->
<!--***************************-->
<?php if($this->withFormMessage == 1): ?>
    <div style="border:1px black solid;">
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


<!--******-->
<!-- Wall -->
<!--******-->
<input type="hidden" value="<?php echo Yii::app()->user->isGuest; ?>" id="isGuest" />
<div style="border:1px black solid;">
    <?php foreach($wall as $row): ?>
        <!--******-->
        <!-- Date -->
        <!--******-->
        <div style="text-align:right;"><?php echo $row['createDate']; ?></div>
        <div>
            <table width="100%" style="border:1px black solid;">
                <tr>
                    <th rowspan="2"><img src="userImages/profilePicture_mini/<?php echo $row['picture'] . '_mini' . $row['pictureExtension']; ?>" /></th>
                    <td width="100%">
                        <span style="float:left;">
                            <b>
                                <?php 
                                    switch($row['type']){
                                        case 'WallMessage':
                                            echo $row['displayUsername'] . " says:";
                                            break;
                                        case 'Challenge' :
                                            echo $row['displayUsername'] . " has successfuly realized the following challenge (" . $row['category'] . ") :";
                                            break;
                                        case 'Truth' :
                                        case 'Dare' :
                                            echo $row['displayUsername'] . " has submitted the following " . $row['type'] . " (" . $row['category'] . ") :";
                                            break;
                                    }
                                ?>
                            </b>
                        </span>
                        <span style="float:right;">
                            <!--*****************-->
                            <!-- Truths or Dares -->
                            <!--*****************-->
                            <?php if($row['type'] == "Truth" || $row['type'] == "Dare"): ?>
                                <?php $ref = substr($row['type'],0,1) . $row['id']; ?>

                                <!-- Like and Dislike -->
                                <?php if($this->withVotes){ ?>
                                    <span style="float:right;" id="nbVote<?php echo $ref; ?>"><?php echo $row['vote']; ?></span>
                                    <a href="" class="voteTruthOrDare" style="background-image: url(/TruthOrDare/images/iDislike.png);" id="V<?php echo $ref; ?>" name="down">&nbsp;</a>
                                    <a href="" class="voteTruthOrDare" style="background-image: url(/TruthOrDare/images/iLike.png);" id="V<?php echo $ref; ?>" name="up">&nbsp;</a>
                                <?php } ?>

                                <!-- Comments -->
                                <?php if($this->withComments){ ?>
                                    &nbsp;&nbsp;<a style="margin-right:10px; float:right;" href="index.php?r=site/comment&id<?php echo $row['type'] . "=" . $row['id']; ?>">See the <?php echo $row['nbComment']; ?> comments</a>
                                <?php } ?>

                                <!-- Favourite -->
                                <?php if($this->withFavourites){?>
                                    <div tag='<?php echo $row['nbFavourite'] > 0 ? 'Chosen' : ''; ?>' 
                                         class='addFavourite' 
                                         <?php if($row['nbFavourite'] > 0){echo " style='background-image: url(/TruthOrDare/images/favouriteChosen.png);' ";} ?> 
                                         id='F<?php echo $ref; ?>'>&nbsp;
                                    </div>
                                <?php } ?>
                                <br />
                                <br />
                            <?php endif; ?>

                            <!--************-->
                            <!-- Challenges -->
                            <!--************-->
                            <?php if($row['type'] == "Challenge"): ?>
                                <?php $ref = substr($row['type'],0,1) . $row['id']; ?>

                                <!-- Like and Dislike -->
                                <?php if($this->withVotes){ ?>
                                    <span style="float:right;" id="nbVote<?php echo $ref; ?>"><?php echo $row['vote']; ?></span>
                                    <a href="" class="voteChallenge" style="background-image: url(/TruthOrDare/images/iDislike.png);" id="V<?php echo $ref; ?>" name="down">&nbsp;</a>
                                    <a href="" class="voteChallenge" style="background-image: url(/TruthOrDare/images/iLike.png);" id="V<?php echo $ref; ?>" name="up">&nbsp;</a>
                                <?php } ?>
                                    
                                <!-- Comments -->
                                <?php if($this->withComments){ ?>
                                    &nbsp;&nbsp;<a style="margin-right:10px; float:right;" href="index.php?r=site/challengeComment&id<?php echo $row['type'] . "=" . $row['id']; ?>">See the <?php echo $row['nbComment']; ?> comments</a>
                                <?php } ?>
                                <br />
                                <br />
                            <?php endif; ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $row['content']; ?></td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>


<!--**************************************************-->
<!-- Dialog box to choose which list to add favourite -->
<!--**************************************************-->
<?php if($this->withFavourites){ ?>
    <div id="dialog-form" style="font-size:0.8em;" title="Choose your list">
        <?php 
            $form=$this->beginWidget('CActiveForm', array('id'=>'addFavourite-form'));
            echo $form->dropDownList($modelUserList,'name', $userLists, array('prompt'=>'Select List','style'=>'width:330px;','id'=>'UserList_name'));
            if($userLists == null)
                {echo "<br /><br /><p style='color:red;'>You haven't created any list yet, please click <a href='index.php?r=user/favourite'>here</a></p>";} 
            $this->endWidget(); 
        ?>
    </div>
<?php } ?>