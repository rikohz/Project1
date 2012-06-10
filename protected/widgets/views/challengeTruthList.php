<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.yii.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">
$(function() {

    //<!--*********************-->
    //<!-- Vote Challenge Truth -->
    //<!--*********************-->
    $(".voteChallengeTruth").click(function() 
    { 
        if($("#isGuestChallengeTruth").val() == 1)
            window.location = "index.php?r=user/login";
             
        var id = $(this).attr("id").substring(3, $(this).attr("id").length);
        var name = $(this).attr("name");
        var dataString = 'idChallenge='+ id + '&vote=' + name;  
        
        var me = $(this); 
        var background = $(this).css("background-image");
        
        me.css("background", "url(/TruthOrDare/images/loading.gif) no-repeat");
        me.css("background-size","100% 100%"); 

        var compteur = $('#nbVoteCT' + id);

        $.ajax({
            type: "POST",
            url: "index.php?r=site/voteChallenge",
            data: dataString,
            cache: false,

            success: function(html)
            {
                compteur.html(html);
                me.css("background", background);
            } 
        }); 
        return false;
    });

    //<!--**************************-->
    //<!-- Favorite Challenge Truth -->
    //<!--**************************-->

    $( ".addFavouriteChallengeListTruth" ).click(function() {
        idTruth = $(this).attr("id").substring(3, $(this).attr("id").length);
        $( "#dialog-form-favourite-challenge-list-truth" ).dialog( "open" ); 
    });
    
    $( "#dialog-form-favourite-challenge-list-truth" ).dialog({
            autoOpen: false,
            height: 210,
            width: 350,
            modal: true,
            buttons: {
                    "Validate": function() {
                            var idUserList = $( "#FavouriteChallengeListTruth_idUserList" ).val();
                            if ( idUserList !== '' ) {
                                    $.ajax({ 
                                      url: "index.php?r=site/addFavourite", 
                                      type: "POST", 
                                      data: 'idTruth='+ idTruth + '&idUserList=' + idUserList, 
                                      success: function(result){ 
                                          if(result == "SUCCESS")
                                            document.getElementById('FCT' + idTruth).style.backgroundImage = "url(/TruthOrDare/images/favouriteChosen.png)";  
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

    //<!--**********************-->
    //<!-- Send Challenge Truth -->
    //<!--**********************-->

    $("#dialog-form-challenge-list-truth-sent").dialog({autoOpen: false});
    $("#dialog-form-challenge-list-truth-alreadyexists").dialog({autoOpen: false});
    
    $( ".challengeListTruth" ).click(function() {
        idTruth = $(this).attr("id").substring(3, $(this).attr("id").length);
        $("#dialog-form-challenge-list-truth").dialog('option', 'title', 'Challenge Truth #'+idTruth); 
        $("#dialog-form-challenge-list-truth").dialog( "open" );   
    });
    
    $( "#dialog-form-challenge-list-truth" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            context: $(this), 
            modal: true,
            buttons: {
                    "Validate": function() {    
                        if ( $( "#ChallengeListTruth_idUser" ).val() !== '' ) {
                            $.ajax({ 
                              url: "index.php?r=user/sendChallenge", 
                              type: "POST", 
                              data: {
                                  'idUser':$( "#ChallengeListTruth_idUser" ).val(),
                                  'private':document.getElementById('ChallengeListTruth_private').checked,
                                  'idTruth':idTruth,
                                  'comment':$( "#ChallengeListTruth_comment" ).val()
                              }, 
                              success: function(result){ 
                                  if(result == "SUCCESS"){
                                    $("#dialog-form-challenge-list-truth" ).dialog( "close" );
                                    $("#ChallengeListTruth_idUser").val('');
                                    $("#ChallengeListTruth_private").checked = 0;
                                    $("#ChallengeListTruth_comment").val('');
                                    $("#dialog-form-challenge-list-truth-sent").dialog( "open" );
                                  }
                                  if(result == "ALREADY_EXISTS"){
                                    $("#dialog-form-challenge-list-truth-alreadyexists").dialog( "open" );
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
    
    //<!--***********************************-->
    //<!-- PictureBox for Truth Challenge Pictures -->
    //<!--***********************************-->
    $("a.challengeTruthPicture").fancybox();
});

</script>

<!--***************************************************************-->
<!-- If the user tryes to access a level he doesn't have access to -->
<!--***************************************************************-->
<?php if(Yii::app()->user->hasFlash('forbiddenLevel')): ?>
    <br />
    <br />
    <div class="flash-error">
        <?php echo Yii::app()->user->getFlash('forbiddenLevel'); ?>
    </div>
<?php endif ?>
<br />

<!--*********************-->
<!-- Truth Challenge List -->
<!--*********************-->
<input type="hidden" value="<?php echo Yii::app()->user->isGuest; ?>" id="isGuestChallengeTruth" />
<?php foreach ($datas as $row) { ?>
    <div style="position:relative;">
        <span style="position:relative;bottom:3px;">
            <img style="margin: 0 5px 0 5px;" src="userImages/profilePicture_mini/<?php echo $row->userTo->profilePicture . "_mini" . $row->userTo->profilePictureExtension; ?>" width="32px" height="32px" />
            <a href='index.php?r=user/userPage&idUser=<?php echo $row->idUserTo; ?>'><?php echo $row->userTo->username; ?></a>
        </span>
        <div style="position:absolute;bottom:0;right:0;height:26px; line-height: 26px;">
                <!-- Favourite -->
                <?php if($this->withFavourites){?>
                    <div class='addFavouriteChallengeListTruth' 
                        style='float:right;<?php if($row->nbFavourite > 0){echo "background-image: url(/TruthOrDare/images/favouriteChosen.png);";} ?>'
                        id='FCT<?php echo $row->idTruth; ?>' title="Add to your favorites!">&nbsp;
                    </div>
                <?php } ?>

                <!-- Comments -->
                <?php if($this->withComments){ ?>
                    <span style="float:right;font-size:0.8em;">
                        <a href="index.php?r=site/challengeComment&idChallenge=<?php echo $row->idChallenge; ?>" style="text-decoration:none;">
                            <img src="/TruthOrDare/images/comment.png" width="24px" height="24px" title="Add/See comments" />
                            0<?php //echo $row->nbComment; ?>
                        </a>
                    </span>
                <?php } ?>

                <!-- Challenge -->
                <span style="float:right; font-size:0.8em;">
                    <?php if($this->withSendChallenge){ ?>
                        <img  class='challengeListTruth' id='CLT<?php echo $row->idTruth; ?>' src="/TruthOrDare/images/challenge.png" width="24px" height="24px" style="cursor:pointer;" title="Challenge someone!" />
                    <?php } ?>
                    <a href="index.php?r=challenge/challenge&idTruth=<?php echo $row->idTruth; ?>" style="text-decoration:none;">
                        <img  class='challengeListTruth' src="/TruthOrDare/images/seeChallenge.png" width="24px" height="24px" style="cursor:pointer;" title="See other people who realized this challenge!" />
                        <?php echo $row->nbChallenge; ?>
                    </a>
                </span>

                <!-- Like and Dislike -->
                <span style="float:right;">
                    <span id="nbVoteCT<?php echo $row->idChallenge; ?>" style="margin-right:3px;padding:3px;font-weight: bold;border-radius:3px;background-color:#C9E0ED;color:<?php echo ($row->voteUp - $row->voteDown) >= 0 ? 'green' : 'red'; ?>"><?php echo ($row->voteUp - $row->voteDown) >= 0 ? '+' : ''; ?><?php echo($row->voteUp - $row->voteDown); ?></span>
                    <?php if(($this->withVotes) && $row->idUserTo !== $this->idUser){ ?>
                        <a href="" class="voteChallengeTruth" style="margin-right:3px;float: right;background: url(/TruthOrDare/images/iDislike.png); text-decoration: none" id="VCT<?php echo $row->idChallenge; ?>" name="down">&nbsp;</a>
                        <a href="" class="voteChallengeTruth" style="margin-right:3px;float: right;background: url(/TruthOrDare/images/iLike.png); text-decoration: none;" id="VCT<?php echo $row->idChallenge; ?>" name="up">&nbsp;</a>
                    <?php } ?>
                </span>  
        </div>
    </div>
    <div style="width: 100%; border: 2px dotted #29ABE2; margin-bottom: 30px; ">
        <div style="margin:5px;">
            <b><?php echo "Truth #" . $row->idTruth . " (" . $row->truth->category->category . ") accomplished : " . $row->truth->truth; ?></b>
        </div>
        <div style="margin:5px;"><?php echo $row->answer; ?></div>
    </div>
<?php } ?>

<!--************-->
<!-- Link Pager -->
<!--************-->
<div style="text-align: center;">
    <?php $this->widget('AjaxFormCLinkPager', array(
        'pages' => $pages,
        'maxButtonCount'=>10,
        'header'=>"",
        'idDivUpdate'=>$this->idDivUpdate,
        'idForm'=>$this->idFormCriteria
    )) ?>
</div>


<!--**************************************************-->
<!-- Dialog box to choose which list to add favourite -->
<!--**************************************************-->
<?php if($this->withFavourites){ ?>
    <div id="dialog-form-favourite-challenge-list-truth" style="font-size:0.8em; display: none;" title="Choose your list">
        <?php 
            echo CHtml::dropDownList('FavouriteChallengeListTruth_UserLists',null, $userLists, array('prompt'=>'Select List','style'=>'width:330px;','id'=>'FavouriteChallengeListTruth_idUserList'));
            if($userLists == null)
                {echo "<br /><br /><p style='color:red;'>You haven't created any list yet, please click <a href='index.php?r=user/myLists'>here</a></p>";} 
        ?>
    </div>
<?php } ?>


<!--******************************-->
<!-- Dialog box to send Challenge -->
<!--******************************-->
<?php if($this->withSendChallenge){ ?>
    <div id="dialog-form-challenge-list-truth" style="font-size:0.8em; display: none;" title="Send Challenge">
        <?php echo CHtml::dropDownList('FriendChallengeListTruth_username',null, $friends, array('prompt'=>'Select Friend','style'=>'width:330px;','id'=>'ChallengeListTruth_idUser')); ?>
        <br />
        Comment:
        <textarea rows="4" cols="50" id="ChallengeListTruth_comment"></textarea>
        <br />Private: 
        <?php echo CHtml::checkBox('FriendChallengeListTruth_private',false, array('id'=>'ChallengeListTruth_private')); ?>
        <?php if($friends == null): ?>
            <br /><br /><p style='color:red;'>You haven't any friend yet!</p> 
        <?php endif; ?>
    </div>

    <div id="dialog-form-challenge-list-truth-sent" style="display: none;">
        <p>Challenge sent!</p>
    </div>

    <div id="dialog-form-challenge-list-truth-alreadyexists" style="display: none;">
        <p>This user already played this challenge or has it in his/her waiting list!</p>
    </div>
<?php } ?>



