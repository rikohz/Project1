<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.yii.js"></script>
<script type="text/javascript">
$(function() {

    //Vote Management
    $(".voteTruth").click(function() 
    { 
        if($("#isGuestTruth").val() == 1)
            window.location = "index.php?r=user/login";
             
        var id = $(this).attr("id").substring(2, $(this).attr("id").length);
        var name = $(this).attr("name");
        var dataString = 'idTruth='+ id + '&vote=' + name;  
        
        var me = $(this); 
        var background = $(this).css("background-image");
        
        me.css("background", "url(/TruthOrDare/images/loading.gif) no-repeat");
        me.css("background-size","100% 100%"); 

        var compteur = $('#nbVoteT' + id);

        $.ajax({
            type: "POST",
            url: "index.php?r=site/vote",
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

    //Favourite Management
    $( "#dialog-form-favourite-truth" ).dialog({
            autoOpen: false,
            height: 210,
            width: 350,
            modal: true,
            buttons: {
                    "Validate": function() {
                            var idUserList = $( "#FavouriteTruth_idUserList" ).val();
                            if ( idUserList !== '' ) {
                                    $.ajax({ 
                                      url: "index.php?r=site/addFavourite", 
                                      type: "POST", 
                                      data: 'idTruth='+ idTruth + '&idUserList=' + idUserList, 
                                      success: function(result){ 
                                          if(result == "SUCCESS")
                                            document.getElementById('FT' + idTruth).style.backgroundImage = "url(/TruthOrDare/images/favouriteChosen.png)";  
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

    $( ".addFavouriteTruth" ).click(function() {
        idTruth = $(this).attr("id").substring(2, $(this).attr("id").length);
        $( "#dialog-form-favourite-truth" ).dialog( "open" ); 
    });

    //Send Challenge
    $("#dialog-form-challenge-truth-sent").dialog({autoOpen: false});
    $("#dialog-form-challenge-truth-alreadyexists").dialog({autoOpen: false});
    $( "#dialog-form-challenge-truth" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            context: $(this), 
            modal: true,
            buttons: {
                    "Validate": function() {    
                        if ( $( "#ChallengeTruth_idUser" ).val() !== '' ) {
                            $.ajax({ 
                              url: "index.php?r=user/sendChallenge", 
                              type: "POST", 
                              data: {
                                  'idUser':$( "#ChallengeTruth_idUser" ).val(),
                                  'private':document.getElementById('ChallengeTruth_private').checked,
                                  'idTruth':idTruth,
                                  'comment':$( "#ChallengeTruth_comment" ).val()
                              }, 
                              success: function(result){ 
                                  if(result == "SUCCESS"){
                                    $("#dialog-form-challenge-truth" ).dialog( "close" );
                                    $("#ChallengeTruth_idUser").val('');
                                    $("#ChallengeTruth_private").checked = 0;
                                    $("#ChallengeTruth_comment").val('');
                                    $("#dialog-form-challenge-truth-sent").dialog( "open" );
                                  }
                                  if(result == "ALREADY_EXISTS"){
                                    $("#dialog-form-challenge-truth-alreadyexists").dialog( "open" );
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

    $( ".challengeTruth" ).click(function() {
        idTruth = $(this).attr("id").substring(2, $(this).attr("id").length);
        $("#dialog-form-challenge-truth").dialog('option', 'title', 'Challenge Truth #'+idTruth); 
        $("#dialog-form-challenge-truth").dialog( "open" );   
    });
  
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


<!--************-->
<!-- Truth List -->
<!--************-->
<input type="hidden" value="<?php echo Yii::app()->user->isGuest; ?>" id="isGuestTruth" />
<?php foreach ($datas as $row) { ?>
    <div style="height:26px; line-height: 26px;">
        <!-- #Ref of Truth and category -->
        <span>
            <?php echo '#' . $row['idTruth']; ?>&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo $row->category->category; ?>
        </span>

        <!-- Author informations and Date -->
        <span>
            &nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo $row->anonymous == 1 ? 'Anonymous' : "<a href='index.php?r=user/userPage&idUser=" . $row->user->idUser . "'>" . $row->user->username . "</a>"; ?>
            <?php if($this->withAuthorScores){ ?>
                <?php echo MyFunctions::getTruthRankName($row->user->scoreTruth->score); ?>&nbsp;&nbsp;-&nbsp;&nbsp;
                <?php echo MyFunctions::getDareRankName($row->user->scoreDare->score); ?>&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php } ?>
        </span>

        <!-- Favourite -->
        <?php if($this->withFavourites){?>
            <div class='addFavouriteTruth' 
                style='float:right;<?php if($row->nbFavourite > 0){echo "background-image: url(/TruthOrDare/images/favouriteChosen.png);";} ?>'
                id='FT<?php echo $row->idTruth; ?>' title="Add to your favorites!">&nbsp;
            </div>
        <?php } ?>

        <!-- Comments -->
        <?php if($this->withComments){ ?>
            <span style="float:right;font-size:0.8em;">
                <a href="index.php?r=site/comment&idTruth=<?php echo $row['idTruth']; ?>" style="text-decoration:none;">
                    <img src="/TruthOrDare/images/comment.png" width="24px" height="24px" title="Add/See comments" />
                    <?php echo $row->nbComment; ?>
                </a>
            </span>
        <?php } ?>

        <!-- Challenge -->
        <span style="float:right; font-size:0.8em;">
            <?php if($this->withSendChallenge){ ?>
                <img  class='challengeTruth' id='CT<?php echo $row->idTruth; ?>' src="/TruthOrDare/images/challenge.png" width="24px" height="24px" style="cursor:pointer;" title="Challenge someone!" />
            <?php } ?>
            <a href="index.php?r=challenge/challenge&idTruth=<?php echo $row['idTruth']; ?>" style="text-decoration:none;">
                <img  class='challengeTruth' src="/TruthOrDare/images/seeChallenge.png" width="24px" height="24px" style="cursor:pointer;" title="See people who realized this challenge!" />
                <?php echo $row->nbChallenge; ?>
            </a>
        </span>

        <!-- Like and Dislike -->
        <span style="float:right;">
            <span id="nbVoteT<?php echo $row['idTruth']; ?>" style="padding:3px;font-weight: bold;border-radius:3px;background-color:#C9E0ED;color:<?php echo ($row['voteUp'] - $row['voteDown']) >= 0 ? 'green' : 'red'; ?>"><?php echo ($row['voteUp'] - $row['voteDown']) >= 0 ? '+' : ''; ?><?php echo $row['voteUp'] - $row['voteDown']; ?></span>
            <?php if(($this->withVotes) && ($row->user->idUser !== $this->idUser)){ ?>
                <a href="" class="voteTruth" style="background: url(/TruthOrDare/images/iLike.png); text-decoration: none;" id="VT<?php echo $row['idTruth']; ?>" name="up">&nbsp;</a>
                <a href="" class="voteTruth" style="background: url(/TruthOrDare/images/iDislike.png); text-decoration: none" id="VT<?php echo $row['idTruth']; ?>" name="down">&nbsp;</a>
            <?php } ?>
        </span>  

    </div>
    <!-- Truth -->
    <div class="boxTruthOrDare">
        <div style="margin:10px;"><?php echo $row['truth']; ?></div>
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
    <div id="dialog-form-favourite-truth" style="font-size:0.8em; display: none;" title="Choose your list">
        <?php 
            echo CHtml::dropDownList('FavouriteTruth_UserLists',null, $userLists, array('prompt'=>'Select List','style'=>'width:330px;','id'=>'FavouriteTruth_idUserList'));
            if($userLists == null)
                {echo "<br /><br /><p style='color:red;'>You haven't created any list yet, please click <a href='index.php?r=user/myLists'>here</a></p>";} 
        ?>
    </div>
<?php } ?>


<!--******************************-->
<!-- Dialog box to send Challenge -->
<!--******************************-->
<?php if($this->withSendChallenge){ ?>
    <div id="dialog-form-challenge-truth" style="font-size:0.8em; display: none;" title="Send Challenge">
        <?php echo CHtml::dropDownList('FriendTruth_username',null, $friends, array('prompt'=>'Select Friend','style'=>'width:330px;','id'=>'ChallengeTruth_idUser')); ?>
        <br />
        Comment:
        <textarea rows="4" cols="50" id="ChallengeTruth_comment"></textarea>
        <br />Private: 
        <?php echo CHtml::checkBox('FriendTruth_private',false, array('id'=>'ChallengeTruth_private')); ?>
        <?php if($friends == null): ?>
            <br /><br /><p style='color:red;'>You haven't any friend yet!</p> 
        <?php endif; ?>
    </div>

    <div id="dialog-form-challenge-truth-sent" style="display: none;">
        <p>Challenge sent!</p>
    </div>

    <div id="dialog-form-challenge-truth-alreadyexists" style="display: none;">
        <p>This user already played this challenge or has it in his/her waiting list!</p>
    </div>
<?php } ?>



