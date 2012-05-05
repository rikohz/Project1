<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - Favorites';
  $this->breadcrumbs=array(
	'Favourites',
  );
?>
<p>My Favourites</p>
