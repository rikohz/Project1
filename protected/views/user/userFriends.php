<script type="text/javascript">
function selectCategory(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/userFriends&idCategory=" + value;
}
function selectGender(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/userFriends&idGender=" + value;
}
</script>
<?php
$this->pageTitle=Yii::app()->name . ' - User Friends';
$this->breadcrumbs=array(
        User::getUsernameFromId($idUser) . " Page"=>array("user/userPage&idUser=$idUser"),
	'User Friends',
);
?>

<!--******************-->
<!-- Order and Filter -->
<!--******************-->
<span style="float:right;"><?php echo CHtml::dropDownList('category',$idCategory,$categories, array('empty' => 'All','onChange'=>'selectCategory(this)')); ?></span>
<span style="float:right;"><?php echo CHtml::dropDownList('gender',$idGender,$genders, array('empty' => 'All','onChange'=>'selectGender(this)')); ?></span>



<!--**************-->
<!-- Friends list -->
<!--**************-->
<?php foreach($friends as $row): ?>
    <div style="display:inline-block; width:80px; height:100px;">
        <a href="index.php?r=user/userPage&idUser=<?php echo $row['idUser']; ?>">
            <div><img src="<?php echo Yii::app()->request->baseUrl; ?>/userImages/profilePicture_mini/<?php echo $row['profilePicture'] . '_mini' . $row['profilePictureExtension']; ?>" /></div>
            <div><?php echo $row['username']; ?></div>
        </a>
    </div>
<?php endforeach; ?>