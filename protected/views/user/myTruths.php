<script type="text/javascript">
function selectCategory(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=truth/myTruths&idCategory=" + value;
}
</script>
 

<?php
$this->pageTitle=Yii::app()->name . ' - My Truths';
$this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
	'My Truths',
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
            'idUser'=>Yii::app()->user->getId(),
            'idUserFilter'=>Yii::app()->user->getId(),
            'filterLevel'=>Yii::app()->user->getLevel(),
            'idCategory'=>$idCategory,
            'order'=>$order,
            'withVotes'=>1,
            'withFavourites'=>1,
            'withComments'=>1,
            'withAuthorInformations'=>0
            )); ?>


