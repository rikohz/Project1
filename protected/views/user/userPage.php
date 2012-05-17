<script type="text/javascript">
//Progress bar for Truth
$(document).ready(function() {
    $("#progressBarTruth").progressbar({ value: <?php echo MyFunctions::getValueProgressBar($user->scoreTruth->score); ?> });
  });
  
//Progress bar for Dare
$(document).ready(function() {
    $("#progressBarDare").progressbar({ value: <?php echo MyFunctions::getValueProgressBar($user->scoreDare->score); ?> });
  });
  
//Send friend request
function sendFriendRequest()
{
    $.ajax({
        type: "POST",
        url: "index.php?r=user/sendFriendRequest",
        data: "idUser=<?php echo $user->idUser; ?>",
        cache: false,

        success: function(html)
        {
            document.getElementById('dialog-friendRequest').innerHTML = html;
            $('#dialog-friendRequest').dialog('open');
        } 
    })
};

//Dialog box for Friend Request
$(function() {         
    $("#dialog:ui-dialog").dialog( "destroy" );
    $("#dialog-friendRequest").dialog({ autoOpen: false })
    $("#dialog-friendRequest").dialog({
            modal: true,
            buttons: {
                    Ok: function() {
                            $( this ).dialog( "close" );
                    }
            }
    });
});
</script>  

<?php 
  @session_start();  
  
  $this->pageTitle=Yii::app()->name . " - $user->username Page";
  $this->breadcrumbs=array(
	"$user->username Page",
  );
?>

  
<div style="width:150px; display:inline-block;border:1px black solid; vertical-align: top;">
    
    <!--*****************-->
    <!-- Profile Picture -->
    <!--*****************-->
    
    <img style="vertical-align:middle;" src="userImages/profilePicture/<?php echo $user->profilePicture . '_profile' . $user->profilePictureExtension; ?>" />

    <!--*******************-->
    <!-- User Profile Menu -->
    <!--*******************-->
    
    <div style="margin-bottom:15px; margin-top:10px;">
        <a style="display:block;" href="index.php?r=user/myFriends">See Friends</a>      
        <a style="display:block;" href="index.php?r=user/myMessages">Send Message</a>
        <a style="display:block;" href="index.php?r=user/myChallenges">See Challenges</a>
        <a style="display:block;" href="index.php?r=user/myChallenges">Send Challenge</a>
        <a style="display:block;" href="index.php?r=user/myAlbums">See Albums</a>
        <a style="display:block;" href="index.php?r=user/myTruths">See Truths</a>
        <a style="display:block;" href="index.php?r=user/myDares">See Dares</a>
        <a style="display:block;" href="index.php?r=user/myLists">See Lists</a>
        <a style="display:block;" href="#" onClick="sendFriendRequest()">Add Friend</a>
    </div>
    
    <!--*****************-->
    <!-- Display Friends -->
    <!--*****************-->
    
    My Friends
    <?php $this->widget('FriendsOverview',array('idUser'=>$user->idUser)); ?>
</div>


<div style="border:1px black solid; width:750px; display:inline-block;">
    <div style="height:150px; border:1px black solid;">
        
        <!--********************************-->
        <!-- Display User main informations -->
        <!--********************************-->
        <div style="height:150px; width:250px; border:1px black solid; float:left;">
            <div style="font-size:2em; font-weight:bold;"><?php echo $user->username; ?></div>
            <div>
                <?php 
                    echo isset($user->province)? $user->province->name : "";
                    echo isset($user->city)? " - " . $user->city->name : "";
                    echo isset($user->district)? " - " . $user->district->name : "" ; 
                ?>
            </div>
            <div><?php $myFunctions = new MyFunctions(); echo isset($user->birthDate) ? $myFunctions->getAge($user->birthDate) : ''; ?></div>
            <div style="margin-top:10px;"><?php echo MyFunctions::getTruthRankName($user->scoreTruth->score); ?></div>
            <div id="progressBarTruth" style="width:100px; height:10px; margin-bottom:10px;"></div>
            <div><?php echo MyFunctions::getDareRankName($user->scoreDare->score); ?></div>
            <div id="progressBarDare" style="width:100px; height:10px;"></div>
        </div>
        
        <!--****************************-->
        <!-- Display Scores of the User -->
        <!--****************************-->
        <div style="height:150px; width:494px; border:1px black solid; float:right;">
            <div style="float:right; width:450px; height:73px; border:1px black solid;">
                <!-- Display Score Truth -->
                <div style="float:right; width:120px; height:71px; border:1px black solid; font-weight:bold; text-align:center;">
                    <p style="line-height:71px; vertical-align:middle;">Total: <?php echo $score['scoreTruthVoteIdeas']['total'] + $score['scoreTruthChallenges']['total'] + $score['scoreTruthVoteChallenges']['total']; ?>pts</p>
                </div>
                <!-- Display Score Truth Week -->
                <div style="float:right; width:120px; height:71px; border:1px black solid;">
                    <div style="margin: 10px 0 0 8px;">
                        <u>Week</u><br />
                        Idea: <?php echo $score['scoreTruthVoteIdeas']['week']; ?>pts<br />
                        Challenge: <?php echo $score['scoreTruthChallenges']['week'] + $score['scoreTruthVoteChallenges']['week']; ?>pts
                    </div>
                </div>
                <div style="float:right; width:120px; height:71px; border:1px black solid; font-size:4.5em; vertical-align:middle; text-align:center; font-weight:bold;">T</div>
            </div>
            <div style="float:right; width:450px; height:73px; border:1px black solid;">
                <!-- Display Score Dare -->
                <div style="float:right; width:120px; height:71px; border:1px black solid; font-weight:bold; text-align:center;">
                    <p style="line-height:71px; vertical-align:middle;">Total: <?php echo $score['scoreDareVoteIdeas']['total'] + $score['scoreDareChallenges']['total'] + $score['scoreDareVoteChallenges']['total']; ?>pts</p>
                </div>
                <!-- Display Score Dare Week -->
                <div style="float:right; width:120px; height:71px; border:1px black solid;">
                    <div style="margin: 10px 0 0 8px;">
                        <u>Week</u><br />
                        Idea: <?php echo $score['scoreDareVoteIdeas']['week']; ?>pts<br />
                        Challenge: <?php echo $score['scoreDareChallenges']['week'] + $score['scoreDareVoteChallenges']['week']; ?>pts
                    </div>
                </div>
                <div style="float:right; width:120px; height:71px; border:1px black solid; font-size:4.5em; vertical-align:middle; text-align:center; font-weight:bold;">D</div>
            </div>
        </div>
    </div>

    
    <!--******-->
    <!-- Wall -->
    <!--******-->
    <?php $this->widget('UserWallWidget',
            array(
                'idCurrentUser'=>Yii::app()->user->getId(),
                'idWallOwner'=>$user->idUser,
                'filterLevel'=>Yii::app()->user->getLevel(),
                'withVotes'=>1,
                'withFavourites'=>1,
                'withComments'=>1
                )); ?>
            

        <p>See yiinfinite-scroll</p>
    </div>
</div>


<!--***************************-->
<!-- Dialog for Friend Request -->
<!--***************************-->
<div id="dialog-friendRequest" title="Friend Request"></div>