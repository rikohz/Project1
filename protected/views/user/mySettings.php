<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - My Settings';
  $this->breadcrumbs=array(
	'My Settings',
  );
?>

<div>
    <a href="index.php?r=user/updateUser">Update my informations</a>
    <br />
    <a href="index.php?r=user/changePassword">Change my Password</a>
    <br />
    <a href="index.php?r=user/updateCoins">Update my Coins</a>
    <br />
    <a href="index.php?r=user/updateProfilePicture">Update my Profile Picture</a>
    <br />
    <a href="index.php?r=user/favourite">My Favorites</a>
</div>
   