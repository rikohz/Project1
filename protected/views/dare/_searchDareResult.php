<!--***************-->
<!-- List of Dares -->
<!--***************-->
<?php $this->widget('DareList',
        array(
            'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
            'filterLevel'=>Yii::app()->user->getLevel(),
            'withVotes'=>1,
            'withFavourites'=>!Yii::app()->user->isGuest,
            'withComments'=>1,
            'withSendChallenge'=>!Yii::app()->user->isGuest,
            'idFormCriteria'=>"dare-search-form",
            'idDivUpdate'=>'divSearchResult',
            'model'=>$model
            ));
?>