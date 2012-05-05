<script type="text/javascript">
function selectCategory(dropDownList)
{
    window.location = "index.php?r=truth/truth&idCategory=" + dropDownList.selectedIndex;
}
</script>
 

<?php
$this->pageTitle=Yii::app()->name . ' - Truths';
$this->breadcrumbs=array(
	'Truths',
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
    <span style="float:right;"><?php echo CHtml::dropDownList('category',$idCategory,$categories, array('empty' => 'All','id'=>'category','onChange'=>'selectCategory(this)')); ?></span>
</div>


<!--****************-->
<!-- List of Truths -->
<!--****************-->
<?php $this->widget('TruthList',
        array(
            'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
            'filterLevel'=>Yii::app()->user->isGuest ? 1 : Yii::app()->user->getLevel(),
            'idCategory'=>$idCategory,
            'order'=>$order,
            'withVotes'=>1,
            'withFavourites'=>!Yii::app()->user->isGuest,
            'withComments'=>1
            )); ?>


