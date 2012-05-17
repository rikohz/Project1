<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - My Settings';
  $this->breadcrumbs=array(
        'My Page'=>array('user/myPage'),
	'My Settings',
  );
?>

<div>
    <a href="index.php?r=user/myInormations">Update my informations</a>
    <br />
    <a href="index.php?r=user/myPassword">Change my Password</a>
    <br />
    <a href="index.php?r=user/myCoins">Update my Coins</a>
    <br />
    <a href="index.php?r=user/myProfilePicture">Update my Profile Picture</a>
</div>
   