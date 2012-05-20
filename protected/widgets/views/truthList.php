<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
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
        var parent = $(this);

        var tampon = $(this).html();
        var compteur = document.getElementById('nbVoteT' + id);
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

    //Favourite Management
    var idTruth = null;
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
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
    
    <!-- #Ref of Truth and category -->
    <span>
        <?php echo '#' . $row['idTruth']; ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo $row->category->category; ?>
    </span>

    <!-- Like and Dislike -->
    <?php if(($this->withVotes) && ($row->user->idUser !== $this->idUser)){ ?>
        <a href="" class="voteTruth" style="background-image: url(/TruthOrDare/images/iLike.png);" id="VT<?php echo $row['idTruth']; ?>" name="up">&nbsp;</a>
        <a href="" class="voteTruth" style="background-image: url(/TruthOrDare/images/iDislike.png);" id="VT<?php echo $row['idTruth']; ?>" name="down">&nbsp;</a>
    <?php } ?>
    <span id="nbVoteT<?php echo $row['idTruth']; ?>"><?php echo $row['voteUp'] - $row['voteDown']; ?></span>

    
    <!-- Favourite -->
    <?php if($this->withFavourites){?>
        <div class='addFavouriteTruth' 
             <?php if($row->nbFavourite > 0){echo " style='background-image: url(/TruthOrDare/images/favouriteChosen.png);' ";} ?> 
             id='FT<?php echo $row->idTruth; ?>'>&nbsp;
        </div>
    <?php } ?>
    
    <!-- Challenge -->
    <?php if($this->withSendChallenge){ ?>
        &nbsp;&nbsp;<span class='challengeTruth' id='CT<?php echo $row->idTruth; ?>'><a>Challenge</a></span>
    <?php } ?>
    
    <!-- Comments -->
    <?php if($this->withComments){ ?>
        &nbsp;&nbsp;<a href="index.php?r=site/comment&idTruth=<?php echo $row['idTruth']; ?>">See the <?php echo $row->nbComment; ?> comments</a>
    <?php } ?>
        
    <!-- Author informations and Date -->
    <span style="float: right;">
        <?php echo Yii::app()->dateFormatter->format('yyyy-MM-dd',$row['dateSubmit']); ?>
    </span>
    <?php if($this->withAuthorInformations){ ?>
        <span style="float: right;">
            <?php echo $row->anonymous == 1 ? 'Anonymous' : "<a href='index.php?r=user/userPage&idUser=" . $row->user->idUser . "'>" . $row->user->username . "</a>"; ?>&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo MyFunctions::getTruthRankName($row->user->scoreTruth->score); ?>&nbsp;&nbsp;-&nbsp;&nbsp;
            <?php echo MyFunctions::getDareRankName($row->user->scoreDare->score); ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        </span>
    <?php } ?>
    
    <!-- Truth -->
    <div class="boxTruthOrDare">
        <?php echo $row['truth']; ?>
     </div> 
    
<?php } ?>
<br />


<!--************-->
<!-- Link Pager -->
<!--************-->
<div style="text-align: center;">
    <?php $this->widget('CLinkPager', array(
        'pages' => $pages,
        'maxButtonCount'=>10,
        'header'=>"",
    )) ?>
</div>


<!--**************************************************-->
<!-- Dialog box to choose which list to add favourite -->
<!--**************************************************-->
<?php if($this->withFavourites){ ?>
    <div id="dialog-form-favourite-truth" style="font-size:0.8em;" title="Choose your list">
        <?php 
            echo CHtml::dropDownList('FavouriteTruth_UserLists',null, $userLists, array('prompt'=>'Select List','style'=>'width:330px;','id'=>'FavouriteTruth_idUserList'));
            if($userLists == null)
                {echo "<br /><br /><p style='color:red;'>You haven't created any list yet, please click <a href='index.php?r=user/favourite'>here</a></p>";} 
        ?>
    </div>
<?php } ?>


<!--******************************-->
<!-- Dialog box to send Challenge -->
<!--******************************-->
<?php if($this->withSendChallenge){ ?>
    <div id="dialog-form-challenge-truth" style="font-size:0.8em;" title="Send Challenge">
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

    <div id="dialog-form-challenge-truth-sent">
        <p>Challenge sent!</p>
    </div>

    <div id="dialog-form-challenge-truth-alreadyexists">
        <p>This user already played this challenge or has it in his/her waiting list!</p>
    </div>
<?php } ?>



