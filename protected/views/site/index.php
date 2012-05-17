<?php $this->pageTitle=Yii::app()->name; ?>

<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>
<br />

<!-- GENERAL RANKING -->
<div style="width:150px; display:inline-block;">
    <p style="text-align:center;">GENERAL</p>
    <table width="100%">
    <?php foreach ($generalRanking as $row) { ?>
        <tr>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['score']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>

<!-- TRUTH RANKING -->
<div style="width:150px; display:inline-block; margin-left:100px; margin-right:100px;">
    <p style="text-align:center;">TRUTH</p>
    <table width="100%">
    <?php 
        foreach ($truthRanking as $row) { ?>
        <tr>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['scoreTruth']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>

<!-- DARE RANKIN -->
<div style="width:150px; display:inline-block;">
    <p style="text-align:center;">DARE</p>
    <table width="100%">
    <?php 
        foreach ($dareRanking as $row) { ?>
        <tr>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['scoreDare']; ?></td>
        </tr>
    <?php } ?>
    </table>
</div>

<!-- LINK TO ACCESS FULL RANKING PAGE -->
<br /><br /><a href="index.php?r=site/ranking">See the full ranking</a><br /><br />

<!-- LIST 3 MOST RECENT DARES -->
<p>3 most recent DARES</p>
<?php $this->widget('DareList',
        array(
            'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
            'filterLevel'=>Yii::app()->user->isGuest ? 1 : Yii::app()->user->getLevel(),
            'order'=>'dateSubmit',
            'withVotes'=>1,
            'withFavourites'=>!Yii::app()->user->isGuest,
            'withComments'=>1,
            'limit'=>3,
            'withSendChallenge'=>!Yii::app()->user->isGuest
            )); ?>

<!-- LIST 3 MOST RECENT TRUTHS -->
<p>3 most recent TRUTHS</p>
<?php $this->widget('TruthList',
        array(
            'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
            'filterLevel'=>Yii::app()->user->isGuest ? 1 : Yii::app()->user->getLevel(),
            'order'=>'dateSubmit',
            'withVotes'=>1,
            'withFavourites'=>!Yii::app()->user->isGuest,
            'withComments'=>1,
            'limit'=>3,
            'withSendChallenge'=>!Yii::app()->user->isGuest
            )); ?>

