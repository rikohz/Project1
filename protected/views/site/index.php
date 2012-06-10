<?php $this->pageTitle=Yii::app()->name; ?>

<!--*********-->
<!-- RANKING -->
<!--*********-->
<div style="display:inline-block; border:1px black dashed; width:500px;">
    <!-- GENERAL RANKING -->
    <div style="width:140px; display:inline-block; margin-left:5px;">
        <br />
        <p style="text-align:center;font-weight: bold;">GENERAL</p>
        <table width="100%">
        <?php $i=0; foreach ($generalRanking as $row) { ?>
            <tr>
                <td width="1%" style="padding:0;">
                    <?php switch($i)
                    {
                        case "0":
                            echo '<img src="/TruthOrDare/images/goldCrown.png" width="16px" height="16px" />';
                            break;
                        case "1":
                            echo '<img src="/TruthOrDare/images/silverCrown.png" width="16px" height="16px" />';
                            break;
                        case "2":
                            echo '<img src="/TruthOrDare/images/bronzeCrown.png" width="16px" height="16px" />';
                            break;
                    }
                    ?>
                </td>
                <td width="50%"><?php echo $row['username']; ?></td>
                <td width="50%"><?php echo $row['score']; ?></td>
            </tr>
        <?php $i++; } ?>
        </table>
    </div>

    <!-- TRUTH RANKING -->
    <div style="width:140px; display:inline-block; margin-left:30px; margin-right:30px;">
        <br />
        <p style="text-align:center;font-weight: bold;">TRUTH</p>
        <table width="100%">
        <?php 
            $i=0; 
            foreach ($truthRanking as $row) { ?>
            <tr>
                <td width="1%" style="padding:0;">
                    <?php switch($i)
                    {
                        case "0":
                            echo '<img src="/TruthOrDare/images/goldCrown.png" width="16px" height="16px" />';
                            break;
                        case "1":
                            echo '<img src="/TruthOrDare/images/silverCrown.png" width="16px" height="16px" />';
                            break;
                        case "2":
                            echo '<img src="/TruthOrDare/images/bronzeCrown.png" width="16px" height="16px" />';
                            break;
                    }
                    ?>
                </td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['scoreTruth']; ?></td>
            </tr>
        <?php $i++;} ?>
        </table>
    </div>

    <!-- DARE RANKIN -->
    <div style="width:140px; display:inline-block;">
        <br />
        <p style="text-align:center;font-weight: bold;">DARE</p>
        <table width="100%">
        <?php 
            $i=0; 
            foreach ($dareRanking as $row) { ?>
            <tr>
                <td width="1%" style="padding:0;">
                    <?php switch($i)
                    {
                        case "0":
                            echo '<img src="/TruthOrDare/images/goldCrown.png" width="16px" height="16px" />';
                            break;
                        case "1":
                            echo '<img src="/TruthOrDare/images/silverCrown.png" width="16px" height="16px" />';
                            break;
                        case "2":
                            echo '<img src="/TruthOrDare/images/bronzeCrown.png" width="16px" height="16px" />';
                            break;
                    }
                    ?>
                </td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['scoreDare']; ?></td>
            </tr>
        <?php $i++;} ?>
        </table>
    </div>
    <!-- LINK TO ACCESS FULL RANKING PAGE -->
    <div style="text-align:center;margin:10px;"><a href="index.php?r=site/ranking">See the full ranking</a></div>
</div>

<!--*******-->
<!-- EVENT -->
<!--*******-->
<div style="float:right;">
    <img width="400px" src="/TruthOrDare/images/event.jpg" />    
</div>

<br />
<br />




<div style="width:500px;float:left;">
    
    <!--CHALLENGE DARE LIST-->
    <div style="width:500px;text-align:right;color:#FAA;font-size:1.5em;font-weight:bold;font-family:comic sans ms;">LAST 3 CHALLENGES DARE</div>
    <div style="border:1px #CCC solid;width:500px;border-radius:10px;background-color: #FEE;">
        <div style="margin:10px;">
            <?php $this->widget('ChallengeDareList',
                    array(
                        'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
                        'filterLevel'=>Yii::app()->user->getLevel(),
                        'withVotes'=>1,
                        'withFavourites'=>!Yii::app()->user->isGuest,
                        'withComments'=>1,
                        'withSendChallenge'=>!Yii::app()->user->isGuest,
                        'model'=>$searchChallengeDareForm
                        )); ?>
        </div>
    </div>
    
    <br />
    <br />
    
    <!--CHALLENGE TRTUTH LIST-->
    <div style="width:500px;text-align:right;color:#FAA;font-size:1.5em;font-weight:bold;font-family:comic sans ms;">LAST 3 CHALLENGES TRUTH</div>
    <div style="border:1px #CCC solid;width:500px;border-radius:10px;background-color: #FEE;">
        <div style="margin:10px;">
            <?php $this->widget('ChallengeTruthList',
                    array(
                        'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
                        'filterLevel'=>Yii::app()->user->getLevel(),
                        'withVotes'=>1,
                        'withFavourites'=>!Yii::app()->user->isGuest,
                        'withComments'=>1,
                        'withSendChallenge'=>!Yii::app()->user->isGuest,
                        'model'=>$searchChallengeTruthForm
                        )); ?>
        </div>
    </div>
    
</div>

<div style="width:400px;float:right;">
    <!-- LIST 3 MOST RECENT DARES -->
    <div style="width:400px;;text-align:right;color:#AAF;font-size:1.5em;font-weight:bold;font-family:comic sans ms;">LAST 3 DARES</div>
    <div style="border:1px #CCC solid;width:400px;;;border-radius:10px;background-color: #EEF;">
        <div style="margin:10px;">
            <?php 
            $this->widget('DareList',
                    array(
                        'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
                        'filterLevel'=>Yii::app()->user->getLevel(),
                        'withVotes'=>1,
                        'withFavourites'=>!Yii::app()->user->isGuest,
                        'withComments'=>1,
                        'withSendChallenge'=>!Yii::app()->user->isGuest,
                        'model'=>$searchDareForm
                        )); 
            ?>
        </div>
    </div>

    <br />
    <br />

    <!-- LIST 3 MOST RECENT TRUTHS -->
    <div style="width:400px;text-align:right;color:#AAF;font-size:1.5em;font-weight:bold;font-family:comic sans ms;">LAST 3 TRUTHS</div>
    <div style="border:1px #CCC solid;width:400px;border-radius:10px;background-color: #EEF;">
        <div style="margin:10px;">
            <?php $this->widget('TruthList',
                    array(
                        'idUser'=>Yii::app()->user->isGuest ? null : Yii::app()->user->getId(),
                        'filterLevel'=>Yii::app()->user->getLevel(),
                        'withVotes'=>1,
                        'withFavourites'=>!Yii::app()->user->isGuest,
                        'withComments'=>1,
                        'withSendChallenge'=>!Yii::app()->user->isGuest,
                        'model'=>$searchTruthForm
                        )); ?>
        </div>
    </div>
</div>