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
                if(html=='no')
                    compteur.innerHTML = "You have already voted for this one!";
                else
                    compteur.innerHTML = html;
                parent.html(tampon);
            } 
        }); 
        return false;
    });

    //Favourite Management
    var idTruth = null;
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#dialog-form-truth" ).dialog({
            autoOpen: false,
            height: 210,
            width: 350,
            modal: true,
            buttons: {
                    "Validate": function() {
                            var idUserList = $( "#UserListTruth_name" ).val();
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
        if($(this).attr("tag") !== 'Chosen')
        {
            idTruth = $(this).attr("id").substring(2, $(this).attr("id").length);
            $( "#dialog-form-truth" ).dialog( "open" );
        }    
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
<?php foreach ($datas as $row) { ?>
    
    <!-- Like and Dislike -->
    <input type="hidden" value="<?php echo Yii::app()->user->isGuest; ?>" id="isGuestTruth" />
    <?php if($this->withVotes){ ?>
        <a href="" class="voteTruth" style="background-image: url(/TruthOrDare/images/iLike.png);" id="VT<?php echo $row['idTruth']; ?>" name="up">&nbsp;</a>
        <a href="" class="voteTruth" style="background-image: url(/TruthOrDare/images/iDislike.png);" id="VT<?php echo $row['idTruth']; ?>" name="down">&nbsp;</a>
        <span id="nbVoteT<?php echo $row['idTruth']; ?>"><?php echo $row['voteUp'] - $row['voteDown']; ?></span>
    <?php } ?>

    
    <!-- Favourite -->
    <?php if($this->withFavourites){?>
        <div tag='<?php echo $row->nbFavourite > 0 ? 'Chosen' : ''; ?>' 
             class='addFavouriteTruth' 
             <?php if($row->nbFavourite > 0){echo " style='background-image: url(/TruthOrDare/images/favouriteChosen.png);' ";} ?> 
             id='FT<?php echo $row->idTruth; ?>'>&nbsp;
        </div>
    <?php } ?>
    
    <!-- Comments -->
    <?php if($this->withComments){ ?>
        &nbsp;&nbsp;<a href="index.php?r=site/comment&idTruth=<?php echo $row['idTruth']; ?>">See the <?php echo $row->nbComment; ?> comments</a>
    <?php } ?>
        
    <!-- Author informations and Date -->
    <span style="float: right; margin-right:20px;">
        <?php echo $row->anonymous == 1 ? 'Anonymous' : $row->user->username; ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo Yii::app()->user->getTruthRankName($row->user->scoreTruth === null? 0 : $row->user->scoreTruth->score); ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo Yii::app()->user->getDareRankName($row->user->scoreDare === null? 0 : $row->user->scoreDare->score); ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo $row->categories->category; ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo Yii::app()->dateFormatter->format('yyyy-MM-dd',$row['dateSubmit']); ?>
    </span>
    
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
    <div id="dialog-form-truth" style="font-size:0.8em;" title="Choose your list">
        <?php 
            $form=$this->beginWidget('CActiveForm', array('id'=>'addFavourite-form'));
            echo $form->dropDownList($modelUserList,'name', $userLists, array('prompt'=>'Select List','style'=>'width:330px;','id'=>'UserListTruth_name'));
            if($userLists == null)
                {echo "<br /><br /><p style='color:red;'>You haven't created any list yet, please click <a href='index.php?r=user/favourite'>here</a></p>";} 
            $this->endWidget(); 
        ?>
    </div>
<?php } ?>



