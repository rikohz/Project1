<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">
$(function() {
    
    //<!--***********************************-->
    //<!-- PictureBox for Challenge Pictures -->
    //<!--***********************************-->
    $("a.challengePicture").fancybox();


    //<!--***************************-->
    //<!-- Vote Challenge Management -->
    //<!--***************************-->   
    $(".voteChallenge").click(function() 
    { 
        if($("#isGuestTruth").val() == 1)
            window.location = "index.php?r=user/login";
             
        var id = $(this).attr("id").substring(2, $(this).attr("id").length);
        var name = $(this).attr("name");
        var dataString = 'idChallenge='+ id + '&vote=' + name;    
        
        var me = $(this); 
        var background = $(this).css("background-image");
        
        me.css("background", "url(/TruthOrDare/images/loading.gif) no-repeat");
        me.css("background-size","100% 100%"); 

        var compteur = $('#nbVoteC' + id);

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

});
</script>
<?php
$this->pageTitle = Yii::app()->name . ' - Challenge';
$this->breadcrumbs = $idType == 'idTruth'? array('Truth'=>array('truth/truth'),'Challenge') : array('Dares'=>array('dare/dare'),'Challenge');
?>


<!--***************-->
<!-- Truth or Dare -->
<!--***************-->  

<?php 
    $this->widget(substr($idType,2,strlen($idType)).'List',
            array(
                'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
                'filterLevel'=>Yii::app()->user->getLevel(),
                'withVotes'=>1,
                'withFavourites'=>!Yii::app()->user->isGuest,
                'withComments'=>1,
                'model'=>$modelTruthOrDare
                )); 
?>

<br />
<br />


    
<!--***********-->
<!-- Challenge -->
<!--***********-->  

<table style="border-spacing:5px;">
    <?php foreach ($challenges as $row) : ?>
        <tr style="background-color: buttonface;">
            <?php if($row->dare === null): ?>
                <td width="55%" style="height:40px;" title="<?php echo $row->answer; ?>">
                    <span id="<?php echo 'TR' . $row->truth->idTruth; ?>" class='answerTruth'><?php echo $row->answer; ?></span>
                </td>
            <?php else: ?>
                <td width="1%" style="padding:0">
                    <a id="<?php echo $row->idChallenge; ?>" class="challengePicture" title="<?php echo "Dare #" . $row->dare->idDare . " (" . $row->dare->category->category . "): " . $row->dare->dare ?>" href="userImages/challenge_original/<?php echo $row->pictureName . '_original' . $row->pictureExtension; ?>"><img src="userImages/challenge_mini/<?php echo $row->pictureName . '_mini' . $row->pictureExtension; ?>" width="48px" height="48px" style="margin:0" /></a>
                </td>
                <td width="45%" title="<?php echo $row->answer; ?>">
                    <span id="<?php echo 'DA' . $row->dare->idDare; ?>" class='commentDare' style='display:block;overflow:hidden;'><?php echo $row->answer; ?></span>
                </td>
            <?php endif; ?>
            <td width="10%"><?php echo $row->userTo->username; ?></td>
            <td width="10%">Rank<?php //echo MyFunctions::getTruthRankName($row->userTo->scoreTruth->score) . ' - ' . MyFunctions::getDareRankName($row->userTo->scoreDare->score); ?></td>
            <td width="15%">
                <a href="" class="voteChallenge" style="background-image: url(/TruthOrDare/images/iLike.png); text-decoration:none;" id="VC<?php echo $row->idChallenge; ?>" name="up">&nbsp;</a>
                <a href="" class="voteChallenge" style="background-image: url(/TruthOrDare/images/iDislike.png); text-decoration:none;" id="VC<?php echo $row->idChallenge; ?>" name="down">&nbsp;</a>
                <span id="nbVoteC<?php echo $row->idChallenge; ?>" style="position:relative; top:4px;"><?php echo ($row->voteUp - $row->voteDown) >= 0 ? '+ ' . ($row->voteUp - $row->voteDown) : '- ' . ($row->voteUp - $row->voteDown); ?></span>
            </td>
            <td width="10%"><?php echo Yii::app()->dateFormatter->format('yyyy-MM-dd',$row->finishDate); ?></td>    
        </tr>
    <?php endforeach; ?>  
</table>
