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
    $( "#dialog-form-dare" ).dialog({
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
            $( "#dialog-form-dare" ).dialog( "open" );
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
<!-- Dare List -->
<!--************-->
<input type="hidden" value="<?php echo Yii::app()->user->isGuest; ?>" id="isGuestDare" />
<?php foreach ($datas as $row) { ?>
    
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
    
    <!-- Comments -->
    <?php if($this->withComments){ ?>
        &nbsp;&nbsp;<a href="index.php?r=site/comment&idDare=<?php echo $row['idDare']; ?>">See the <?php echo $row->nbComment; ?> comments</a>
    <?php } ?>
        
    <!-- Author informations and Date -->
    <span style="float: right;">
        <?php echo $row->anonymous == 1 ? 'Anonymous' : "<a href='index.php?r=user/userPage&idUser=" . $row->user->idUser . "'>" . $row->user->username . "</a>"; ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo MyFunctions::getTruthRankName($row->user->scoreTruth->score); ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo MyFunctions::getDareRankName($row->user->scoreDare->score); ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo $row->category->category; ?>&nbsp;&nbsp;-&nbsp;&nbsp;
        <?php echo Yii::app()->dateFormatter->format('yyyy-MM-dd',$row['dateSubmit']); ?>
    </span>
    
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
    <div id="dialog-form-dare" style="font-size:0.8em;" title="Choose your list">
        <?php 
            $form=$this->beginWidget('CActiveForm', array('id'=>'addFavourite-form'));
            echo $form->dropDownList($modelUserList,'name', $userLists, array('prompt'=>'Select List','style'=>'width:330px;','id'=>'UserListDare_name'));
            if($userLists == null)
                {echo "<br /><br /><p style='color:red;'>You haven't created any list yet, please click <a href='index.php?r=user/favourite'>here</a></p>";} 
            $this->endWidget(); 
        ?>
    </div>
<?php } ?>



