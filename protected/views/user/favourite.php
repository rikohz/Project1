<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - Favorites';
  $this->breadcrumbs=array(
	'Favorites',
  );
  
//Flash add favourite list
if(Yii::app()->user->hasFlash('addFavouriteList')): ?>
<div class="flash-success">
	<?php echo Yii::app()->user->getFlash('addFavouriteList'); ?>
</div>
<?php else: ?>

<?php foreach ($model as $row){ ?>
    <a href="index.php?favouriteList"
    echo $row->name . '<br />';
} ?>

<?php endif; ?>
