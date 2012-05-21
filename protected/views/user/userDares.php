<script type="text/javascript">
function selectCategory(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/userDares&idUser=<?php echo $idUser; ?>&idCategory=" + value;
}
</script>
 

<?php
$this->pageTitle=Yii::app()->name . ' - User Dares';
$this->breadcrumbs=array(
        'User Page'=>array("user/userPage&idUser=$idUser"),
	'User Dares',
);
?>


<!--******************-->
<!-- Order and Filter -->
<!--******************-->
<div style="margin-bottom:40px;">
    <span style="float:left;">Order by:</span>
    <span style="float:left;margin-left:20px;"><a href="<?php echo Yii::app()->request->getUrl(); ?>&idUser=<?php echo $idUser; ?>&order=t.dateSubmit">Submit Date</a></span>
    <span style="float:left;margin-left:20px;"><a href="<?php echo Yii::app()->request->getUrl(); ?>&idUser=<?php echo $idUser; ?>&order=t.voteUp-t.voteDown">Popularity</a></span>
    <span style="float:left;margin-left:20px;"><a href="<?php echo Yii::app()->request->getUrl(); ?>&idUser=<?php echo $idUser; ?>&order=nbComment">Nb of Comments</a></span>
    <span style="float:right;"><?php echo CHtml::dropDownList('category',$idCategory,$categories, array('empty' => 'All','id'=>'category','onChange'=>'selectCategory(this)')); ?></span>
</div>


<!--***************-->
<!-- List of Dares -->
<!--***************-->
<?php $this->widget('DareList',
        array(
            'idUser'=>Yii::app()->user->getId(),
            'idUserFilter'=>$idUser,
            'filterLevel'=>Yii::app()->user->getLevel(),
            'idCategory'=>$idCategory,
            'order'=>$order,
            'withVotes'=>1,
            'withFavourites'=>1,
            'withComments'=>1,
            'withAuthorInformations'=>0,
            'withoutAnonymous'=>1
            )); ?>

