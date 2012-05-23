<script type="text/javascript" src="/TruthOrDare/script/ajaxupload.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/script/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
<script type="text/javascript">
function selectCategory(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/userChallenges&idUser=<?php echo $idUser; ?>&idCategory=" + value;
}
function selectGender(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/userChallenges&idUser=<?php echo $idUser; ?>&idGender=" + value;
}
function selectTypeChallenge(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/userChallenges&idUser=<?php echo $idUser; ?>&idTypeChallenge=" + value;
}
function selectStatusChallenge(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/userChallenges&idUser=<?php echo $idUser; ?>&idStatusChallenge=" + value;
}
function selectMinDateChallenge(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/userChallenges&idUser=<?php echo $idUser; ?>&minDateChallenge=" + value;
}
function selectUserFrom(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/userChallenges&idUser=<?php echo $idUser; ?>&idUserFrom=" + value;
}
$(function() {
    
    //<!--***********************************-->
    //<!-- PictureBox for Challenge Pictures -->
    //<!--***********************************-->
    $("a.challengePicture").fancybox();

    //<!--********************************-->
    //<!-- Dialog box to see answer Truth -->
    //<!--********************************-->
    $(".answerTruth").click(function() {
        answerTruth = $(this).html();
        $( "#answerTruth" ).html(answerTruth);
        id = $(this).attr("id").substring(2, $(this).attr("id").length);
        type = $(this).attr("id").substring(0,2) == 'TR' ? 'Truth' : 'Dare';
        $("#dialog-form-see-answerTruth").dialog('option', 'title', 'Challenge ' + type + ' #'+id); 
        $( "#dialog-form-see-answerTruth" ).dialog( "open" );
    }); 
    
    $( "#dialog-form-see-answerTruth" ).dialog({
            autoOpen: false
    });

    //<!--*********************************-->
    //<!-- Dialog box to see Truth or Dare -->
    //<!--*********************************-->
    $(".truthOrDare").click(function() {
        truthOrDare = $(this).html();
        $( "#truthOrDare" ).html(truthOrDare);
        id = $(this).attr("id").substring(2, $(this).attr("id").length);
        type = $(this).attr("id").substring(0,2) == 'TR' ? 'Truth' : 'Dare';
        $("#dialog-form-see-truthOrDare").dialog('option', 'title', 'Challenge ' + type + ' #'+id); 
        $("#dialog-form-see-truthOrDare").dialog( "open" );
    }); 
    
    $( "#dialog-form-see-truthOrDare" ).dialog({
            autoOpen: false
    });
});
</script>
<style type="text/css">  
    div.preview {margin-left: auto; margin-right: auto; height: 150px; width: 150px;  border: 2px dotted #CCCCCC;}
    div.preview.loading {background: url(/TruthOrDare/images/loading.gif) no-repeat 63px 63px;}
    div.preview.loading img {display: none;}
    input { z-index: 10000 }
</style>

<?php
$this->pageTitle=Yii::app()->name . ' - My Challenges';
$this->breadcrumbs=array(
        User::getUsernameFromId($idUser) . " Page"=>array("user/userPage&idUser=$idUser"),
	'My Challenges',
);
?>

<!--******************-->
<!-- Order and Filter -->
<!--******************-->
<div style="margin-top:30px;">
    <span style="float:right; margin-left:18px;">Category: <?php echo CHtml::dropDownList('category',$idCategory,$categories, array('empty' => 'All','onChange'=>'selectCategory(this)')); ?></span>
    <span style="float:right; margin-left:18px;">Gender: <?php echo CHtml::dropDownList('gender',$idGender,$genders, array('empty' => 'All','onChange'=>'selectGender(this)')); ?></span>
    <span style="float:right; margin-left:18px;">Type: <?php echo CHtml::dropDownList('typeChallenge',$idTypeChallenge,$typeChallenges, array('empty' => 'All','onChange'=>'selectTypeChallenge(this)')); ?></span>
    <span style="float:right; margin-left:18px;">Status: <?php echo CHtml::dropDownList('statusChallenge',$idStatusChallenge,$statusChallenges, array('empty' => 'All','onChange'=>'selectStatusChallenge(this)')); ?></span>
    <span style="float:right; margin-left:18px;">Period: <?php echo CHtml::dropDownList('minDateChallenge',$minDateChallenge,$period, array('empty' => 'All','onChange'=>'selectMinDateChallenge(this)')); ?></span>
    <span style="float:right; margin-left:18px;">From: <?php echo CHtml::dropDownList('userFrom',$idUserFrom,$userFrom, array('empty' => 'All','onChange'=>'selectUserFrom(this)')); ?></span>
</div>
<br />


<!--************-->
<!-- Challenges -->
<!--************-->
<table width="100%" style="border-spacing:5px;">
    <tr>
        <td width="8%" style="font-weight:bold; font-size:1.2em;">&nbsp;</td>
        <td width="3%" style="font-weight:bold; font-size:1.2em;">&nbsp;</td>
        <td width="3%" style="font-weight:bold; font-size:1.2em;">&nbsp;</td>
        <td width="5%" style="font-weight:bold; font-size:1.2em;">&nbsp;</td>
        <td width="10%" style="font-weight:bold; font-size:1.2em;">&nbsp;</td>
        <td width="30%" style="font-weight:bold; font-size:1.2em;">&nbsp;</td>
        <td width="41%" style="font-weight:bold; font-size:1.2em;">&nbsp;</td>
    </tr>
    <?php foreach($challenges as $row): ?>
        <tr style="background-color: <?php echo $row->status == 1 ? '#c1fb9e' : '#ffd4d5'; ?>">
            <td style="height:60px;"><?php echo Yii::app()->dateFormatter->format('yyyy-MM-dd',$row->createDate); ?></td>
            <td><?php echo $row->truth === null ? "D" : "T"; ?></td>
            <td><?php echo $row->truth === null ? $row->dare->category->category : $row->truth->category->category; ?></td>
            <td><?php echo ($row->voteUp - $row->voteDown) >= 0 ? '+ ' . ($row->voteUp - $row->voteDown) : '- ' . ($row->voteUp - $row->voteDown); ?></td>
            <td><a href="index.php?r=user/userPage&idUser=<?php echo $row->idUserFrom; ?>"><?php echo $row->userFrom->username; ?></a></td>
            <td style="overflow:hidden;">
                <span id="<?php echo $row->truth === null ? 'DA' . $row->dare->idDare : 'TR' . $row->truth->idTruth; ?>" class='truthOrDare' style='cursor:pointer;display:block;height:50px;overflow:hidden;'>
                    <?php echo $row->truth === null ? $row->dare->dare : $row->truth->truth; ?>
                </span>
            </td>
            <td>
                <?php if($row->status == 0): ?>
                    <span>Waiting...</span>
                <?php else: ?>
                    <?php if($row->dare === null): ?>
                        <span id="<?php echo $row->truth === null ? 'DA' . $row->dare->idDare : 'TR' . $row->truth->idTruth; ?>" class='answerTruth' style='cursor:pointer;display:block;height:50px;overflow:hidden;'><?php echo $row->answer; ?></span>
                    <?php else: ?>
                        <a id="<?php echo $row->idChallenge; ?>" class="challengePicture" title="<?php echo "Dare #" . $row->dare->idDare . " (" . $row->dare->category->category . "): " . $row->dare->dare ?>" href="userImages/challenge_original/<?php echo $row->pictureName . '_original' . $row->pictureExtension; ?>"><img src="userImages/challenge_mini/<?php echo $row->pictureName . '_mini' . $row->pictureExtension; ?>" width="48px" height="48px" /></a>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>


<!--*************************************-->
<!-- Dialog box to see the Truth or Dare -->
<!--*************************************-->
<div id="dialog-form-see-truthOrDare" title="See answer">
    <div id="truthOrDare">&nbsp;</div>
</div>


<!--******************************************-->
<!-- Dialog box to see answer Challenge Truth -->
<!--******************************************-->
<div id="dialog-form-see-answerTruth" title="See answer">
    <div id="answerTruth">&nbsp;</div>
</div>

