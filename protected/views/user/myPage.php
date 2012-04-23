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
    My Friendss
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
                    <p style="line-height:71px; vertical-align:middle;">Total: ???pts</p>
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
                    <p style="line-height:71px; vertical-align:middle;">Total: ???pts</p>
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
        <p>
            <u>Total</u><br />
            Week score: <?php echo $scoreTruth['scoreWeek'] + $scoreDare['scoreWeek']; ?> pts<br />
            Month score: <?php echo $scoreTruth['scoreMonth'] + $scoreDare['scoreMonth']; ?> pts<br />
            Year score: <?php echo $scoreTruth['scoreYear'] + $scoreDare['scoreYear']; ?> pts<br />
            Total score: <?php echo $scoreTruth['scoreTotal'] + $scoreDare['scoreTotal']; ?> pts
        </p>
        <p>
            <u>Truth</u><br />
            Week score: <?php echo $scoreTruth['scoreWeek'];; ?> pts<br />
            Month score: <?php echo $scoreTruth['scoreMonth']; ?> pts<br />
            Year score: <?php echo $scoreTruth['scoreYear']; ?> pts<br />
            Total score: <?php echo $scoreTruth['scoreTotal']; ?> pts
        </p>
        <p>
            <u>Dare</u><br />
            Week score: <?php echo $scoreDare['scoreWeek']; ?> pts<br />
            Month score: <?php echo $scoreDare['scoreMonth']; ?> pts<br />
            Year score: <?php echo $scoreDare['scoreYear']; ?> pts<br />
            Total score: <?php echo $scoreDare['scoreTotal']; ?> pts
        </p>
    </div>
</div>