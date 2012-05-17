<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript">
$(function() {

    //Vote Management
    $(".voteDare").click(function() 
    { 
        if($("#isGuestDare").val() == 1)
            window.location = "index.php?r=user/login";
             
        var id = $(this).attr("id").substring(2, $(this).attr("id").length);
        var name = $(this).attr("name");
        var dataString = 'idDare='+ id + '&vote=' + name;    
        var parent = $(this);

        var tampon = $(this).html();
        var compteur = document.getElementById('nbVoteD' + id);
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
    var idDare = null;
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#dialog-form-favourite-dare" ).dialog({
            autoOpen: false,
            height: 210,
            width: 350,
            modal: true,
            buttons: {
                    "Validate": function() {
                            var idUserList = $( "#UserListDare_name" ).val();
                            if ( idUserList !== '' ) {
                                    $.ajax({ 
                                      url: "index.php?r=site/addFavourite", 
                                      type: "POST", 
                                      data: 'idDare='+ idDare + '&idUserList=' + idUserList, 
                                      success: function(result){ 
                                          if(result == "SUCCESS")
                                            document.getElementById('FD' + idDare).style.backgroundImage = "url(/TruthOrDare/images/favouriteChosen.png)";  
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

    $( ".addFavouriteDare" ).click(function() {
        if($(this).attr("tag") !== 'Chosen')
        {
            idDare = $(this).attr("id").substring(2, $(this).attr("id").length);
            $( "#dialog-form-favourite-dare" ).dialog( "open" );
        }    
    });

    //Send Challenge
    $("#dialog-form-challenge-dare-sent").dialog({autoOpen: false});
    $("#dialog-form-challenge-dare-alreadyexists").dialog({autoOpen: false});
    $( "#dialog-form-challenge-dare" ).dialog({
            autoOpen: false,
            height: 300,
            width: 350,
            context: $(this), 
            modal: true,
            buttons: {
                    "Validate": function() {    
                        if ( $( "#ChallengeDare_idUser" ).val() !== '' ) {
                            $.ajax({ 
                              url: "index.php?r=user/sendChallenge", 
                              type: "POST", 
                              data: {
                                  'idUser':$( "#ChallengeDare_idUser" ).val(),
                                  'private':document.getElementById('ChallengeDare_private').checked,
                                  'idDare':idDare,
                                  'comment':$( "#ChallengeDare_comment" ).val()
                              }, 
                              success: function(result){ 
                                  if(result == "SUCCESS"){
                                    $("#dialog-form-challenge-dare" ).dialog( "close" );
                                    $("#ChallengeDare_idUser").val('');
                                    $("#ChallengeDare_private").checked = 0;
                                    $("#ChallengeDare_comment").val('');
                                    $("#dialog-form-challenge-dare-sent").dialog( "open" );
                                  }
                                  if(result == "ALREADY_EXISTS"){
                                    $("#dialog-form-challenge-dare-alreadyexists").dialog( "open" );
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

    $( ".challengeDare" ).click(function() {
        idDare = $(this).attr("id").substring(2, $(this).attr("id").length);
        $("#dialog-form-challenge-dare").dialog('option', 'title', 'Challenge Dare #'+idDare); 
        $("#dialog-form-challenge-dare").dialog( "open" );   
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
<!-- Dare List -->
<!--************-->
<input type="hidden" value="<?php echo Yii::app()->user->isGuest; ?>" id="isGuestDare" />
<?php foreach ($datas as $row) { ?>
    
    <!-- #Ref of Dare and Category -->
    <span>
        <?php echo '#' . $row['idDare']; ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo $row->category->category; ?>
    </span>
    
    <!-- Like and Dislike -->
    <?php if(($this->withVotes) && ($row->user->idUser !== $this->idUser)){ ?>
        <a href="" class="voteDare" style="background-image: url(/TruthOrDare/images/iLike.png);" id="VD<?php echo $row['idDare']; ?>" name="up">&nbsp;</a>
        <a href="" class="voteDare" style="background-image: url(/TruthOrDare/images/iDislike.png);" id="VD<?php echo $row['idDare']; ?>" name="down">&nbsp;</a>
    <?php } ?>
    <span id="nbVoteD<?php echo $row['idDare']; ?>"><?php echo $row['voteUp'] - $row['voteDown']; ?></span>

    
    <!-- Favourite -->
    <?php if($this->withFavourites){?>
        <div tag='<?php echo $row->nbFavourite > 0 ? 'Chosen' : ''; ?>' 
             class='addFavouriteDare' 
             <?php if($row->nbFavourite > 0){echo " style='background-image: url(/TruthOrDare/images/favouriteChosen.png);' ";} ?> 
             id='FD<?php echo $row->idDare; ?>'>&nbsp;
        </div>
    <?php } ?>
    
    <!-- Challenge -->
    <?php if($this->withSendChallenge){ ?>
        &nbsp;&nbsp;<span class='challengeDare' id='CD<?php echo $row->idDare; ?>'><a>Challenge</a></span>
    <?php } ?>
    
    <!-- Comments -->
    <?php if($this->withComments){ ?>
        &nbsp;&nbsp;<a href="index.php?r=site/comment&idDare=<?php echo $row['idDare']; ?>">See the <?php echo $row->nbComment; ?> comments</a>
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
        
    <!-- Dare -->
    <div class="boxTruthOrDare">
        <?php echo $row->dare; ?>
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
    <div id="dialog-form-favourite-dare" style="font-size:0.8em;" title="Choose your list">
        <?php 
            echo CHtml::dropDownList('FavouriteDare_UserLists',null, $userLists, array('prompt'=>'Select List','style'=>'width:330px;','id'=>'FavouriteDare_idUserList'));
            if($userLists == null)
                {echo "<br /><br /><p style='color:red;'>You haven't created any list yet, please click <a href='index.php?r=user/favourite'>here</a></p>";} 
        ?>
    </div>
<?php } ?>


<!--******************************-->
<!-- Dialog box to send Challenge -->
<!--******************************-->
<?php if($this->withSendChallenge){ ?>
    <div id="dialog-form-challenge-dare" style="font-size:0.8em;" title="Send Challenge">
        <?php echo CHtml::dropDownList('FriendDare_username',null, $friends, array('prompt'=>'Select Friend','style'=>'width:330px;','id'=>'ChallengeDare_idUser')); ?>
        <br />
        Comment:
        <textarea rows="4" cols="50" id="ChallengeDare_comment"></textarea>
        <br />Private: 
        <?php echo CHtml::checkBox('FriendDare_private',false, array('id'=>'ChallengeDare_private')); ?>
        <?php if($friends == null): ?>
            <br /><br /><p style='color:red;'>You haven't any friend yet!</p> 
        <?php endif; ?>
    </div>

    <div id="dialog-form-challenge-dare-sent">
        <p>Challenge sent!</p>
    </div>

    <div id="dialog-form-challenge-dare-alreadyexists">
        <p>This user already played this challenge or has it in his/her waiting list!</p>
    </div>
<?php } ?>



