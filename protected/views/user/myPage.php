<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . ' - My Page';
  $this->breadcrumbs=array(
	'My Page',
  );
?>
<div style="width:150px; display:inline-block;border:1px black solid; vertical-align: top;">
    <img style="vertical-align:middle;" src="userImages/profilePicture/<?php echo $user->profilePicture . '_profile' . $user->profilePictureExtension; ?>" />

    <div style="margin-bottom:15px; margin-top:10px;">
        <a style="display:block;" href="index.php?r=user/myFriends">My Friends</a>      
        <a style="display:block;" href="index.php?r=user/myMessages">My Messages</a>
        <a style="display:block;" href="index.php?r=user/myChallenges">My Challenges</a>
        <a style="display:block;" href="index.php?r=user/myAlbums">My Albums</a>
        <a style="display:block;" href="index.php?r=user/myTruths">My Truths</a>
        <a style="display:block;" href="index.php?r=user/myDares">My Dares</a>
        <a style="display:block;" href="index.php?r=user/myLists">My Lists</a>
        <a style="display:block;" href="index.php?r=user/mySettings">My Settings</a>
    </div>
    My Friends
    <?php $this->widget('FriendsOverview',array('idUser'=>$user->idUser)); ?>
</div>
<div style="border:1px black solid; width:750px; display:inline-block;">
    <div style="height:150px; border:1px black solid;">
        <div style="height:150px; width:250px; border:1px black solid; float:left;">
            <h1><?php echo Yii::app()->user->getName(); ?></h1>
            <p>
                <?php 
                    echo isset($user->province)? $user->province->name : "";
                    echo isset($user->city)? " - " . $user->city->name : "";
                    echo isset($user->district)? " - " . $user->district->name : "" ; 
                ?>
            </p>
            <p><?php $myFunctiuns = new MyFunctiuns(); echo isset($user->birthDate) ? $myFunctiuns->getAge($user->birthDate) : ''; ?></p>
        </div>
        <div style="height:150px; width:494px; border:1px black solid; float:right;">
            <div style="float:right; width:450px; height:73px; border:1px black solid;">
                <div style="float:right; width:120px; height:71px; border:1px black solid; font-weight:bold; text-align:center;">
                    <p style="line-height:71px; vertical-align:middle;">Total: <?php echo $scoreTruth['scoreTotal']; ?>pts</p>
                </div>
                <div style="float:right; width:120px; height:71px; border:1px black solid;">
                    <div style="margin: 10px 0 0 8px;">
                        <u>Week</u><br />
                        Idea: <?php echo $scoreTruth['scoreWeek']; ?>pts<br />
                        Challenge: ??pts
                    </div>
                </div>
                <div style="float:right; width:120px; height:71px; border:1px black solid; font-size:4.5em; vertical-align:middle; text-align:center; font-weight:bold;">T</div>
            </div>
            <div style="float:right; width:450px; height:73px; border:1px black solid;">
                <div style="float:right; width:120px; height:71px; border:1px black solid; font-weight:bold; text-align:center;">
                    <p style="line-height:71px; vertical-align:middle;">Total: <?php echo $scoreDare['scoreTotal']; ?>pts</p>
                </div>
                <div style="float:right; width:120px; height:71px; border:1px black solid;">
                    <div style="margin: 10px 0 0 8px;">
                        <u>Week</u><br />
                        Idea: <?php echo $scoreDare['scoreWeek']; ?>pts<br />
                        Challenge: ??pts
                    </div>
                </div>
                <div style="float:right; width:120px; height:71px; border:1px black solid; font-size:4.5em; vertical-align:middle; text-align:center; font-weight:bold;">D</div>
            </div>
        </div>
    </div>
    <div style="border:1px black solid;">
        <div class="form">
            <?php $form = $this->beginWidget('CActiveForm', array('id'=>'wall-form')); ?>
                <div class="row"><?php echo $form->textArea($model,'content'); ?></div>
                <div class="row buttons"><?php echo CHtml::submitButton('Submit'); ?></div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
    <div style="border:1px black solid;">
        <?php foreach($wall as $row): ?>
            <img src="userImages/profilePicture_mini/<?php echo $row['picture'] . '_mini' . $row['pictureExtension']; ?>" />
            <p><?php echo $row['createDate']; ?></p>
            <p><?php echo $row['content']; ?></p>
        <?php endforeach; ?>
            
        <p>ARGH ORDE DATE A REVOIRs</p>
        <p>See yiinfinite-scroll</p>
    </div>
</div>