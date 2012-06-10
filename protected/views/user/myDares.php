<script type="text/javascript">
function selectCategory(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myDares&idCategory=" + value;
}
</script>
 

<?php
$this->pageTitle=Yii::app()->name . ' - My Dares';
$this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
	'My Dares',
);
?>


<!--******************-->
<!-- Order and Filter -->
<!--******************-->
<div style="margin-bottom:40px;">
    <span style="float:left;">Order by:</span>
    <span style="float:left;margin-left:20px;"><a href="<?php echo Yii::app()->request->getUrl(); ?>&order=t.dateSubmit">Submit Date</a></span>
    <span style="float:left;margin-left:20px;"><a href="<?php echo Yii::app()->request->getUrl(); ?>&order=t.voteUp-t.voteDown">Popularity</a></span>
    <span style="float:left;margin-left:20px;"><a href="<?php echo Yii::app()->request->getUrl(); ?>&order=nbComment">Nb of Comments</a></span>
    <span style="float:right;"><?php echo CHtml::dropDownList('category',$model->idCategory,$categories, array('empty' => 'All','id'=>'category','onChange'=>'selectCategory(this)')); ?></span>
</div>


<!--***************-->
<!-- List of Dares -->
<!--***************-->
<?php $this->widget('DareList',
        array(
            'idUser'=>Yii::app()->user->getId(),
            'filterLevel'=>Yii::app()->user->getLevel(),
            'model'=>$model,
            'withVotes'=>1,
            'withFavourites'=>1,
            'withComments'=>1
            )); ?>

