<script type="text/javascript">
function selectCategory(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myFriends&idCategory=" + value;
}
function selectGender(dropDownList)
{
    var value = dropDownList.options[dropDownList.selectedIndex].value;
    window.location = "index.php?r=user/myFriends&idGender=" + value;
}
$(function() {

    //ACCEPT Friend Request
    $(".acceptFriendRequest").click(function() 
    { 
        var parent = $(this).parent();
        $.ajax({
            type: "POST",
            url: "index.php?r=user/acceptFriendRequest",
            data: { idUser: $(this).attr("id") },
            cache: false,

            success: function(html)
            {
                parent.html(html);
            } 
        }); 
        return false;
    });

    //DECLINE Friend Request
    $(".declineFriendRequest").click(function() 
    { 
        var parent = $(this).parent();
        $.ajax({
            type: "POST",
            url: "index.php?r=user/declineFriendRequest",
            data: { idUser: $(this).attr("id") },
            cache: false,

            success: function(html)
            {
                parent.html(html);
            } 
        }); 
        return false;
    });
});
</script>
<?php
$this->pageTitle=Yii::app()->name . ' - My Friends';
$this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
	'My Friends',
);
?>

<!--******************-->
<!-- Order and Filter -->
<!--******************-->
<span style="float:right;"><?php echo CHtml::dropDownList('category',$idCategory,$categories, array('empty' => 'All','onChange'=>'selectCategory(this)')); ?></span>
<span style="float:right;"><?php echo CHtml::dropDownList('gender',$idGender,$genders, array('empty' => 'All','onChange'=>'selectGender(this)')); ?></span>



<!--**********************-->
<!-- Friends Request list -->
<!--**********************-->
<?php foreach($friendsRequest as $row): ?>
    <div style="display:inline-block; width:80px; height:100px;">
        <a href="index.php?r=user/userPage&idUser=<?php echo $row->userFrom->idUser; ?>">
            <div><img src="<?php echo Yii::app()->request->baseUrl; ?>/userImages/profilePicture_mini/<?php echo $row->userFrom->profilePicture . '_mini' . $row->userFrom->profilePictureExtension; ?>" /></div>
            <div><?php echo $row->userFrom->username; ?></div>
            <div style="text-align:center;">
                <a id="<?php echo $row->userFrom->idUser; ?>" class="acceptFriendRequest" href="index.php?r=user/acceptFriendRequest&idUser=<?php echo $row->userFrom->idUser; ?>">Accept</a>
                <a id="<?php echo $row->userFrom->idUser; ?>" class="declineFriendRequest" href="index.php?r=user/declineFriendRequest&idUser=<?php echo $row->userFrom->idUser; ?>">Refuse</a>
            </div>
        </a>
    </div>
<?php endforeach; ?>

<br />
<br />

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