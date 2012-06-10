<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.yii.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">
$(function() {

    //<!--*********************-->
    //<!-- Vote Challenge Dare -->
    //<!--*********************-->
    $(".voteChallengeDare").click(function() 
    { 
        if($("#isGuestChallengeDare").val() == 1)
            window.location = "index.php?r=user/login";
             
        var id = $(this).attr("id").substring(3, $(this).attr("id").length);
        var name = $(this).attr("name");
        var dataString = 'idChallenge='+ id + '&vote=' + name;  
        
        var me = $(this); 
        var background = $(this).css("background-image");
        
        me.css("background", "url(/TruthOrDare/images/loading.gif) no-repeat");
        me.css("background-size","100% 100%"); 

        var compteur = $('#nbVoteCD' + id);

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

    //<!--*************************-->
    //<!-- Favorite Challenge Dare -->
    //<!--*************************-->

    $( ".addFavouriteChallengeListDare" ).click(function() {
        idDare = $(this).attr("id").substring(3, $(this).attr("id").length);
        $( "#dialog-form-favourite-challenge-list-dare" ).dialog( "open" ); 
    });
    
    $( "#dialog-form-favourite-challenge-list-dare" ).dialog({
            autoOpen: false,
            height: 210,
            width: 350,
            modal: true,
            buttons: {
                    "Validate": function() {
                            var idUserList = $( "#FavouriteChallengeListDare_idUserList" ).val();
                            if ( idUserList !== '' ) {
                                    $.ajax({ 
                                      url: "index.php?r=site/addFavourite", 
                                      type: "POST", 
                                      data: 'idDare='+ idDare + '&idUserList=' + idUserList, 
                                      success: function(result){ 
                                          if(result == "SUCCESS")
                                            document.getElementById('FCD' + idDare).style.backgroundImage = "url(/TruthOrDare/images/favouriteChosen.png)";  
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

    //<!--*********************-->
    //<!-- Send Challenge Dare -->
    //<!--*********************-->

    $("#dialog-form-challenge-list-dare-sent").dialog({autoOpen: false});
    $("#dialog-form-challenge-list-dare-alreadyexists").dialog({autoOpen: false});
    
    $( ".challengeListDare" ).click(function() {
        idDare = $(this).attr("id").substring(3, $(this).attr("id").length);
        $("#dialog-form-challenge-list-dare").dialog('option', 'title', 'Challenge Dare #'+idDare); 
        $("#dialog-form-challenge-list-dare").dialog( "open" );   
    });
    
    $( "#dialog-form-challenge-list-dare" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            context: $(this), 
            modal: true,
            buttons: {
                    "Validate": function() {    
                        if ( $( "#ChallengeListDare_idUser" ).val() !== '' ) {
                            $.ajax({ 
                              url: "index.php?r=user/sendChallenge", 
                              type: "POST", 
                              data: {
                                  'idUser':$( "#ChallengeListDare_idUser" ).val(),
                                  'private':document.getElementById('ChallengeListDare_private').checked,
                                  'idDare':idDare,
                                  'comment':$( "#ChallengeListDare_comment" ).val()
                              }, 
                              success: function(result){ 
                                  if(result == "SUCCESS"){
                                    $("#dialog-form-challenge-list-dare" ).dialog( "close" );
                                    $("#ChallengeListDare_idUser").val('');
                                    $("#ChallengeListDare_private").checked = 0;
                                    $("#ChallengeListDare_comment").val('');
                                    $("#dialog-form-challenge-list-dare-sent").dialog( "open" );
                                  }
                                  if(result == "ALREADY_EXISTS"){
                                    $("#dialog-form-challenge-list-dare-alreadyexists").dialog( "open" );
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
    //<!-- PictureBox for Dare Challenge Pictures -->
    //<!--***********************************-->
    $("a.challengeDarePicture").fancybox();
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
<!-- Dare Challenge List -->
<!--*********************-->
<input type="hidden" value="<?php echo Yii::app()->user->isGuest; ?>" id="isGuestChallengeDare" />
<?php foreach ($datas as $row) { ?>
    <div style="position:relative;">
        <span style="position:relative;bottom:3px;">
            <img style="margin: 0 5px 0 5px;" src="userImages/profilePicture_mini/<?php echo $row->userTo->profilePicture . "_mini" . $row->userTo->profilePictureExtension; ?>" width="32px" height="32px" />
            <a href='index.php?r=user/userPage&idUser=<?php echo $row->idUserTo; ?>'><?php echo $row->userTo->username; ?></a>
        </span>
        <div style="position:absolute;bottom:0;right:0;height:26px; line-height: 26px;">
                <!-- Favourite -->
                <?php if($this->withFavourites){?>
                    <div class='addFavouriteChallengeListDare' 
                        style='float:right;<?php if($row->nbFavourite > 0){echo "background-image: url(/TruthOrDare/images/favouriteChosen.png);";} ?>'
                        id='FCD<?php echo $row->idDare; ?>' title="Add to your favorites!">&nbsp;
                    </div>
                <?php } ?>

                <!-- Comments -->
                <?php if($this->withComments){ ?>
                    <span style="float:right;font-size:0.8em;">
                        <a href="index.php?r=site/challengeComment&idChallenge=<?php echo $row->idChallenge; ?>" style="text-decoration:none;">
                            <img src="/TruthOrDare/images/comment.png" width="24px" height="24px" title="Add/See comments" />
                            <?php //echo $row->nbComment; ?>
                        </a>
                    </span>
                <?php } ?>

                <!-- Challenge -->
                <span style="float:right; font-size:0.8em;">
                    <?php if($this->withSendChallenge){ ?>
                        <img  class='challengeListDare' id='CLD<?php echo $row->idDare; ?>' src="/TruthOrDare/images/challenge.png" width="24px" height="24px" style="cursor:pointer;" title="Challenge someone!" />
                    <?php } ?>
                    <a href="index.php?r=challenge/challenge&idDare=<?php echo $row->idDare; ?>" style="text-decoration:none;">
                        <img  class='challengeListDare' src="/TruthOrDare/images/seeChallenge.png" width="24px" height="24px" style="cursor:pointer;" title="See other people who realized this challenge!" />
                        <?php echo $row->nbChallenge; ?>
                    </a>
                </span>

                <!-- Like and Dislike -->
                <span style="float:right;">
                    <span id="nbVoteCD<?php echo $row->idChallenge; ?>" style="margin-right:3px;padding:3px;font-weight: bold;border-radius:3px;background-color:#C9E0ED;color:<?php echo ($row->voteUp - $row->voteDown) >= 0 ? 'green' : 'red'; ?>"><?php echo ($row->voteUp - $row->voteDown) >= 0 ? '+' : ''; ?><?php echo($row->voteUp - $row->voteDown); ?></span>
                    <?php if(($this->withVotes) && $row->idUserTo !== $this->idUser){ ?>
                        <a href="" class="voteChallengeDare" style="margin-right:3px;float: right;background: url(/TruthOrDare/images/iDislike.png); text-decoration: none" id="VCD<?php echo $row->idChallenge; ?>" name="down">&nbsp;</a>
                        <a href="" class="voteChallengeDare" style="margin-right:3px;float: right;background: url(/TruthOrDare/images/iLike.png); text-decoration: none;" id="VCD<?php echo $row->idChallenge; ?>" name="up">&nbsp;</a>
                    <?php } ?>
                </span>  
        </div>
    </div>
    <div style="width: 100%; border: 2px dotted #29ABE2; margin-bottom: 30px; ">
        <table width="100px" style="margin:0;">
            <tr>
                <th rowspan="2" width="50px;"><a id="picture" class="challengeDarePicture" title="<?php echo $row->answer; ?>" href="userImages/challenge_original/<?php echo $row->pictureName; ?>_original<?php echo $row->pictureExtension; ?>"><img src="userImages/challenge_mini/<?php echo $row->pictureName; ?>_mini<?php echo $row->pictureExtension; ?>" width="48px" height="48px" /></a></th>
                <td>
                    <b><?php echo "Dare #" . $row->dare->idDare . " (" . $row->dare->category->category . ") accomplished (+2 points):"; ?></b>
                </td>
            </tr>
            <tr><td><?php echo $row->dare->dare; ?></td></tr>
        </table>
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
    <div id="dialog-form-favourite-challenge-list-dare" style="font-size:0.8em; display: none;" title="Choose your list">
        <?php 
            echo CHtml::dropDownList('FavouriteChallengeListDare_UserLists',null, $userLists, array('prompt'=>'Select List','style'=>'width:330px;','id'=>'FavouriteChallengeListDare_idUserList'));
            if($userLists == null)
                {echo "<br /><br /><p style='color:red;'>You haven't created any list yet, please click <a href='index.php?r=user/myLists'>here</a></p>";} 
        ?>
    </div>
<?php } ?>


<!--******************************-->
<!-- Dialog box to send Challenge -->
<!--******************************-->
<?php if($this->withSendChallenge){ ?>
    <div id="dialog-form-challenge-list-dare" style="font-size:0.8em; display: none;" title="Send Challenge">
        <?php echo CHtml::dropDownList('FriendChallengeListDare_username',null, $friends, array('prompt'=>'Select Friend','style'=>'width:330px;','id'=>'ChallengeListDare_idUser')); ?>
        <br />
        Comment:
        <textarea rows="4" cols="50" id="ChallengeListDare_comment"></textarea>
        <br />Private: 
        <?php echo CHtml::checkBox('FriendChallengeListDare_private',false, array('id'=>'ChallengeListDare_private')); ?>
        <?php if($friends == null): ?>
            <br /><br /><p style='color:red;'>You haven't any friend yet!</p> 
        <?php endif; ?>
    </div>

    <div id="dialog-form-challenge-list-dare-sent" style="display: none;">
        <p>Challenge sent!</p>
    </div>

    <div id="dialog-form-challenge-list-dare-alreadyexists" style="display: none;">
        <p>This user already played this challenge or has it in his/her waiting list!</p>
    </div>
<?php } ?>



